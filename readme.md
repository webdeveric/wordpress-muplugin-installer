# WordPress must-use plugin installer

[![Build Status](https://travis-ci.org/LPLabs/wordpress-muplugin-installer.svg?branch=master)](https://travis-ci.org/LPLabs/wordpress-muplugin-installer)
[![Test Coverage](https://codeclimate.com/github/LPLabs/wordpress-muplugin-installer/badges/coverage.svg)](https://codeclimate.com/github/LPLabs/wordpress-muplugin-installer/coverage)
[![Code Climate](https://codeclimate.com/github/LPLabs/wordpress-muplugin-installer/badges/gpa.svg)](https://codeclimate.com/github/LPLabs/wordpress-muplugin-installer)
[![Issue Count](https://codeclimate.com/github/LPLabs/wordpress-muplugin-installer/badges/issue_count.svg)](https://codeclimate.com/github/LPLabs/wordpress-muplugin-installer/issues)

The [Multi-Framework Composer Library Installer](https://github.com/composer/installers) does not completely install WordPress **must-use plugins**.
It does put the plugin in the `mu-plugins` folder, but that isn't enough to be recognized as a must-use plugin by WordPress.

This plugin addresses that by copying the plugin entry point file into the `mu-plugins` folder.
Your plugin can indicate which file is the main entry point by specifying it in the `extra` section of your `composer.json` file.

```json
{
  "name": "vendorname/muplugin",
  "description": "Just another must-use plugin",
  "type": "wordpress-muplugin",
  "extra": {
    "wordpress-muplugin-entry": "your-plugin-entry-point-file.php"
  },
  "require": {
    "composer/installers": "^1.0"
  }
}
```

If you do not specify the entry point yourself, this plugin will figure out which PHP files
are WordPress plugins by looking for `Plugin Name:` in the first 8 kilobytes of each PHP file in the root of your plugin folder.

---

This plugin is currently in development and hasn't been published to Packagist yet. You'll need to get it from Github as shown below.

Since [repositories](https://getcomposer.org/doc/04-schema.md#repositories) is `root-only`, you'll have to put this in your project's `composer.json` file.

```json
{
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/LPLabs/wordpress-muplugin-installer.git"
    }
  ],
  "require": {
    "lplabs/wordpress-muplugin-installer": "dev-master"
  }
}
```
