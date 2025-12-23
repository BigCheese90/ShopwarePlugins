<?php

namespace JakobPlugin\Service;

use Shopware\Core\Framework\Struct\Struct;

class MentionDelivery extends Struct
{
    protected string $receiptNumber;
    protected string $receiptType;
    protected string $orderNumber;
    protected array $trackingNumber;
    protected array $positions;

    public function __construct( array $data ){
        $this->receiptNumber = $data['receipt_number'];
        $this->receiptType = $data['receipt_type'];
        $this->orderNumber = $data['receipt_number'];
        $this->trackingNumber = explode(",", $data['tracking_number']);
        $this->positions = $this->positionNormalizer($data['articles']);
    }

    public function positionNormalizer (array $positions): array {
        $result = [];
        foreach ($positions as $position) {
            $articleNumber = $position['article_number'];
            $serials = $position["serials"];
            $serials = $serials==="" ? [] : explode(", ", $serials);
            if (!array_key_exists($articleNumber, $result)) {
                $result[$articleNumber]["quantity"] = $position['quantity'];
                $result[$articleNumber]["serials"] = $serials;
            }
            else {
                $result[$articleNumber]["quantity"] += $position['quantity'];
                $result[$articleNumber]["serials"] = array_merge($result[$articleNumber]["serials"], $serials);
            }

        }
        return $result;
    }


    public function getReceiptNumber(): string {
        return $this->receiptNumber;
    }
    public function getReceiptType(): string {
        return $this->receiptType;
    }
    public function getOrderNumber(): string {
        return $this->orderNumber;
    }
    public function getTrackingNumber(): array {
        return $this->trackingNumber;
    }
    public function getPositions(): array {
        return $this->positions;
    }

}