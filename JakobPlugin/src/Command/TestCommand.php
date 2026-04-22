<?php declare(strict_types=1);

namespace JakobPlugin\Command;

use JakobPlugin\Service\DiscountService;
use JakobPlugin\Service\MentionApi;
use JakobPlugin\Service\OrderHelper;
use JakobPlugin\Service\PriceImport;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
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
    private DiscountService $discountService;
    private PriceImport $priceImport;


    public function __construct(MentionApi $api, OrderHelper $orderHelper,
    DiscountService $discountService, PriceImport $priceImport)
    {
        $this->api = $api;
        $this->orderHelper = $orderHelper;
        $this->discountService = $discountService;
        $this->priceImport = $priceImport;
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
//        $this->orderHelper->updateAllOrders();
//        $this->api->getBackendPriceData();
//        $discounts = $this->api->getBackendPriceHtml();
//        $this->discountService->assignTags($discounts);
        $this->priceImport->importProductPrice();
       // $this->discountService->assignTagToCustomer("237853-at", "AXIS Gold");

        // Exit code 0 for success
        return 0;
    }


}
