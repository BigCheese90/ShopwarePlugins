<?php declare(strict_types=1);

namespace AdminTest\Command;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;

#[AsCommand(
    name: 'swag-commands:example',
    description: 'Add a short description for your command',
)]
class ExampleCommand extends Command
{
    // Provides a description, printed out in bin/console
    private EntityRepository $manufacturerDiscountRepository;

    public function __construct(EntityRepository $manufacturerDiscountRepository)
    {
        $this->manufacturerDiscountRepository = $manufacturerDiscountRepository;
        parent::__construct();

    }
    protected function configure(): void
    {
        $this->setDescription('Does something very special.');
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $discounts = $this->manufacturerDiscountRepository->search(new Criteria(), $context)->getEntities();
        print_r($context->getCustomer());
        $list = [];
        foreach ($discounts as $discount) {
            $list[] = [$discount->get("id"), $discount->get("manufacturerId")];
    }
        print_r($list);
        $output->writeln("it worked");

        // Exit code 0 for success
        return 0;
    }
}
