<?php declare(strict_types=1);

namespace JakobPlugin\Service;


use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Tax\TaxCalculator;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryDefinition;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryStates;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Write\CloneBehavior;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\StateMachine\StateMachineRegistry;
use Shopware\Core\System\StateMachine\Transition;
use JakobPlugin\Service\MentionDelivery;

class OrderHelper

{
    private MentionApi $mentionApi;
    private EntityRepository $orderRepository;
    private EntityRepository $orderLineItemRepository;
    private EntityRepository $deliveryRepository;
    private StateMachineRegistry $stateMachineRegistry;
    private EntityRepository $productRepository;
    private EntityRepository $deliveryPositionRepository;
    private TaxCalculator $taxCalculator;


    public function __construct(MentionApi $mentionApi,
                                EntityRepository $orderRepository,
                                EntityRepository $orderLineItemRepository,
                                EntityRepository $deliveryRepository,
                                StateMachineRegistry $stateMachineRegistry,
                                EntityRepository $productRepository,
                                EntityRepository $deliveryPositionRepository,
                                TaxCalculator $taxCalculator)
    {
        $this->mentionApi = $mentionApi;
        $this->orderRepository = $orderRepository;
        $this->orderLineItemRepository = $orderLineItemRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->stateMachineRegistry = $stateMachineRegistry;
        $this->productRepository = $productRepository;
        $this->deliveryPositionRepository = $deliveryPositionRepository;
        $this->taxCalculator = $taxCalculator;
    }



