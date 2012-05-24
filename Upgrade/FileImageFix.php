<?php

namespace MQM\UpgradeBundle\Upgrade;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;
use MQM\PaginationBundle\Entity\QueryPagination;
use MQM\ImageBundle\Model\ImageInterface;

class FileImageFix
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
        $em = $this->container->get('doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT i FROM MQM\ImageBundle\Entity\Image i');
        $iterableResult = $query->iterate();
        $i = 0;
        $batchSize = 20;
        foreach ($iterableResult as $row) {
            $image = $row[0];
            $this->fixImage($image);
            if (($i % $batchSize) == 0) {
                $em->flush(); // Executes all db changes
                $em->clear(); //Detaches all objects from Doctrine
            }
            ++$i;
        }
        $em->flush(); // Executes all db changes
        $em->clear(); //Detaches all objects from Doctrine
    }

    private function fixImage(ImageInterface $image)
    {
        try {
            $this->tryToCloneFile($image);
        }
        catch (\Exception $e) {

        }

    }

    private function tryToCloneFile(ImageInterface $image)
    {
        if (!file_exists($this->rootWebPath)) {
            $this->createDir($this->rootWebPath);
        }

        $clonedName = $image->cloneFile($this->rootWebPath);
        //$image->deleteFile();
        $image->setName($clonedName);
        $image->setType('image/jpeg');
    }

    private function createDir()
    {

    }
}