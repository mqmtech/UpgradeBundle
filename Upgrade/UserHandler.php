<?php

namespace MQM\UpgradeBundle\Upgrade;

use MQM\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use MQM\UserBundle\Model\UserInterface;

class UserHandler
{
    private $userManager;
    private $encoderFactory;
    /**
     * @var UserInterface
     */
    private $user;
    private $username;
    private $password;
    private $email;

    public function __construct(UserManagerInterface $userManager, EncoderFactoryInterface $encoderFactory)
    {
        $this->userManager = $userManager;
        $this->encoderFactory = $encoderFactory;
    }

    public function restoreUser($username, $password, $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        
        $this->user = $this->userManager->findUserByUsername($this->username);
        if ($this->user == null) {
            $this->user = $this->userManager->createUser();
        }        
        $this->setDefaultAdminUser();
        $this->userManager->saveUser($this->user);        
    }
    
    private function setDefaultAdminUser()
    {
        $this->user->setUsername($this->username);
        $this->user->setPassword($this->password);
        $this->encodeUserPassword();
        $this->user->setEmail($this->email);
        $this->user->setPermissionType(UserInterface::ROLE_ADMIN);
        $this->user->setIsEnabled(true);
        
        return $this->user;
    }
    
    private function encodeUserPassword()
    {
        $encoder = $this->encoderFactory->getEncoder($this->user);
        $password = $encoder->encodePassword($this->user->getPassword(), $this->user->getSalt());
        $this->user->setPassword($password);

        return $password;
    }
}