    public function getOrderIdByOrderNumber(string $orderNumber): ?string
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderNumber', "$orderNumber"));
        return $this->orderRepository->search($criteria, $context)->first()->getId();
    }

    public function updateOrderTrackingAndStatus(string $orderNumber, array $trackingNumbers, string $mentionStatus): void
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderNumber', $orderNumber));
        $order = $this->orderRepository->search($criteria, $context)->first();
        $this->updateOrderEntity($order, $trackingNumbers, $mentionStatus);

    }

        public function getOpenOrders(): array
    {
        $context = Context::createDefaultContext();
        $criteria = (new Criteria())
            ->addAssociation("stateMachineState")
            ->addAssociation("deliveries")
            ->addFilter(new EqualsFilter("stateMachineState.technicalName", OrderStates::STATE_OPEN))
            ->addFilter(new EqualsFilter("primaryOrderDelivery.stateMachineState.technicalName", OrderDeliveryStates::STATE_OPEN));
        $result = $this->orderRepository->search($criteria, $context);
        return $result->getIds();
    }

    public function updateOrderShipping(string $orderId): void
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $orderId));
        $order = $this->orderRepository->search($criteria, $context)->first();
        $orderNumber = $order->getOrderNumber();

        $mentionData = $this->mentionApi->get_german_data($orderNumber);
        $mentionStatus = "";
        $trackingNumbers = "";
        foreach ($mentionData as $entry) {

            $trackingNumbers .= $entry['tracking_number'] . ",";
            $mentionStatus .= $entry['receipt_type'];
        }
        $trackingNumbers = substr($trackingNumbers, 0, -1);
        $trackingNumbers = explode(",", $trackingNumbers);
        if ($mentionStatus === "") {
            echo "Could not update order number: " . $orderNumber . "\n";
            return;
        }
        $this->updateOrderEntity($order, $trackingNumbers, $mentionStatus);
        echo "Updated order number: " . $orderNumber . " with " . $mentionStatus . "\n";
    }

    public function updateOrderEntity($order, array $trackingNumbers, string $mentionStatus): void
    {
        $context = Context::createDefaultContext();
        $deliveryId = $order->getPrimaryOrderDeliveryId();
        $this->orderRepository->update([
            [
                "id" => $order->getId(),
                "customFields" => ["custom_order_mention_status" => $mentionStatus]
            ]
        ], $context);
        $this->deliveryRepository->update([
            [
                "id" => $deliveryId,
                "trackingCodes" => $trackingNumbers,
            ]], $context);

        if ($mentionStatus === 'F') {
            $this->stateMachineRegistry->transition(new Transition(
                OrderDeliveryDefinition::ENTITY_NAME,
                $deliveryId,
                "ship",
                "stateId"
            ), $context);
        }

    }
    public function bulkUpdateOrders(): void
    {
        $openOrders = $this->getOpenOrders();
        foreach ($openOrders as $openOrderId) {
            echo $openOrderId . "\n";
            $this->updateOrderShipping($openOrderId);
        }
    }

    public function cloneDelivery(string $orderId): string
    {
        $context = Context::createCLIContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $orderId));
        $order = $this->orderRepository->search($criteria, $context)->first();
        $deliveryId = $order->getPrimaryOrderDeliveryId();
        $cloneBehavior = new CloneBehavior(["customFields" => ["Cloned" => "YES"]]);
        $newDeliveryId ??= Uuid::randomHex();
        $this->deliveryRepository->clone($deliveryId, $context, $newDeliveryId, $cloneBehavior);

        return $newDeliveryId;

    }

    public function createDeliveryFromOrder(string $orderId, array $deliveredContent = [], ?string $newDeliveryId = null, ?array $trackingCodes = null): string
    {
        $context = Context::createCLIContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $orderId));
        $criteria->addAssociation("primaryOrderDelivery");
        $order = $this->orderRepository->search($criteria, $context)->first();

        $newDeliveryId ??= Uuid::randomHex();
        $primaryDelivery = $order->getPrimaryOrderDelivery();
        $deliveryPayload = [
            "id" => $newDeliveryId,
            "orderId" => $order->getId(),
            "shippingOrderAddressId" => $primaryDelivery->getShippingOrderAddressId(),
            "shippingMethodId" => $primaryDelivery->getShippingMethodId(),
            "stateId" => $primaryDelivery->getStateId(),
            "shippingDateEarliest" => (new \DateTimeImmutable())->modify('+1 days')->format(\DATE_ATOM),
            "shippingDateLatest" => (new \DateTimeImmutable())->modify('+2 days')->format(\DATE_ATOM),
            "shippingCosts" => $primaryDelivery->getShippingCosts(),
            "trackingCodes" => $trackingCodes,
            "customFields" => ["deliveredContent" => $deliveredContent]
        ];

        $this->deliveryRepository->create([$deliveryPayload], $context);

        return $newDeliveryId;

    }


    public function getProductNumberFromId(string $productId, $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $productId));
        return $this->productRepository->search($criteria, $context)->first()->getProductNumber();
    }

    public function updatePriceQuantity(CalculatedPrice $priceInformation, int $quantity): CalculatedPrice
    {
        $totalPrice = $priceInformation->getUnitPrice() * $quantity;
        $calculatedTaxes = $this->taxCalculator->calculateNetTaxes($totalPrice, $priceInformation->getTaxRules());

        return new CalculatedPrice(
            $priceInformation->getUnitPrice(),
            $totalPrice,
            $calculatedTaxes,
            $priceInformation->getTaxRules(),
            $quantity,
            $priceInformation->getReferencePrice(),
            $priceInformation->getListPrice(),
            $priceInformation->getRegulationPrice()
        );
    }

    public function checkOrder(string $orderId): void
    {
        $context = Context::createCLIContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $orderId));
        $criteria->addAssociation("primaryOrderDelivery");
        $criteria->addAssociation("deliveries");
        $order = $this->orderRepository->search($criteria, $context)->first();
        echo  "PrimaryOrderDeliveryID: " . $order->getPrimaryOrderDeliveryId() . "\n";

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderId', $orderId));
        $criteria->addAssociation("positions");
        $deliveries = $this->deliveryRepository->search($criteria, $context);
        foreach($deliveries as $delivery) {
            echo "Delivery ID: " . $delivery->getId() . "\n";
            $positions = $delivery->getPositions();
            foreach ($positions as $position) {
                echo "Position: " . $position->getQuantity() . ">" . $position->getId() . "\n";
            }
        }


    }
    public function getOpenOrderInfo(OrderEntity $order, array $openOrdersMention, Context $context): void
    {

        $orderNumber = $order->getOrderNumber();
        $mentionData = $this->mentionApi->getOpenPositions($orderNumber, $openOrdersMention);
        $in_progress = $this->mentionApi->checkIfOrderInProgress($orderNumber, $openOrdersMention);
        if ($in_progress) {
            $this->stateMachineRegistry->transition(new Transition(
                OrderDefinition::ENTITY_NAME,
                $order->getId(),
                'process',
                'stateId'
            ), $context);
        }

        $lineItems = $order->getLineItems();

        forEach($lineItems as $lineItem) {
            $productNumber = $this->getProductNumberFromId($lineItem->getProductId(), $context);
            echo $productNumber    . "\n";
            $positionInfo = $mentionData[$productNumber] ?? "Not Found";
            print_r($positionInfo ?? "Not Found" . "\n");
            $this->orderLineItemRepository->update([[
                "id" => $lineItem->getId(),
                "customFields" => ["openOrderInformation" => $positionInfo],
            ]], $context);
        }






    }

    public function updateDeliveriesFromMention(OrderEntity $order, $context): void {


        $allDelivered = $this->mentionApi->get_german_data($order->getOrderNumber());

        forEach($allDelivered as $delivery) {
            $this->updateSingleDeliveryFromMention($delivery, $order, $context);
        }

    }

    public function updateSingleDeliveryFromMention(array $delivery, $order, Context $context): void {


        $mentionDelivery = new MentionDelivery($delivery);
        $newDeliveryId = Uuid::fromStringToHex($mentionDelivery->getReceiptNumber());
        if ($this->deliveryRepository->searchIds(new Criteria([$newDeliveryId]), $context)->getTotal() > 0) {
            echo "Delivery already exists \n";
            return;
        };

        $delivery = $this->prepareDelivery($order, $mentionDelivery->getPositions(), $context);

        $deliveryInformation = array_map(function ($array) {
            return $array["delivered"];
        }, $delivery);

        $this->createDeliveryFromOrder($order->getId(), $deliveryInformation, $newDeliveryId, $mentionDelivery->getTrackingNumber());

        $this->updateDeliveryInDatabase($delivery, $newDeliveryId, $order->getPrimaryOrderDeliveryId(), $context);

        $this->stateMachineRegistry->transition(new Transition(
            OrderDeliveryDefinition::ENTITY_NAME,
            $newDeliveryId,
            "ship",
            "stateId"
        ), $context);
        $this->stateMachineRegistry->transition(new Transition(
            OrderDefinition::ENTITY_NAME,
            $order->getId(),
            'process',
            'stateId'
        ), $context);

    }

    public function updateDeliveryInDatabase(array $delivery, string $newDeliveryId, string $primaryDeliveryId, $context): void {


        foreach($delivery as $lineItem) {

            $criteria = new Criteria;
            $criteria->addFilter(new EqualsFilter('orderDeliveryId', $primaryDeliveryId));
            $criteria->addFilter(new EqualsFilter("orderLineItemId", $lineItem["orderLineItemId"]));
            $deliveryPosition = $this->deliveryPositionRepository->search($criteria, $context)->first();

            if ($deliveryPosition == null) {
                continue;
            }
            $remainingQuantity = $deliveryPosition->getPrice()->getQuantity();
            $priceInformation = $lineItem["priceInformation"];

            if ($deliveryPosition == null) {
                echo "Not Found" . "\n";
                continue;
            }


            echo $deliveryPosition->getQuantity() . "\n";
            $deliveryPositionId = $deliveryPosition->getId();
            if($lineItem["delivered"] == 0) {
                echo "No deliveries" . "\n";
                continue;
            }
            if($remainingQuantity <= $lineItem["delivered"])
            {
                echo "Full Delivery" . "\n";
                $this->deliveryPositionRepository->update([[
                    "id" => $deliveryPositionId,
                    "orderDeliveryId" => $newDeliveryId,
                    "customFields" => ["Delivery" => "Full"]
                ]], $context);

            }

            if($remainingQuantity > $lineItem["delivered"]) {
                $deliveredPrice = $this->updatePriceQuantity($priceInformation, $lineItem["delivered"]);
                $remainingPrice = $this->updatePriceQuantity($priceInformation, $remainingQuantity - $lineItem["delivered"]);
                $cloneBehavior = new CloneBehavior(["customFields" => ["quantity" => $remainingQuantity - $lineItem["delivered"]],
                    "price" => $remainingPrice]);
                echo "Partly Delivered" . $lineItem["delivered"] . "\n";

                $this->deliveryPositionRepository->clone($deliveryPositionId, $context, null, $cloneBehavior );

                $this->deliveryPositionRepository->update([[
                    "id" => $deliveryPositionId,
                    "orderDeliveryId" => $newDeliveryId,
                    "price" => $deliveredPrice,
                    "customFields" => ["Delivery" => "Partly"],
                ]], $context);
            }
        }


    }


    public function prepareDelivery($order, $mentionPositions, $context): array {


        $orderLineItems = $order->getLineItems();

        $delivery = [];


        foreach ($orderLineItems as $lineItem) {

            $id = $lineItem->getId();
            $productId = $lineItem->getProductId();
            $productNumber = $this->getProductNumberFromId($productId, $context);
            $delivered = $mentionPositions[$productNumber]["quantity"] ?? 0;
            if ($delivered == 0) {
                continue;
            }
            $serials = $mentionPositions[$productNumber]["serials"] ?? [];

            $customFields = $lineItem->getCustomFields();
            $alreadyDelivered = $customFields["delivered"] ?? 0;
            $alreadySerials = $customFields["serials"] ?? [];
            $serials = array_merge($alreadySerials, $serials);
            $totalDelivered = is_int($alreadyDelivered) ? ($alreadyDelivered + $delivered) : $delivered;
            if ($delivered > 0) {
                $this->orderLineItemRepository->update([["id" => $id, "customFields" =>
                    ["delivered" => $totalDelivered,
                        "serials" => $serials]]],$context);
            }
            $price = $lineItem->getPrice();

            $delivery[$productNumber] = [
                "orderLineItemId" => $id,
                "delivered" => $delivered,
                "priceInformation" => $price];
        }

        return $delivery;
    }

    public function orderStatus(OrderEntity $order, Context $context): string {


        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter("orderId", $order->getId()));
        $lineItems = $this->orderLineItemRepository->search($criteria, $context)->getEntities();

        $orderStatus = "empty";
        forEach($lineItems as $lineItem) {
            $delivered = $lineItem->getCustomFields()["delivered"] ?? 0;
            if ($delivered > 0) {
                $orderStatus = "full";
            }
            if ($lineItem->getQuantity() > $delivered && $orderStatus === "full") {
                echo "OrderStatus is partial" . "\n";
                return "partial";
            }
            echo $lineItem->getQuantity() . " " . $delivered . "\n";
        }
        echo "OrderStatus is " . $orderStatus . "\n";
        return $orderStatus;


    }


    public function updateOrderStatus(OrderEntity $order, Context $context): void
    {

        $orderStatus = $this->orderStatus($order, $context);
        echo $orderStatus . "\n";
        if ($orderStatus == "full") {
            echo "Order Transitioning to full \n";
            $this->stateMachineRegistry->transition(new Transition(
                OrderDeliveryDefinition::ENTITY_NAME,
                $order->getPrimaryOrderDeliveryId(),
                "ship",
                "stateId"
            ), $context);
            $this->stateMachineRegistry->transition(new Transition(
                OrderDefinition::ENTITY_NAME,
                $order->getId(),
                'complete',
                'stateId'
            ), $context);

        }

        if ($orderStatus == "partial") {
            $this->stateMachineRegistry->transition(new Transition(
                OrderDeliveryDefinition::ENTITY_NAME,
                $order->getPrimaryOrderDeliveryId(),
                "ship_partially",
                "stateId"
            ), $context);
            $this->stateMachineRegistry->transition(new Transition(
                OrderDefinition::ENTITY_NAME,
                $order->getId(),
                'process',
                'stateId'
            ), $context);

        }
    }

    public function getOrdersInProcess($context): OrderCollection {


        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('stateMachineState.technicalName', ['in_progress', 'open']));
        $criteria->addAssociation("lineItems");
        /** @var OrderCollection $orders */
        $orders = $this->orderRepository->search($criteria, $context)->getEntities();
        return $orders;

    }

    public function updateAllOrders(): void {
        $context = Context::createCLIContext();
        $orders = $this->getOrdersInProcess($context);
        $openOrdersMention = $this->mentionApi->getAllOpenOrders();
        foreach ($orders as $order) {
            echo $order->getOrderNumber() . "\n";
            $this->getOpenOrderInfo($order, $openOrdersMention, $context);
            $this->updateDeliveriesFromMention($order, $context);
            $this->updateOrderStatus($order, $context);
        }
    }

    public function updateSpecificOrder(string $orderNumber): void {
        $context = Context::createCLIContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('orderNumber', $orderNumber));
        $criteria->addAssociation("lineItems");

        /** @var OrderEntity $order */
        $order = $this->orderRepository->search($criteria, $context)->first();

        $openOrdersMention = $this->mentionApi->getAllOpenOrders();
        $this->getOpenOrderInfo($order, $openOrdersMention, $context);
        $this->updateDeliveriesFromMention($order, $context);
        $this->updateOrderStatus($order, $context);
    }

}


