<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\DependencyInjection;

use Answear\DpdPlPickupServicesBundle\Service\ConfigProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AnswearDpdPlPickupServicesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(ConfigProvider::class);
        $definition->setArguments(
            [
                $config['url'],
                $config['key'],
                $config['requestTimeout'],
            ]
        );
    }
}
