<?php

namespace Truelab\KottiModelBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Truelab\KottiModelBundle\DependencyInjection\CompilerPass\PostLoaderCompilerPass;

class TruelabKottiModelBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PostLoaderCompilerPass());
    }
}
