<?php

namespace Truelab\KottiModelBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TruelabKottiModelExtension extends Extension
{
    public static $types = array(
        'content'  => 'Truelab\KottiModelBundle\Model\Content',
        'document' => 'Truelab\KottiModelBundle\Model\Document',
        'file' => 'Truelab\KottiModelBundle\Model\File',
        'image' => 'Truelab\KottiModelBundle\Model\Image'
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $types = array_merge(static::$types, $config['types']);
        $config['types'] = $types;

        foreach($config['types'] as $type => $class) {
            if(!$class) {
                unset($config['types'][$type]);
            }
        }

        $container->setParameter('truelab_kotti_model.type_column', $config['type_column']);
        $container->setParameter('truelab_kotti_model.types', $config['types']);
        $container->setParameter('truelab_kotti_model.filter', $config['filter']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
