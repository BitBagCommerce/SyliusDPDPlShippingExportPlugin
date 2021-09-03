<?php

declare(strict_types=1);

namespace BitBag\DpdPlShippingExportPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bitbag_dpd_pl_shipping_export_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
