<?php

/**
 * Install WordPress must-use plugins with Composer
 *
 * @author Eric King <eric@webdeveric.com>
 */

namespace webdeveric\Composer\Installer;

use RuntimeException;
use Composer\Installer\LibraryInstaller;
use Composer\Installers\WordPressInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use React\Promise\PromiseInterface;

class WordPressMustUsePluginInstaller extends LibraryInstaller
{
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package): bool
    {
        $installed = parent::isInstalled($repo, $package);

        if ($installed) {
            foreach ($this->getEntryFileLocations($package) as $entryFile) {
                if (!is_file($entryFile)) {
                    $installed = false;
                    break;
                }
            }
        }

        return $installed;
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package): PromiseInterface | null
    {
        $promise = parent::install($repo, $package);

        if ($promise instanceof PromiseInterface) {
            return $promise->then(function () use ($package) {
                $this->installEntryFiles($package);
            });
        }

        return null;
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target): PromiseInterface | null
    {
        $this->uninstallEntryFiles($initial);

        $promise = parent::update($repo, $initial, $target);

        if ($promise instanceof PromiseInterface) {
            return $promise->then(function () use ($target) {
                $this->installEntryFiles($target);
            });
        }

        return null;
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package): PromiseInterface | null
    {
        $this->uninstallEntryFiles($package);

        return parent::uninstall($repo, $package);
    }

    public function getInstallPath(PackageInterface $package): string
    {
        $installer = new WordPressInstaller($package, $this->composer, $this->io);

        return $installer->getInstallPath($package, 'wordpress');
    }

    /**
     * Install each must-use plugin entry file
     */
    protected function installEntryFiles(PackageInterface $package): void
    {
        foreach ($this->getEntryFileLocations($package) as $src => $dest) {
            $copied = $this->filesystem->copy($src, $dest);

            $this->io->notice(
                sprintf(
                    '    <fg=default>Copying <fg=magenta>%1$s</> to <fg=magenta>%2$s</> -</> %3$s',
                    $src,
                    $dest,
                    $copied ? '<fg=green>OK</>' : '<fg=red>FAILED</>'
                )
            );

            if (!$copied) {
                throw new RuntimeException(sprintf('Cannot copy %s to %s', $src, $dest));
            }
        }
    }

    /**
     * Uninstall each must-use plugin entry file
     */
    protected function uninstallEntryFiles(PackageInterface $package): void
    {
        foreach ($this->getEntryFileLocations($package) as $dest) {
            $unlinked = $this->filesystem->unlink($dest);

            $this->io->notice(
                sprintf(
                    '    <fg=default>Removing <fg=magenta>%1$s</> -</> %2$s',
                    $dest,
                    $unlinked ? '<fg=green>OK</>' : '<fg=red>FAILED</>'
                )
            );

            if (!$unlinked) {
                throw new RuntimeException('Cannot unlink ' . $dest);
            }
        }
    }

    /**
     * Get an item from the package extra array
     */
    protected function getPackageExtra(PackageInterface $package, string $key, mixed $default = null): mixed
    {
        $extra = $package->getExtra();

        return array_key_exists($key, $extra) ? $extra[$key] : $default;
    }

    /**
     * Get the file location source/destination of the must-use plugin entry point files
     */
    protected function getEntryFileLocations(PackageInterface $package): array
    {
        $muPluginsDir = dirname($this->composer->getInstallationManager()->getInstallPath($package));

        return array_reduce(
            $this->getPackageEntryPoints($package),
            function ($locations, $entryPoint) use ($muPluginsDir) {
                $locations[$entryPoint] =  $muPluginsDir . DIRECTORY_SEPARATOR . basename($entryPoint);

                return $locations;
            },
            []
        );
    }

    /**
     * Get the package entry points
     */
    protected function getPackageEntryPoints(PackageInterface $package): array
    {
        $dir = $this->composer->getInstallationManager()->getInstallPath($package);
        $entry = $this->getPackageExtra($package, 'wordpress-muplugin-entry');
        $entryPoints = $entry ? (is_array($entry) ? $entry : [$entry]) : [];

        if (empty($entryPoints)) {
            $phpFiles = glob(rtrim($dir, '/') . '/*.php', GLOB_NOSORT);

            foreach ($phpFiles as $file) {
                $entryPoints[] = basename($file);
            }
        }

        foreach ($entryPoints as $index => $file) {
            $entryPoints[$index] = $dir . $file;
        }

        return array_filter($entryPoints, [$this, 'looksLikePlugin']);
    }

    /**
     * Does the file look like a WordPress plugin?
     */
    protected function looksLikePlugin(string $file): bool
    {
        if (!$file || !is_file($file) || !$this->filesystem->isReadable($file)) {
            return false;
        }

        $chunk = str_replace("\r", "\n", file_get_contents($file, false, null, 0, 8192));

        return preg_match('/^[ \t\/*#@]*Plugin Name:(.*)$/mi', $chunk, $match) && $match[1];
    }
}
