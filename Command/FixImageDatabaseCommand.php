<?php

namespace MQM\UpgradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;

class FixImageDatabaseCommand extends ContainerAwareCommand
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
            ->setName('mqm_upgrade:fix_image')
            ->setDescription('Greet someone')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $priceUpgrade = $this->getContainer()->get('mqm_upgrade.image_fix');
        $priceUpgrade->fixImageFiles();
    }
}