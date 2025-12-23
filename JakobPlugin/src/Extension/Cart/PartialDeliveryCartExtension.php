<?php


namespace JakobPlugin\Extension\Cart;

use Shopware\Core\Framework\Struct\Struct;

class PartialDeliveryCartExtension extends Struct
{
protected bool $partialDelivery;

public function __construct(bool $partialDelivery = false)
{
$this->partialDelivery = $partialDelivery;
}

public function getPartialDelivery(): bool
{
return $this->partialDelivery;
}

public function setPartialDelivery(bool $partialDelivery): void
{
$this->partialDelivery = $partialDelivery;
}
}