<?php

namespace Cricut\CodingExercise\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Tax\Api\OrderTaxManagementInterface;

/**
 * Note: This class is not appropriate in a production setting.
 * It double loads the order. First using the resource model to load by cricut_id,
 * then passing the entity_id off to the parent's get() method to accomplish
 * the additional work that the original OrderRepository performs.
 *
 * Janky solution for while on short notice short notice.
 *
 */
class OrderRepository extends \Magento\Sales\Model\OrderRepository
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    private $orderResource;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    public function __construct(
        Metadata $metadata,
        \Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor = null,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null,
        OrderTaxManagementInterface $orderTaxManagement = null,
        \Magento\Payment\Api\Data\PaymentAdditionalInfoInterfaceFactory $paymentAdditionalInfoFactory = null,
        JsonSerializer $serializer = null,
        JoinProcessorInterface $extensionAttributesJoinProcessor = null,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
        parent::__construct(
            $metadata,
            $searchResultFactory,
            $collectionProcessor,
            $orderExtensionFactory,
            $orderTaxManagement,
            $paymentAdditionalInfoFactory,
            $serializer,
            $extensionAttributesJoinProcessor
        );
    }

    /**
     * Load entity
     *
     * @param int $cricutId
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($cricutId): OrderInterface
    {
        if (!$cricutId) {
            throw new InputException(__('A "cricut_ id" is needed. Set the "cricut_id" and try again.'));
        }
        $order = $this->orderFactory->create();
        $this->orderResource->load($order, $cricutId, 'cricut_id');
        if (!$order->getId()) {
            throw new NoSuchEntityException(
                __("The entity that was requested doesn't exist. Verify the entity and try again.")
            );
        }
        return parent::get($order->getId());
    }

}