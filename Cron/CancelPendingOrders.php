<?php
/**
 * @author Gustavo Ulyssea - gustavo.ulyssea@gmail.com
 * @copyright Copyright (c) 2020 - 2024 GumNet (https://gum.net.br)
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

declare(strict_types=1);

namespace GumNet\AutomaticCancelPendingOrders\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class CancelPendingOrders
{
    public const MESSAGE_STARTING = 'Automatic cancel order starting...';
    public const MESSAGE_DISABLED = 'Automatic cancel order disabled, stop...';
    public const MESSAGE_AUTOMATIC_CANCEL = 'Automatic cancel order ID: ';
    public const MESSAGE_FINISHED = 'Automatic cancel order finished.';
    public const CONFIG_ENABLE = 'cancel_pending_orders/general/enable';
    public const CONFIG_DAYS_OLD = 'cancel_pending_orders/general/days_old';
    public const MINIMUM_DAYS_THRESHOLD = 2;
    public const DATE_PATTERN = 'Y-m-d h:i:s';

    /**
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly CollectionFactory $orderCollectionFactory,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly LoggerInterface $logger,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Cron execution
     *
     * @return void
     */
    public function execute(): void
    {
        $this->logger->info(self::MESSAGE_STARTING);

        if (!$this->scopeConfig->getValue(self::CONFIG_ENABLE, ScopeInterface::SCOPE_STORE)) {
            $this->logger->info(self::MESSAGE_DISABLED);
            return;
        }

        $days_old = $this->scopeConfig->getValue(self::CONFIG_DAYS_OLD, ScopeInterface::SCOPE_STORE);

        if (!$days_old || $days_old < self::MINIMUM_DAYS_THRESHOLD) {
            return;
        }

        $to = strtotime("-" . $days_old . " day");
        $to = date(self::DATE_PATTERN, $to);

        $orderCollection = $this->getOrderCollection(Order::STATE_PENDING_PAYMENT, $to);

        foreach ($orderCollection->getItems() as $item) {
            $this->logger->info(self::MESSAGE_AUTOMATIC_CANCEL . $item->getId());
            $order = $this->orderRepository->get($item->getId());
            $order->cancel()->save();
        }

        $this->logger->info(self::MESSAGE_FINISHED);
    }

    /**
     * Get order collection
     *
     * @param string $status
     * @param string $to
     * @return Collection
     */
    public function getOrderCollection(string $status, string $to): Collection
    {
        return $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter(OrderInterface::STATE, ['in' => [Order::STATE_NEW, Order::STATE_PENDING_PAYMENT]])
            ->addFieldToFilter(OrderInterface::CREATED_AT, ['lt' => $to]);
    }
}
