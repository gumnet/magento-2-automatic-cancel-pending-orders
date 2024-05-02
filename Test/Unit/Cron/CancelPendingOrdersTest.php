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

namespace GumNet\AutomaticCancelPendingOrders\Test\Unit\Cron;

use GumNet\AutomaticCancelPendingOrders\Cron\CancelPendingOrders;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Reports\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CancelPendingOrdersTest extends TestCase
{
    /**
     * @var CollectionFactory|MockObject
     */
    private CollectionFactory|MockObject $orderCollectionFactoryMock;

    /**
     * @var OrderRepository|MockObject
     */
    private OrderRepository|MockObject $orderRepositoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private LoggerInterface|MockObject $loggerMock;

    /**
     * @var Collection|MockObject
     */
    private Collection|MockObject $orderCollectionMock;

    /**
     * @var Order|MockObject|(Order&object&MockObject)|(Order&MockObject)|(object&MockObject)
     */
    private Order|MockObject $orderMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    /**
     * @var CancelPendingOrders
     */
    private CancelPendingOrders $cancelPendingOrders;

    /**
     * @return void
     */
    public function setup(): void
    {
        $this->orderCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->orderRepositoryMock = $this->createMock(OrderRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->orderCollectionMock = $this->createMock(Collection::class);
        $this->orderMock = $this->createMock(Order::class);

        $this->cancelPendingOrders = new CancelPendingOrders(
            $this->orderCollectionFactoryMock,
            $this->orderRepositoryMock,
            $this->loggerMock,
            $this->scopeConfigMock
        );
    }

    /**
     * @return void
     */
    public function testExecute(): void
    {
        $this->loggerMock->expects(self::exactly(3))
            ->method('info');
        $this->scopeConfigMock->expects(self::exactly(2))
            ->method('getValue')
            ->willReturnOnConsecutiveCalls('1', '4');
        $this->prepareGetOrderCollection();
        $this->orderCollectionMock->expects(self::once())
            ->method('getItems')
            ->willReturn([$this->orderMock]);
        $this->orderMock->expects(self::exactly(2))
            ->method('getId')
            ->willReturn(1);
        $this->orderRepositoryMock->expects(self::once())
            ->method('get')
            ->willReturn($this->orderMock);
        $this->orderMock->expects(self::once())
            ->method('cancel')
            ->willReturnSelf();
        $this->orderMock->expects(self::once())
            ->method('save')
            ->willReturnSelf();
        $this->assertNull($this->cancelPendingOrders->execute());
    }

    /**
     * @return void
     */
    public function testExecuteDisabled(): void
    {
        $this->loggerMock->expects(self::exactly(2))
            ->method('info');
        $this->scopeConfigMock->expects(self::once())
            ->method('getValue')
            ->willReturn('0');
        $this->assertNull($this->cancelPendingOrders->execute());
    }

    /**
     * @return void
     */
    public function testExecuteOneDay(): void
    {
        $this->loggerMock->expects(self::exactly(1))
            ->method('info');
        $this->scopeConfigMock->expects(self::exactly(2))
            ->method('getValue')
            ->willReturnOnConsecutiveCalls('1', '1');
        $this->assertNull($this->cancelPendingOrders->execute());
    }

    /**
     * @return void
     */
    public function testGetOrderCollection(): void
    {
        $this->prepareGetOrderCollection();
        $this->assertSame(
            $this->orderCollectionMock,
            $this->cancelPendingOrders->getOrderCollection('pending', '2024-01-01')
        );
    }

    /**
     * @return void
     */
    public function prepareGetOrderCollection(): void
    {
        $this->orderCollectionFactoryMock->expects(self::once())
            ->method('create')
            ->willReturn($this->orderCollectionMock);
        $this->orderCollectionMock->expects(self::exactly(2))
            ->method('addFieldToFilter')
            ->willReturnSelf();
    }
}
