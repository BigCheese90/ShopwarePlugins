<?php

namespace JakobPlugin\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;
class UpdateOrdersTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'jakob.update_orders';
    }

    public static function getDefaultInterval(): int
    {
        return 3600;
    }
    

}