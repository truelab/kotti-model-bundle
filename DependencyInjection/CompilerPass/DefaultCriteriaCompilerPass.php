<?php

namespace Truelab\KottiModelBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class DefaultCriteriaCompilerPass
 * @package Truelab\KottiModelBundle\DependencyInjection
 */
class DefaultCriteriaCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('truelab_kotti_model.default_criteria_manager')) {
            return;
        }

        $definition = $container->getDefinition(
            'truelab_kotti_model.default_criteria_manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'truelab_kotti_model.default_criteria'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'add',
                array(new Reference($id))
            );
        }
    }
}

