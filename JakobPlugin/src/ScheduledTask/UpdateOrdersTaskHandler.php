<?php

namespace JakobPlugin\ScheduledTask;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use JakobPlugin\Service\OrderHelper;
use JakobPlugin\ScheduledTask\UpdateOrdersTask;

#[AsMessageHandler(handles: UpdateOrdersTask::class)]

class UpdateOrdersTaskHandler extends ScheduledTaskHandler
{

    public function __construct(EntityRepository $scheduledTaskRepository,
                                LoggerInterface $logger,
                                private readonly OrderHelper $orderHelper,
                                private readonly LoggerInterface $monolog)
    {
        parent::__construct($scheduledTaskRepository, $logger);
    }

    public function run(): void
    {
        ob_start();
       try {
           $this->orderHelper->updateAllOrders();
       }
       catch (\Exception $e) {
           $this->monolog->error($e->getMessage());
       }

        $output = ob_get_clean();
        $this->monolog->info($output);
    }
}