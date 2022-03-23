<?php

namespace lynxlab\Italiagov;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class AlboInstallerPlugin implements PluginInterface
{
    private $installer;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->installer = new AlboInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($this->installer);
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        $composer->getInstallationManager()->removeInstaller($this->installer);
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }
}