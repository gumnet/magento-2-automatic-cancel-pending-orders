<?php
/**
 * @author Gustavo Ulyssea - gustavo.ulyssea@gmail.com
 * @copyright Copyright (c) 2020 GumNet (https://gum.net.br)
 * @package GumNet AutomaticCancelPendingOrders
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY GUM Net (https://gum.net.br). AND CONTRIBUTORS
 * ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE FOUNDATION OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace GumNet\AutomaticCancelPendingOrders\Cron;

class CancelPendingOrders
{
    protected $orderCollectionFactory;
    protected $orderFactory;
    protected $orderManagement;
    protected $logger;
    protected $_scopeConfig;


    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;
        $this->_scopeConfig = $scopeConfig;
    }
    public function execute()
    {
        $this->logger->info("Automatic cancel order starting...");
        if(!$this->_scopeConfig->getValue('cancel_pending_orders/general/days_old', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $this->logger->info("Automatic cancel order disabled, stop...");
            return;
        }
        $days_old = $this->_scopeConfig->getValue('cancel_pending_orders/general/days_old', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if(!$days_old) return;
        if($days_old<2) return;
        $to = strtotime("-".$days_old." day", strtotime($days_old));
        $to = date('Y-m-d h:i:s', $to);

        $orders = $this->getOrderCollection("pending",$to);
        foreach($orders as $_order){
            $this->logger->info("Automatic cancel order ID: ".$_order->getId());
            $order = $this->_orderFactory->create()->load($_order->getId());
            $order->cancel()->save();
        }
        $this->logger->info("Automatic cancel order finished.");
    }
    public function getOrderCollection($status,$to)
    {
        $orderCollection = $this->orderCollectionFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq'=> $status])
            ->addFieldToFilter('created_at', array('lt' => $to));

        return $orderCollection;
    }
}
