<?php
/**
 * Install WordPress must-use plugins with Composer
 *
 * @author Eric King <eric@webdeveric.com>
 */

namespace webdeveric\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use webdeveric\Composer\Installer\WordPressMustUsePluginInstaller;

final class WordPressMustUsePlugins implements PluginInterface
{
    private WordPressMustUsePluginInstaller | null $installer = null;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->installer = new WordPressMustUsePluginInstaller($io, $composer, 'wordpress-muplugin');

        $composer->getInstallationManager()->addInstaller($this->installer);

        $io->notice(sprintf('<fg=magenta>Composer plugin activated:</> <fg=default>%s</>', self::class));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
        if ($this->installer) {
            $composer->getInstallationManager()->removeInstaller($this->installer);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // Do nothing
    }
}
