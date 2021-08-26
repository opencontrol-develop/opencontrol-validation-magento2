<?php

namespace OpenControl\Integration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Phrase;
use OpenControl\Integration\Model\Http\AuthDto;

class ConfigObserver implements ObserverInterface
{
    protected $logger;
    protected $config;
    protected $httpClient;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \OpenControl\Integration\Model\OpenControlConfigProvider $config,
        \OpenControl\Integration\Model\Http\HttpClient $httpClient
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    public function execute(EventObserver $observer) {
        $data = ['session_id'=>'Check_configuration'];
        $url = $this->config->getURL().'/v1/validation';
        
        $auth = new AuthDto();
        $auth->user = $this->config->getValue('sandbox_license');
        $auth->password = $this->config->getValue('sandbox_sk');

        $response = $this->httpClient->execute($url, $data, $auth, 'POST');
        
        if($response->httpCode !== 400){
            throw new ValidatorException(new Phrase('Llave privada o licencia errónea.Por favor corrobore los datos.'));
        }
    }
}