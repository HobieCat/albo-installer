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
        $this->io->write(\sprintf("ItaliagovInstaller installed in %s", $this->getInstallPath($package, self::frameworkType)));
    }
}
