<?php

namespace IVRank\Controller\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use IVRank\Controller\CliController as Controller;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CliController implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Controller
    {
        /** @var EntityManager $em */
        $em = $container->get(EntityManager::class);

        return new Controller($em);
    }
}
