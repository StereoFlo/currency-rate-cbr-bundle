<?php

namespace CurrencyRateCbrBundle\DependencyInjection;

use CurrencyRateCbrBundle\CurrencyFactory;
use CurrencyRateCbrBundle\CurrencyManager;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class CurrencyRateCbrExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration(false);
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('currency_rate_cbr.cache_enabled', isset($config['cache_enabled']));
        if (isset($config['cache_enabled']) && empty($config['cache_adapter'])) {
            throw new RuntimeException('property cache_adapter must be specified in config');
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $this->init($container, $config);

    }

    private function init(ContainerBuilder $container, array $config): void
    {
        $container
            ->register('currency_rate_cbr.factory', CurrencyFactory::class)
            ->setAutoconfigured(true);
        if (isset($config['cache_enabled']) && isset($config['cache_adapter'])) {
            $container
                ->register('currency_rate_cbr.currency_manager', CurrencyManager::class)
                ->setArgument('$currencyFactory', 'currency_rate_cbr.factory')
                ->setArgument('$cache', new Reference($config['cache_adapter']))
                ->setAutoconfigured(true);
        }
        if (empty($config['cache_enabled'])) {
            $container
                ->register('currency_rate_cbr.currency_manager', CurrencyManager::class)
                ->setArgument('$currencyFactory', 'currency_rate_cbr.factory')
                ->setAutoconfigured(true);
        }
    }
}
