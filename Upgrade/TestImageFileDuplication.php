<?php

namespace MQM\UpgradeBundle\Upgrade;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;
use MQM\PaginationBundle\Entity\QueryPagination;
use MQM\ProductBundle\Model\ProductInterface;
use MQM\ImageBundle\Model\ImageInterface;

class TestImageFileDuplication
{
    private $container;
    private $kernel;
    private $rootWebPath;

    public function __construct($container)
    {
        $this->container = $container;
        $this->kernel = $this->container->get('kernel');
        $rootDir = $this->kernel->getRootDir();
        $this->rootWebPath = $rootDir . '/../web/uploads/images_bkp';
    }

    public function fixImageFiles()
    {

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT i FROM MQM\ImageBundle\Entity\Image i');
        $iterableResult = $query->iterate();
        foreach ($iterableResult as $row) {
            $image = $row[0];
            $result = $this->fixImagesWithTheSameName($image);
            if (count($result) > 1) {
                return $result;
            }
        }

        return null;
    }

    private function fixImagesWithTheSameName(ImageInterface $image)
    {
        $name = $image->getName();
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT i FROM MQM\ImageBundle\Entity\Image i WHERE i.name = '" . $name . "'");
        return $query->getResult();
    }

    private function getEntityManager()
    {
        return $this->container->get('doctrine')->getEntityManager();
    }

}