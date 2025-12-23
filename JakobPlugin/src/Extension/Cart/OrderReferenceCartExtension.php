<?php


namespace JakobPlugin\Extension\Cart;

use Shopware\Core\Framework\Struct\Struct;

class OrderReferenceCartExtension extends Struct
{
    protected string $orderReference;

    public function __construct(string $orderReference)
    {
        $this->orderReference = $orderReference;
    }

    public function getOrderReference(): string
    {
        return $this->orderReference;
    }

    public function setOrderReference(string $orderReference): void
    {
        $this->orderReference = $orderReference;
    }
}