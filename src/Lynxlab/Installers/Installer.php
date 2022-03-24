<?php

namespace Lynxlab\Installers;

use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use React\Promise\PromiseInterface;

class Installer extends LibraryInstaller
{
    /**
     * Package types to installer class map
     *
     * @var array<string, string>
     */
    private $supportedTypes = array(
        'italiagov' => 'ItaliagovInstaller',
    );

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $type = $package->getType();
        $frameworkType = $this->findFrameworkType($type);
        $installer = $this->getInstaller($package);

        if ($installer === null) {
            throw new \InvalidArgumentException(
                'Sorry the package type of this package is not yet supported.'
            );
        }

        $path = $installer->getInstallPath($package, $frameworkType);
        if (!$this->filesystem->isAbsolutePath($path)) {
            $path = getcwd() . '/' . $path;
        }

        return $path;
    }

    /**
     * @inheritDoc
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $installer = $this->getInstaller($package);

        if ($installer !== null && method_exists($installer, 'preInstall')) {
            $installer->preInstall($repo, $package);
        }

        $parentInstall = parent::install($repo, $package);

        if ($installer !== null && method_exists($installer, 'postInstall')) {
            $installer->postInstall($repo, $package);
        }

        return $parentInstall;
    }

    /**
     * @inheritDoc
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $installer = $this->getInstaller($target);

        if ($installer !== null && method_exists($installer, 'preUpdate')) {
            $installer->preUpdate($repo, $initial, $target);
        }

        $parentUpdate = parent::update($repo, $initial, $target);

        if ($installer !== null && method_exists($installer, 'postUpdate')) {
            $installer->postUpdate($repo, $initial, $target);
        }

        return $parentUpdate;
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $installPath = $this->getPackageBasePath($package);
        $io = $this->io;
        $outputStatus = function () use ($io, $installPath) {
            $io->write(sprintf('Deleting %s - %s', $installPath, !file_exists($installPath) ? '<comment>deleted</comment>' : '<error>not deleted</error>'));
        };

        $installer = $this->getInstaller($package);

        if ($installer !== null && method_exists($installer, 'preUninstall')) {
            $installer->preUninstall($repo, $package);
        }

        $promise = parent::uninstall($repo, $package);

        if ($installer !== null && method_exists($installer, 'postUninstall')) {
            $installer->postUninstall($repo, $package);
        }

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($outputStatus);
        }

        // If not, execute the code right away as parent::uninstall executed synchronously (composer v1, or v2 without async)
        $outputStatus();

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        $frameworkType = $this->findFrameworkType($packageType);

        if ($frameworkType === false) {
            return false;
        }

        $locationPattern = $this->getLocationPattern($frameworkType);

        return preg_match('#' . $frameworkType . '-' . $locationPattern . '#', $packageType, $matches) === 1;
    }

    /**
     * Finds a supported framework type if it exists and returns it
     *
     * @return string|false
     */
    protected function findFrameworkType(string $type)
    {
        krsort($this->supportedTypes);

        foreach ($this->supportedTypes as $key => $val) {
            if ($key === substr($type, 0, strlen($key))) {
                return substr($type, 0, strlen($key));
            }
        }

        return false;
    }

    /**
     * Get the second part of the regular expression to check for support of a
     * package type
     */
    protected function getLocationPattern(string $frameworkType): string
    {
        $pattern = null;
        if (!empty($this->supportedTypes[$frameworkType])) {
            $frameworkClass = 'Lynxlab\\Installers\\' . $this->supportedTypes[$frameworkType];
            /** @var BaseInstaller $framework */
            $framework = new $frameworkClass(new Package('dummy/pkg', '1.0.0.0', '1.0.0'), $this->composer, $this->getIO());
            $locations = array_keys($framework->getLocations($frameworkType));
            if ($locations) {
                $pattern = '(' . implode('|', $locations) . ')';
            }
        }

        return $pattern ?: '(\w+)';
    }

    private function getInstaller(PackageInterface $package)
    {
        $type = $package->getType();
        $frameworkType = $this->findFrameworkType($type);
        if ($frameworkType !== false) {
            $class = 'Lynxlab\\Installers\\' . $this->supportedTypes[$frameworkType];
            $installer = new $class($package, $this->composer, $this->getIO());
        } else {
            $installer = null;
        }
        return $installer;
    }

    private function getIO(): IOInterface
    {
        return $this->io;
    }
}
