<?php

namespace Truelab\KottiModelBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class PostLoaderCompilerPass
 * @package Truelab\KottiModelBundle\DependencyInjection
 */
class PostLoaderCompilerPass implements CompilerPassInterface
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
        if (!$container->hasDefinition('truelab_kotti_model.model_factory')) {
            return;
        }

        $definition = $container->getDefinition(
            'truelab_kotti_model.model_factory'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'truelab_kotti_model.post_loader'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addPostLoader',
                array(new Reference($id))
            );
        }
    }
}

