<?php

namespace Cricut\CodingExercise\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderRepositoryInterface
{
    /**
     * Load entity
     *
     * @param int $cricutId
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($cricutId): OrderInterface;
}