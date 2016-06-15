<?php

namespace Rz\AdvancePageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Rz\AdvancePageBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;

class RzAdvancePageBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverrideServiceCompilerPass());
    }
}
