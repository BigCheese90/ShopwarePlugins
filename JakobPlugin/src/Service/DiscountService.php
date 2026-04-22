<?php

namespace JakobPlugin\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class DiscountService {

    private EntityRepository $tagRepository;
    private EntityRepository $customerRepository;

    public function __construct(EntityRepository $tagRepository, EntityRepository $customerRepository) {
        $this->tagRepository = $tagRepository;
        $this->customerRepository = $customerRepository;
}

    public function assignTagToCustomer($customerNumber, $tagName): void {

        $context = Context::createCLIContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $tagName));
        $tag = $this->tagRepository->search($criteria, $context)->first();
        if (!$tag) {
            return;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerNumber', $customerNumber));
        $customer = $this->customerRepository->search($criteria, $context)->first();

        if (!$customer) {
            return;
        }
        $this->customerRepository->update([[
            "id" => $customer->getId(),
            "tags" => [["id" => $tag->getId()]],
        ]], $context);
    }

    public function assignTags(array $tagArray): void {
        forEach($tagArray as $entry) {
            if (count($entry) != 4) {
                continue;
            }
            if ($entry[2] == "Axis (684)") {
                if($entry[1] == "19.5") {
                    $this->assignTagToCustomer($entry[0], "Axis Authorized Partner");
                    echo "Assigned Axis Authorized Partner to " . $entry[0] . "\n";
                }
                if($entry[1] == "24") {
                    $this->assignTagToCustomer($entry[0], "Axis Silver Partner");
                    echo "Assigned Axis Silver Partner to " . $entry[0] . "\n";
                }
                if($entry[1] == "30") {
                    $this->assignTagToCustomer($entry[0], "Axis Gold Partner");
                    echo "Assigned Axis Gold Partner to " . $entry[0] . "\n";
                }
                if($entry[1] == "31") {
                    $this->assignTagToCustomer($entry[0], "Axis Multiregional Partner");
                    echo "Assigned Axis Multiregional Partner to " . $entry[0] . "\n";
                }
            }
        }
    }
}