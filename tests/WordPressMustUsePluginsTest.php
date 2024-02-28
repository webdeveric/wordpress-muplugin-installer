<?php

declare(strict_types=1);

namespace webdeveric\Composer\Tests;

use Composer\Composer;
use Composer\Downloader\DownloadManager;
use Composer\Installer\InstallationManager;
use Composer\IO\NullIO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use webdeveric\Composer\WordPressMustUsePlugins;

#[CoversClass(WordPressMustUsePlugins::class)]
final class WordPressMustUsePluginsTest extends TestCase
{
    protected Composer $composer;
    protected NullIO & MockObject $io;
    protected InstallationManager & MockObject $installationManager;
    protected DownloadManager & MockObject $downloadManager;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp(): void
    {
        $this->io = $this->createMock('Composer\IO\NullIO');
        $this->installationManager = $this->createMock('Composer\Installer\InstallationManager');
        $this->downloadManager = $this->createMock('Composer\Downloader\DownloadManager');

        $this->composer = \Composer\Factory::create($this->io);
        $this->composer->setDownloadManager($this->downloadManager);
        $this->composer->setInstallationManager($this->installationManager);
    }

    public function testActivate()
    {
        $this->installationManager->expects($this->once())->method('addInstaller');
        $this->io->expects($this->once())->method('notice');

        $plugin = new WordPressMustUsePlugins();
        $plugin->activate($this->composer, $this->io);
    }
}
