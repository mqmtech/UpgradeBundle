<?php

namespace MQM\UpgradeBundle\Upgrade;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;
use MQM\PaginationBundle\Entity\QueryPagination;

class PriceUpgrade
{
    private $pricing;
    private $productManager;
    private $container;

    /**
     * @var QueryPagination
     */
    private $pagination;

    public function __construct(PricingManagerInterface $pricingManager, ProductManagerInterface $productManager, $container)
    {
        $this->pricing = $pricingManager;
        $this->productManager = $productManager;
        $this->container = $container;
    }

    public function upgradeProductPricesInDB()
    {

        $em = $this->container->get('doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT p FROM MQM\ProductBundle\Entity\Product p');
        $iterableResult = $query->iterate();
        $i = 0;
        $batchSize = 20;
        foreach ($iterableResult as $row) {
            $product = $row[0];
            $this->upgradeProduct($product);
            if (($i % $batchSize) == 0) {
                $em->flush(); // Executes all db changes
                $em->clear(); //Detaches all objects from Doctrine
            }
            ++$i;
        }
    }

    private function upgradeBatchOfProducts($products)
    {
        foreach ($products as $product) {
            $this->upgradeProduct($product);
        }
        $this->pricing->getPriceRuleManager()->flush(); // Executes all db changes
        $this->pricing->getPriceRuleManager()->clear(); //Detaches all objects from Doctrine
    }

    private function upgradeProduct($product)
    {
        $price = $product->getBasePrice();
        $priceRule = $this->pricing->getPriceRuleManager()->createPriceRule();
        $priceRule->setProduct($product);
        $priceRule->setPrice($price);
        $priceRule->setCurrencyCode('EUR');
        $this->pricing->getPriceRuleManager()->savePriceRule($priceRule, false);
    }
}