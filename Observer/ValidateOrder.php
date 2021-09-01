<?php
namespace OpenControl\Integration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use OpenControl\Integration\Model\Http\AuthDto;
use OpenControl\Integration\Model\Utils\OpenControlStatus;

class ValidateOrder implements ObserverInterface
{
    const PAYMENT_ERROR = 'Compra fallida. Favor intente con otro método de pago';
    const HTTP_OK = 200;
    const OK_REPONSES = [
        "ACCEPTED"
    ];
    
    protected $logger;
    protected $httpClient;
    protected $openControlConfig;
    protected $orderRepository;
    protected $checkoutSession;
    protected $remoteAddress;
    protected $orderService;
    protected $requestService;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \OpenControl\Integration\Model\Http\HttpClient $httpClient,
        \OpenControl\Integration\Model\OpenControlConfigProvider $openControlConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \OpenControl\Integration\Model\Utils\OrderService $orderService,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \OpenControl\Integration\Model\Utils\RequestValidationService $requestService
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->openControlConfig = $openControlConfig;
        $this->checkoutSession = $checkoutSession;
        $this->remoteAddress = $remoteAddress;
        $this->orderService = $orderService;
        $this->requestService = $requestService;
    }

    public function execute(Observer $observer) {
        $isActiveOpenControl = $this->openControlConfig->getValue('active');
        if (!$isActiveOpenControl) {
            return;
        }

        /** @var \Magento\Quote\Model\Quote  */
        $quote = $this->checkoutSession->getQuote();
        
        $method = $quote->getPayment()->getMethod();
        if (!$this->openControlConfig->isMethodIncludedInConfiguration($method)) {
            return;
        }
        
        $event = $observer->getEvent();

        $order = $event->getData('order');
        if (null === $order) {
            $order = $event->getData('orders')[0];
        }
        $payment = $order->getPayment();
        $orderExists = $this->orderService->existOrderWithQuoteId($quote->getId());

        $ccNumber = $payment->getData('cc_number');
        $ccMonth = $payment->getCcExpMonth();

        //Offline methods or existing card credit
        if($ccNumber == null || $ccMonth == null) {
            $payment->setCcLast4(null);
            $this->logger->debug('#NoCard');
            return $order;
        }
        
        $auth = new AuthDto();
        $auth->user = $this->openControlConfig->getValue('sandbox_license');
        $auth->password = $this->openControlConfig->getValue('sandbox_sk');

        $request = $this->requestService->create($order);

        $url = $this->openControlConfig->getURL().$request::ENDPOINT;
        $response = $this->httpClient->execute($url, $request, $auth, 'POST');
        $body = $response->body;
        
        if ($response->httpCode === self::HTTP_OK && !in_array($body['response'], SELF::OK_REPONSES)) {
            if (!$orderExists) {
                $order->setStatus(OpenControlStatus::DENIED); 
                $this->orderService->createCommentHistoryOpenControlDenied($order, $body['data']['matched_rules']);
                $order->save();
            } else {
                $this->orderService->createCommentHistoryOpenControlDenied($orderExists, $body['data']['matched_rules']);
                $orderExists->save();
            }
            throw new LocalizedException(new Phrase(SELF::PAYMENT_ERROR));
        }

        //Order valid
        if ($response->httpCode === self::HTTP_OK && in_array($body['response'], SELF::OK_REPONSES)) {
            $this->orderService->createCommentHistoryOpenControlApproved($order);
        }
    }
}
