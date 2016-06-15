<?php

namespace Rz\AdvancePageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RzAdvancePageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('processor.xml');
        $this->configureClass($config, $container);
        $this->configureSearchSettings($config['settings']['search'], $container);
        $this->configureConsumerClass($config, $container);

    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz_advance_page.processor.model.news_page.class', $config['class']['processor']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureSearchSettings($config, ContainerBuilder $container)
    {
        $container->setParameter('rz_advance_page.settings.search.processor.service', $config['processor']['service']);
        $container->setParameter('rz_advance_page.settings.search.config.identifier', $config['config']['identifier']);
    }

    public function configureConsumerClass($config, ContainerBuilder $container)
    {
        if(isset($config['consumer_class']['create_snapshots'])) {
            $container->setParameter('rz_advance_page.consumer.create_snapshots.class', $config['consumer_class']['create_snapshots']);
        }

        if(isset($config['consumer_class']['create_snapshot'])) {
            $container->setParameter('rz_advance_page.consumer.create_snapshot.class', $config['consumer_class']['create_snapshot']);
        }

        if(isset($config['consumer_class']['cleanup_snapshots'])) {
            $container->setParameter('rz_advance_page.consumer.cleanup_snapshots.class', $config['consumer_class']['cleanup_snapshots']);
        }

        if(isset($config['consumer_class']['cleanup_snapshot'])) {
            $container->setParameter('rz_advance_page.consumer.cleanup_snapshot.class', $config['consumer_class']['cleanup_snapshot']);
        }
    }
}
