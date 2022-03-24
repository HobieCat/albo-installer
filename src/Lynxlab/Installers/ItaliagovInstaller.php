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

    public function postInstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->doPostInstallOrUpdate($this->getInstallPath($package, self::frameworkType));
    }

    public function postUpdate(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->doPostInstallOrUpdate($this->getInstallPath($target, self::frameworkType));
    }

    private function doPostInstallOrUpdate($basePath)
    {
        $backupExt = '.original';
        $moveToParent = [
            'italiagov.libraries.yml',
        ];
        foreach($moveToParent as $filename) {
            if (!\is_file($basePath.'..'.DIRECTORY_SEPARATOR.$filename.$backupExt)) {
                if (\rename($basePath.'..'.DIRECTORY_SEPARATOR.$filename, $basePath.'..'.DIRECTORY_SEPARATOR.$filename.$backupExt)) {
                    $this->io->write("  - <info>$filename</info> successfully renamed to <comment>$filename$backupExt</comment>!");
                }
            }
            if (\copy($basePath.$filename, $basePath.'..'.DIRECTORY_SEPARATOR.$filename)) {
                $this->io->write("  - <info>$filename</info> successfully copied to parent dir!");
            }
        }
    }
}
