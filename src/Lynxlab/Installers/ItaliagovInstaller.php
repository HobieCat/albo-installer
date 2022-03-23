<?php

namespace Lynxlab\Installers;

use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class ItaliagovInstaller extends BaseInstaller
{
    private const frameworkType = 'italiagov';

    /** @var array<string, string> */
    protected $locations = array(
        'extrafiles' => 'themes/custom/italiagov/{$name}/',
    );

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->doPostInstallOrUpdate($this->getInstallPath($package, self::frameworkType));
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->doPostInstallOrUpdate($this->getInstallPath($target, self::frameworkType));
    }

    private function doPostInstallOrUpdate($basePath)
    {
        if (!\is_file($basePath.'../italiagov.libraries.yml.original')) {
            if (\rename($basePath.'../italiagov.libraries.yml', $basePath.'../italiagov.libraries.yml.original')) {
                $this->io->write("  - italiagov.libraries.yml successfully renamed to italiagov.libraries.yml.original!");
            }
        }
        if (\copy($basePath.'italiagov.libraries.yml', $basePath.'../italiagov.libraries.yml')) {
            $this->io->write("  - italiagov.libraries.yml successfully copied to parent dir!");
        }
    }
}
