<?php

namespace OpenControl\Integration\Model\Utils;

class OrderService
{
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $sortOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|null 
     */
    public function existOrderWithQuoteId(int $id){
        $sortOrder = $this->sortOrder->setField('created_at')->setDirection('ASC')->create();
        $this->searchCriteriaBuilder->addFilter('quote_id', $id);
        $orders = $this->orderRepository->getList(
            $this->searchCriteriaBuilder->setSortOrders([$sortOrder])->create()
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

    public function createCommentHistoryOpenControlDenied($order, $reasons = null, $matchedName) {
        $reasonsMessage = '';
        if ($matchedName === 'matched_list') {
            $reasonsMessage = OpenControlStatus::MESSAGE_DENIED . ' (Rejected by '.$reasons['type'].' list)';
            $order->addCommentToStatusHistory($reasonsMessage);
            return;
        }
        $numberReasons = count($reasons) - 1;
        $index = 0;
        foreach ($reasons as $reason) {
            $reasonsMessage = ($index === $numberReasons) ? $reasonsMessage . $reason['description'] : $reasonsMessage . $reason['description'].',';
            $index++;
        }
        $order->addCommentToStatusHistory(OpenControlStatus::MESSAGE_DENIED .' ('. $reasonsMessage .')');
    }
}