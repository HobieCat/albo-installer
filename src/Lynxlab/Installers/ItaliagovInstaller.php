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
        $basePath = $this->getInstallPath($package, self::frameworkType);
        if (!\is_file($basePath.'../italiagov.libraries.yml.original')) {
            if (\rename($basePath.'../italiagov.libraries.yml', $basePath.'../italiagov.libraries.yml.original')) {
                $this->io->write("italiagov.libraries.yml successfully renamed to italiagov.libraries.yml.original!");
            }
        }
        if (\copy($basePath.'italiagov.libraries.yml', $basePath.'../italiagov.libraries.yml')) {
            $this->io->write("italiagov.libraries.yml successfully copied to parent dir!");
        }
        // $this->io->write(\sprintf("ItaliagovInstaller installed in %s", $this->getInstallPath($package, self::frameworkType)));
    }
}
