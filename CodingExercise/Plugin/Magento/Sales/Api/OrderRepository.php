<?php

namespace Cricut\CodingExercise\Plugin\Magento\Sales\Api;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

class OrderRepository
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        $this->addCricutId($order);
        return $order;
    }

    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult): OrderSearchResultInterface
    {
        foreach ($searchResult->getItems() as $order) {
            $this->addCricutId($order);
        }
        return $searchResult;
    }

    private function addCricutId(OrderInterface $order): void
    {
        $extensionAttributes = $order->getExtensionAttributes() ?: $this->orderExtensionFactory->create();
        $extensionAttributes->setData('cricut_id', $order->getData('cricut_id'));
        $order->setExtensionAttributes($extensionAttributes);
    }
}
