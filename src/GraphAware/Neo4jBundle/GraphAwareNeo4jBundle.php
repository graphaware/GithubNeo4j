<?php

namespace GraphAware\Neo4jBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle,
    Symfony\Component\DependencyInjection\ContainerBuilder;
use Neoxygen\NeoClient\DependencyInjection\Compiler\ConnectionRegistryCompilerPass,
    Neoxygen\NeoClient\DependencyInjection\Compiler\NeoClientExtensionsCompilerPass;
use GraphAware\Neo4jBundle\DependencyInjection\Compiler\EventSubscribersCompilerPass;

class GraphAwareNeo4jBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ConnectionRegistryCompilerPass());
        $container->addCompilerPass(new NeoClientExtensionsCompilerPass());
        $container->addCompilerPass(new EventSubscribersCompilerPass());
    }
}
