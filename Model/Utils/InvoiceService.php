<?php

namespace OpenControl\Integration\Model\Utils;

use Magento\Sales\Model\Order\Invoice;

class InvoiceService
{
    public function isInvoicePaid($order) {
         $invoiceCollection = $order->getInvoiceCollection();
         if ($invoiceCollection->count() > 0) {
            foreach ($invoiceCollection as $invoice) {
                if($invoice->getState() == Invoice::STATE_PAID) {
                    return true;
                }
            }
         }
        return false;
    }
}