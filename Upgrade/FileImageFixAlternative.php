<?php

namespace MQM\UpgradeBundle\Upgrade;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;
use MQM\PaginationBundle\Entity\QueryPagination;
use MQM\ImageBundle\Model\ImageInterface;

class FileImageFixAlternative
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
            $this->fixImagesWithTheSameName($image);
        }
    }

    private function fixImagesWithTheSameName(ImageInterface $image)
    {
        $this->createTemporalDirIfNotExists();

        $name = $image->getName();
        $absolutePath = $image->getAbsolutePath();
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT i FROM MQM\ImageBundle\Entity\Image i WHERE i.name = '" . $name . "'");
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
        $this->deleteOldImageFile($absolutePath);
    }

    private function fixImage(ImageInterface $image)
    {
        try {
            $this->tryToCloneFile($image);
        }
        catch (\Exception $e) {
            $image->setName(null);
            $image->setType(null);
        }
    }

    private function createTemporalDirIfNotExists()
    {
        if (!file_exists($this->rootWebPath)) {
            mkdir($this->rootWebPath);
        }
    }

    private function tryToCloneFile(ImageInterface $image)
    {
        $clonedName = $image->cloneFile($this->rootWebPath);
        $image->setName($clonedName);
        $image->setType('image/jpeg');
    }

    private function deleteOldImageFile($absolutePath)
    {
        try {
            unlink($absolutePath);
        }
        catch (\Exception $e) {
        }
    }

    private function getEntityManager()
    {
        return $this->container->get('doctrine')->getEntityManager();
    }
}