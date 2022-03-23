<?php

namespace Lynxlab\Installers;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller as BaseInstaller;

class AlboInstaller extends BaseInstaller
{
    /**
     * @inheritDoc
     */
    public function getInstallPath(PackageInterface $package)
    {
        $prefix = substr($package->getPrettyName(), 0, 23);
        if ('phpdocumentor/template-' !== $prefix) {
            throw new \InvalidArgumentException(
                'Unable to install template, phpdocumentor templates '
                .'should always start their package name with '
                .'"phpdocumentor/template-"'
            );
        }

        return 'data/templates/'.substr($package->getPrettyName(), 23);
    }

    /**
     * @inheritDoc
     */
    public function supports($packageType)
    {
        return 'phpdocumentor-template' === $packageType;
    }
}