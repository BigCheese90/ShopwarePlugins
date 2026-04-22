<?php

namespace JakobPlugin\Service;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;

class PriceImport
{
    private EntityRepository $productRepository;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    private function createPrice($netPrice, $context) : array | null
    {
        if ($netPrice == 0 or $netPrice == "" or $netPrice == null) {
            return null;
        }
        $priceArray = [
            [
                "currencyId" => $context->getCurrencyId(),
                "net" => $netPrice,
                "gross" => $netPrice*1.2,
                "linked" => true,
            ]
        ];
        return $priceArray;
    }
    public function importProductPrice() : void
    {
        $context = Context::createCLIContext();
        $filePath = __DIR__ . '/pricelist.csv';
        $payload = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {

            // Optional: Get the first row (headers) if you want to skip them
            $headers = fgetcsv($handle, 1000, ";");
            $lenHeaders = count($headers);
            print_r($headers);
            // Loop through the remaining rows
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if (count($data) != $lenHeaders) {
                    echo "corrupt data \n";
                    continue;
                }
                // $data is an array containing the values of the current row
                // Example: $data[0] is column 1, $data[1] is column 2, etc.
//                echo "Product Name: " . $data[0] . " | Price: " . $data[1] . "\n";
                $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('productNumber', $data[0]));
                $productEntity = $this->productRepository->search($criteria, $context)->first();
                if (!$productEntity) {continue;}
                $productId = $productEntity->getId();
                $customFields = [];
                for ($i=0; $i<10; $i++){
                    $vk = $i+1;
                    $customFields["custom_product_pricing_VK{$vk}"] = $this->createPrice($data[6+$i], $context);
                }
                $customFields["custom_product_pricing_EK"] = $this->createPrice($data[16], $context);

                $priceInformation = [
                    "id" => $productId,
                    "stock"=>intval($data[5]),
                    "customFields" => $customFields];

                $price = $this->createPrice($data[6], $context);
                if ($price) {
                    $priceInformation["price"] = $price;
                }
                $payload[] = $priceInformation;

            }
//            print_r($payload);
            $this->productRepository->update($payload, $context);

        }
    }


}