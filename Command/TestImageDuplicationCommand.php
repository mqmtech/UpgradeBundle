<?php

namespace MQM\UpgradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;

class TestImageDuplicationCommand extends ContainerAwareCommand
{
    /**
    * @var ProductManagerInterface
    */
    private $productManager;
    
    /**
     *
     * @var PricingManagerInterface
     */
    private $pricing;    
    
    protected function configure()
    {
        $this
            ->setName('mqm_upgrade:test_image_duplication')
            ->setDescription('Greet someone')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('init');
        $priceUpgrade = $this->getContainer()->get('mqm_upgrade.image_test_object_duplication');
        $result = $priceUpgrade->fixImageFiles();
        if ($result != null) {
            $output->writeln('error');
            foreach ($result as $product) {
                $output->writeln('product ' . $product->getName() . " with id: " . $product->getId() .
                ", with imageId: " . $product->getImage()->getId());
            }
        }
    }
}