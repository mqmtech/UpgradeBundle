<?php

namespace MQM\UpgradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MQM\PricingBundle\Model\DiscountRule\DiscountRuleManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;

class UpgradeDiscountDatabaseCommand extends ContainerAwareCommand
{
    /**
    * @var DiscountRuleManagerInterface
    */
    private $discountRuleManager;
    
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
        $this->pricing = $this->getContainer()->get('mqm_pricing.pricing_manager');
        $this->discountRuleManager = $this->pricing->getDiscountRuleManager('MQM\PricingBundle\Entity\DiscountRule\DiscountByProductRule');
        
        $doctrine = $this->discountRuleManager = $this->get('doctrine');
        $em = $doctrine->getEntityManager();
        $repo = $em->getRepository('MQM\ShopBundle\Entity\TimeOffer');
        $offers = $repo->findAll();
        foreach ($offers as $offer) {
            $discount = $offer->getDiscount();
            $start = $offer->getStart();
            $deadline = $offer->getDeadline();
            $name = $offer->getName();
            $description = $offer->getDescription();
            
            $discountRule = $this->discountRuleManager->createDiscountRule();
        }
    }
}