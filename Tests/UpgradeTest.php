<?php

namespace MQM\ToolsBundle\Test;

use MQM\ProductBundle\Model\ProductManagerInterface;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\AppKernel;

class UpgradeTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{   
    protected $_container;

    public function __construct()
    {
        parent::__construct();
        
        $client = static::createClient();
        $container = $client->getContainer();
        $this->_container = $container;  
    }
    
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    protected function get($service)
    {
        return $this->_container->get($service);
    }
    
    public function testProductAndPricingManagerDependenciesNotNull()
    {
        $productManager = $this->productManager = $this->get('mqm_product.product_manager');
        $this->assertNotNull($productManager);
        $pricingManager = $this->pricingManager = $this->get('mqm_pricing.pricing_manager');
        $this->assertNotNull($pricingManager);
    }
}
