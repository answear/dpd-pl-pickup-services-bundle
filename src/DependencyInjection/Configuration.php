<?php

declare(strict_types=1);

namespace Answear\DpdPlPickupServicesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const API_URL = 'https://mypudo.dpd.com.pl/api/';
    private const CONNECTION_TIMEOUT = 10.0;
    private const REQUEST_TIMEOUT = 10.0;

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('answear_dpd_pl_pickup_services');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('url')->defaultValue(self::API_URL)->end()
            ->scalarNode('key')->cannotBeEmpty()->end()
            ->floatNode('requestTimeout')->defaultValue(self::REQUEST_TIMEOUT)->end()
            ->end();

        return $treeBuilder;
    }
}
