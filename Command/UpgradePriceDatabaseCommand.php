<?php

namespace MQM\UpgradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;

class UpgradePriceDatabaseCommand extends ContainerAwareCommand
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
            ->setName('mqm_upgrade:price')
            ->setDescription('Greet someone')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->productManager = $this->getContainer()->get('mqm_product.product_manager');
        $this->pricing = $this->getContainer()->get('mqm_pricing.pricing_manager');
        $products = $this->productManager->findProducts();
        foreach ($products as $product) {
            $price = $product->getBasePrice();
            $priceRule = $this->pricing->getPriceRuleManager()->createPriceRule();
            $priceRule->setProduct($product);
            $priceRule->setPrice($price);
            $priceRule->setCurrencyCode('EUR');
            $this->pricing->getPriceRuleManager()->savePriceRule($priceRule, false);
        }
        $this->pricing->getPriceRuleManager()->flush();
    }
}