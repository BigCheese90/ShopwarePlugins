<?php declare(strict_types=1);

namespace JakobPlugin\Subscriber;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class PartialDeliverySubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public static function getSubscribedEvents(): array
    {
        // Return the events to listen to as array like this:  <event to listen to> => <method to execute>
        return [
            CartConvertedEvent::class => 'onOrderPlaced',
        ];
    }

    public function onOrderPlaced(CartconvertedEvent $event): void
    {
        $this->logger->debug('onOrderPlaced', ["CartconvertedEvent" => "Started"]);
        $deliveryType = "complete_delivery";
        $cart = $event->getCart();
        $cartExtension = $cart->getExtension('Teillieferung');
        $orderExtension = $cart->getExtension("orderReference");
        if ($cartExtension !== null) {
            $this->logger->debug('Extension', ["Extension" => $cartExtension]);
            if ($cartExtension->getPartialDelivery())
            {
                $deliveryType = "partial_delivery";
            }
        }


        $convertedData = $event->getConvertedCart();
        $customFields = $convertedData['customFields'] ?? [];
        $customFields['custom_order_partial_delivery'] = $deliveryType;
        if ($orderExtension !== null)
        {
            $customFields["custom_order_reference"] = $orderExtension->getOrderReference();

        }

        $convertedData['customFields'] = $customFields;
        $event->setConvertedCart($convertedData);
        $this->logger->debug('convertedData', ["deliveryType" => $deliveryType]);

    }
}