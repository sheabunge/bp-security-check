# BuddyPress Security Check

* __Tested up to:__ WordPress 4.3
* __Stable version:__ [1.4](http://downloads.wordpress.org/plugin/bp-security-check.latest-stable.zip)
* __License:__ [MIT](http://opensource.org/licenses/MIT)

Help combat spam registrations by forcing the user to answer a simple math sum while registering for your BuddyPress-powered site

## Description

This plugin will add a field to the BuddyPress registration form where the user will need to answer simple math sum before registering. This is an effort to stop spam bots from registering on your site. The math sum will be composed of adding or subtracting two random numbers between 0 and 10 (inclusive).

You can learn more at the [plugin's website](http://bungeshea.com/plugins/bp-security-check/), or on [WordPress.org](http://wordpress.org/plugins/bp-security-check)

#### Translations

Thanks to the awesome work of the following translators, this plugin can be used in these languages:

* **French** thanks to Frédérick Baldo
* **Serbo-Croatian** thanks to [Andrijana Nikolic from WebHostingGeeks](https://webhostinggeeks.com)
* **Spanish** thanks to Renato Alves
* **Hungarian** thanks to Laszlo Espadas
* **Brazilian Portuguese** thanks to Renato Alves
* **Danish** thanks to Andreas Bjørn Hassing Nielsen
* **Italian** thanks to [Nicole Curioni](http://nicolecurioni.com/)

If you have a translation to contribute, please sent it through to me [by email](http://bungeshea.com/contact/) or [on GitHub](https://github.com/bungeshea/bp-security-check/pulls).

## Installation

This plugin extends the functionality of [BuddyPress](http://wordpress.org/plugins/buddypress), which must be installed for this plugin to work

1. Upload `bp-security-check.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the __(Plugins > Installed Plugins)__ menu in WordPress
3. Done! The plugin has no settings to configure, just install and activate

## Changelog

### 1.4.0
* Added Serbo-Croatian translation by [Andrijana Nikolic from WebHostingGeeks](https://webhostinggeeks.com)
* Added French translation by Frédérick Baldo

### 1.3.2
* Added Spanish translation by Renato Alves

### 1.3.1
* Fixed incorrect term in Danish translation (Sikkerhedsspørgsmål is a single word) [[#](https://wordpress.org/support/topic/translation-293)]

### 1.3.0
* Added Danish translation by Andreas Bjørn Hassing Nielsen
* Added Italian translation by [Nicole Curioni](http://nicolecurioni.com/)
* Made transient names unique to prevent race conditions

### 1.2.0
* Added Hungarian translation by Laszlo Espadas
* Added Brazilian Portuguese translation by Renato Alves
* Saved sum information in database instead of hidden fields in an attempt to prevent bots
* Code refactoring

### 1.1.0.1
* Fixed bug preventing the plugin from loading

### 1.1.0
* Updated to support translations
* Use mt_rand() function instead of rand()
* Add code documentation
* Use proper class methods, not completely static
* Ensure that the sum never equals 0

### 1.0.1
* Remove buggy multiplication and division functionality

### 1.0.0
* Stable version release
