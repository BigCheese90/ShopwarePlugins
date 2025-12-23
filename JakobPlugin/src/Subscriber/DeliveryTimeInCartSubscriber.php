<?php

namespace JakobPlugin\Subscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemAddedEvent;

class DeliveryTimeInCartSubscriber implements EventSubscriberInterface
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
            AfterLineItemAddedEvent::class => 'onLineItemAdded',
        ];
    }
    public function onLineItemAdded(AfterLineItemAddedEvent $event): void
    {
        $lineItems = $event->getLineItems();
        $this->logger->debug('onLineItemAdded', $lineItems);
    }
}