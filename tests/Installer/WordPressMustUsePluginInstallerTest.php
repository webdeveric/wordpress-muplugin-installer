<?php

declare(strict_types=1);

namespace webdeveric\Composer\Tests\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Util\Filesystem;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use webdeveric\Composer\Installer\WordPressMustUsePluginInstaller;

#[CoversClass(WordPressMustUsePluginInstaller::class)]
final class WordPressMustUsePluginInstallerTest extends TestCase
{
    protected Filesystem & MockObject $fs;
    protected IOInterface & MockObject $io;
    protected Composer $composer;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function setUp(): void
    {
        $this->fs = $this->createMock(Filesystem::class);

        $this->io = $this->createMock(IOInterface::class);

        $this->composer = \Composer\Factory::create($this->io);
        $this->composer->setDownloadManager($this->createMock('Composer\Downloader\DownloadManager'));
        $this->composer->setInstallationManager($this->createMock('Composer\Installer\InstallationManager'));
    }

    public function testSupportedPackages()
    {
        $installer = new WordPressMustUsePluginInstaller($this->io, $this->composer, 'wordpress-muplugin', $this->fs);

        $package = new Package('test-plugin', '1.0.0', '1.0.0');

        $package->setType('wordpress-muplugin');

        $this->assertTrue($installer->supports($package->getType()));

        $package->setType('some-unsupported-package-type');

        $this->assertFalse($installer->supports($package->getType()));
    }

    public function testInstallPath()
    {
        $installer = new WordPressMustUsePluginInstaller($this->io, $this->composer, 'wordpress-muplugin', $this->fs);

        $package = new Package('test-plugin', '1.0.0', '1.0.0');

        $package->setType('wordpress-muplugin');

        $this->assertEquals(
            'wp-content/mu-plugins/test-plugin/',
            $installer->getInstallPath($package)
        );

        $this->expectException(InvalidArgumentException::class);

        $package->setType('some-unsupported-package-type');

        $installer->getInstallPath($package);
    }
}
