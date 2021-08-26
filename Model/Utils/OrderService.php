<?php

namespace OpenControl\Integration\Model\Utils;

class OrderService
{
    protected $orderRepository;
    protected $searchCriteriaBuilder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }
    
    
    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|null 
     */
    public function existOrderWithQuoteId(int $id){
        $this->searchCriteriaBuilder->addFilter('quote_id', $id);
        $orders = $this->orderRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        if (count($orders) > 0) {
            $arrayKeys = array_keys($orders);
            return $orders[$arrayKeys[0]];
        }
        return null;
    }

    public function copyCommentsHistory($fromOrder, $toOrder){
        $histories = $fromOrder->getStatusHistories();
        foreach ($histories as $history) {
            $toOrder->addCommentToStatusHistory($history->getComment())
                ->setCreatedAt($history->getCreatedAt())
                ->setStatus($history->getStatus());
        }
    }

    public function createCommentHistoryOpenControlApproved($order) {
        $order->addCommentToStatusHistory(OpenControlStatus::MESSAGE_APPROVED)
            ->setStatus(OpenControlStatus::APPROVED);
    }

    public function createCommentHistoryOpenControlDenied($order, $reasons = null) {
        $reasonsMessage = '';
        $numberReasons = count($reasons) - 1;
        $index = 0;
        foreach ($reasons as $reason) {
            $reasonsMessage = ($index === $numberReasons) ? $reasonsMessage . $reason['description'] : $reasonsMessage . $reason['description'].',';
            $index++;
        }
        $order->addCommentToStatusHistory(OpenControlStatus::MESSAGE_DENIED .' ('. $reasonsMessage .')');
    }
}