<?php

namespace JakobPlugin\Service;

use Shopware\Core\Framework\Struct\Struct;

class MentionDeliveryPosition extends Struct
{
    protected string $articleNumber;
    protected string $quantity;
    protected array $serials;


    public function __construct( array $position ){
        $this->articleNumber = $position['article_number'];
        $this->quantity = $position['quantity'];
        $this->serials = $position['serials'];
    }

}