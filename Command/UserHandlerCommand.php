<?php

namespace MQM\UpgradeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use MQM\ProductBundle\Model\ProductManagerInterface;
use MQM\PricingBundle\Pricing\PricingManagerInterface;

class UserHandlerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mqm_upgrade:set_user')
            ->setDescription('Greet someone')
            ->addArgument('username', InputArgument::REQUIRED, 'Who do you want to add?')
            ->addArgument('password', InputArgument::REQUIRED, 'what password do you want to set?')
            ->addArgument('email', InputArgument::REQUIRED, 'what email do you want to set?')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('username');
        $password= $input->getArgument('password');
        $email = $input->getArgument('email');
        
        $userUpgrade= $this->getContainer()->get('mqm_upgrade.user_upgrade');
        $userUpgrade->restoreUser($name, $password, $email);
    }
}