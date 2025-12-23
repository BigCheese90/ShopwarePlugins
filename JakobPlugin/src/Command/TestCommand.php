<?php declare(strict_types=1);

namespace JakobPlugin\Command;

use JakobPlugin\Service\MentionApi;
use JakobPlugin\Service\OrderHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'testcommand',
    description: 'Add a short description for your command',
)]
class
TestCommand extends Command
{
    // Provides a description, printed out in bin/console
    private MentionApi $api;
    private OrderHelper $orderHelper;


    public function __construct(MentionApi $api, OrderHelper $orderHelper)
    {
        $this->api = $api;
        $this->orderHelper = $orderHelper;
        parent::__construct();

    }
    protected function configure(): void
    {
        $this->setDescription('Does something very special.');
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

//        $order_number = "80744596";
//        $result = $this->api->get_german_data($order_number);
//        print_r($result);
//        $mentionStatus = "";
//        $trackingNumbers = [];
//        foreach ($result as $order) {
//
//            $trackingNumbers[] = $order['tracking_number'];
//            $mentionStatus .= $order['receipt_type'];
//        }
//
//        $this->orderHelper->updateOrderTrackingAndStatus($order_number, $trackingNumbers, $mentionStatus);
//        $output->writeln("it worked");
//        $this->orderHelper->updateOrderShipping("0199a51f35e973709e20193d995af9b0");
//        $this->orderHelper->getOpenOrders();
          #$this->orderHelper->bulkUpdateOrders();
        #$this->orderHelper->createDeliveryFromOrder("019ab696a772738f877f16b0a95be8f3");
//        $this->orderHelper->partialDelivery("019ada63b01671b6a1b9f35fb286b76b");
//        $this->orderHelper->checkOrder("019ada63b01671b6a1b9f35fb286b76b");
        #$this->api->get_german_data("80745139");
        #$this->orderHelper->deliverItems("019acac05f837009895fa0ae5a838a55");
        #$this->api->getOpenPositions("80745730");
//        $this->orderHelper->getOpenOrderInfo("80746239");
//        $this->orderHelper->updateDeliveriesFromMention("80746239");
//        $this->orderHelper->updateOrderStatus("80746239");
        $this->orderHelper->updateAllOrders();
       # print_r($this->api->getAllOpenOrders());
        #print_r($this->api->get_german_data("80745617"));
        #$this->orderHelper->updateOpenPositions("80745730");
        #$this->orderHelper->updateDelivered("80745730");
        #$this->orderHelper->updateDeliveriesFromMention("80745730");
        #echo $this->orderHelper->orderStatus("80745730");
        // Exit code 0 for success
        return 0;
    }


}
