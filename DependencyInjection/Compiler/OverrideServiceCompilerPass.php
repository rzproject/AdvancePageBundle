<?php

namespace Rz\AdvancePageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        #####################################
        ## Consumer Class
        #####################################
        if ($container->hasParameter('rz_advance_page.consumer.create_snapshots.class')) {
            $definition = $container->getDefinition('sonata.page.notification.create_snapshots');
            $definition->setClass($container->getParameter('rz_advance_page.consumer.create_snapshots.class'));
        }

        if ($container->hasParameter('rz_advance_page.consumer.create_snapshot.class')) {
            $definition = $container->getDefinition('sonata.page.notification.create_snapshot');
            $definition->setClass($container->getParameter('rz_advance_page.consumer.create_snapshot.class'));
            $definition->addMethodCall('setContainer', array(new Reference('service_container')));
        }

        if ($container->hasParameter('rz_advance_page.consumer.cleanup_snapshots.class')) {
            $definition = $container->getDefinition('sonata.page.notification.cleanup_snapshots');
            $definition->setClass($container->getParameter('rz_advance_page.consumer.cleanup_snapshots.class'));
        }

        if ($container->hasParameter('rz_advance_page.consumer.cleanup_snapshot.class')) {
            $definition = $container->getDefinition('sonata.page.notification.cleanup_snapshot');
            $definition->setClass($container->getParameter('rz_advance_page.consumer.cleanup_snapshot.class'));
        }
    }
}
