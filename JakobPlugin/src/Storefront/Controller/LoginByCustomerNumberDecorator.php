<?php

namespace JakobPlugin\Storefront\Controller;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\CustomerException;
use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Checkout\Customer\Password\LegacyPasswordVerifier;
use Shopware\Core\Checkout\Customer\SalesChannel\SwitchDefaultAddressRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Context\CartRestorer;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class LoginByCustomerNumberDecorator extends AccountService
{

    public function __construct(private readonly EntityRepository $customerRepository,
                                private readonly EventDispatcherInterface $eventDispatcher,
                                private readonly LegacyPasswordVerifier $legacyPasswordVerifier,
                                private readonly SwitchDefaultAddressRoute $switchDefaultAddressRoute,
                                private readonly CartRestorer $restorer)
    {
        parent::__construct($customerRepository, $eventDispatcher, $legacyPasswordVerifier,$switchDefaultAddressRoute, $restorer );
    }


    public function getCustomerByEmail(string $email, SalesChannelContext $context): CustomerEntity
    {
        $email = strtolower($email);
        if (str_ends_with($email, '-at')) {
            $email = substr($email, 0, -3);
        }
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('customerNumber', $email));
        $customer = $this->fetchCustomer($criteria, $context);

        if ($customer === null) {
            return parent::getCustomerByEmail($email, $context);
        }

        return $customer;
    }

    public function fakeCustomerLogin(string $email, SalesChannelContext $context): string
    {
        if ($email === '') {
            throw CustomerException::badCredentials();
        }

        $customer = $this->getCustomerByEmail($email, $context);
        $context = $this->restorer->restore($customer->getId(), $context);
        $newToken = $context->getToken();
        $event = new CustomerLoginEvent($context, $customer, $newToken);
        $this->eventDispatcher->dispatch($event);

        return $newToken;


    }

    private function fetchCustomer(Criteria $criteria, SalesChannelContext $context, bool $includeGuest = false): ?CustomerEntity
    {
        $criteria->setTitle('account-service::fetchCustomer');

        $result = $this->customerRepository->search($criteria, $context->getContext())->getEntities();
        $result = $result->filter(function (CustomerEntity $customer) use ($includeGuest, $context): ?bool {
            // Skip not active users
            if (!$customer->getActive()) {
                return null;
            }

            // Skip guest if not required
            if (!$includeGuest && $customer->getGuest()) {
                return null;
            }

            // If not bound, we still need to consider it
            if ($customer->getBoundSalesChannelId() === null) {
                return true;
            }

            // It is bound, but not to the current one. Skip it
            if ($customer->getBoundSalesChannelId() !== $context->getSalesChannelId()) {
                return null;
            }

            return true;
        });

        // If there is more than one account we want to return the latest, this is important
        // for guest accounts, real customer accounts should only occur once, otherwise the
        // wrong password will be validated
        if ($result->count() > 1) {
            $result->sort(fn (CustomerEntity $a, CustomerEntity $b) => ($a->getCreatedAt() <=> $b->getCreatedAt()) * -1);
        }

        return $result->first();
    }

}
