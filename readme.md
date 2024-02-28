# WordPress must-use plugin installer

```shell
composer require webdeveric/wordpress-muplugin-installer
```

---

The [Multi-Framework Composer Library Installer](https://github.com/composer/installers) does not completely install WordPress **must-use plugins**.
It does put the plugin in the `mu-plugins` folder, but that isn't enough to be recognized as a must-use plugin by WordPress.

This plugin addresses that by copying the plugin entry point file into the `mu-plugins` folder.
Your plugin can indicate which file is the main entry point by specifying it in the `extra` section of your `composer.json` file.

```json
{
  "name": "scope/mu-plugin",
  "description": "Just another must-use plugin",
  "type": "wordpress-muplugin",
  "extra": {
    "wordpress-muplugin-entry": "your-plugin-entry-point-file.php"
  },
  "require": {
    "webdeveric/wordpress-muplugin-installer": "^2.0"
  }
}
```

If you do not specify `wordpress-muplugin-entry`, this plugin will figure out which PHP files
are WordPress plugins by looking for `Plugin Name:` in the first 8 kilobytes of each PHP file in the root of your plugin folder.

## Git hooks

Run this to install a git hook that will run PHPCS, PHPMD, and PHPUnit before you commit.

```
composer setup-hooks
```
