# BuddyPress Security Check

* __Tested up to:__ WordPress 4.4.2
* __Stable version:__ [2.0.0](https://downloads.wordpress.org/plugin/bp-security-check.latest-stable.zip)
* __License:__ [MIT](https://opensource.org/licenses/MIT)

Combat spam registrations for a BuddyPress-powered site using Google's reCAPTCHA

## Description

> **Important**: Since version 2.0, this plugin now requires at least PHP 5.3. Please make sure you are running latest available version of PHP on your server.

This plugin adds [Google's reCAPTCHA](https://www.google.com/recaptcha/) to the BuddyPress registration field to prevent bots from registering and keep your site free from spam registrations. 

reCAPTCHA is "tough on bots, easy on humans": while it is increbianle effective on preventing bots from registering, most of the time all the user needs to do to verify themselves is simply check a box. 

After installing this plugin, you will need to [register your site with Google](https://www.google.com/recaptcha/admin) (requires a Google account) and enter the site key and secret key on the **Settings > BuddyPress > Settings** admin menu. If you would prefer not to use Google's service, there is an alternative security check method also available; see below;

Prior to version 2.0, a less effective security check method was used where the user needed to answer simple math sum before registering. This method is still available, and can be turned on on the **Settings > BuddyPress > Settings** menu.

You can learn more at the [plugin's website](https://bungeshea.com/plugins/bp-security-check/), or on [WordPress.org](https://wordpress.org/plugins/bp-security-check)

#### Translations

Thanks to the awesome work of the following translators, this plugin can be used in these languages:

* **Russian** thanks to [Howard Steele from SuperbWebsiteBuilders.com](http://superbwebsitebuilders.com/)
* **Swedish** thanks to [Thord D. Hedengren](http://tdh.me)
* **French** thanks to Frédérick Baldo
* **Serbo-Croatian** thanks to [Andrijana Nikolic from WebHostingGeeks](https://webhostinggeeks.com)
* **Spanish** thanks to Renato Alves
* **Hungarian** thanks to Laszlo Espadas
* **Brazilian Portuguese** thanks to Renato Alves
* **Danish** thanks to Andreas Bjørn Hassing Nielsen
* **Italian** thanks to [Nicole Curioni](http://nicolecurioni.com/)

If you have a translation to contribute, please sent it through to me [by email](https://bungeshea.com/contact/) or [on GitHub](https://github.com/sheabunge/bp-security-check/pulls).

## Installation

This plugin extends the functionality of [BuddyPress](https://wordpress.org/plugins/buddypress), which must be installed for this plugin to work

1. Upload the `bp-security-check` directory to `/wp-content/plugins/`
2. Activate the plugin through the __(Plugins > Installed Plugins)__ menu in WordPress
2. Visit the **Settings > BuddyPress > Settings** admin menu
3. If you want to use the more secure reCAPTCHA mode, you will need to [register your site with Google](https://www.google.com/recaptcha/admin) and enter the site and secret keys. Otherwise, choose the 'legacy math method' to turn on security checks.

## Changelog

### 2.0.0
* Converted code to class-based OOP format with namespaces
* Added plugin settings
* Implemented Composer for package management and classloading
* Added reCaPTCHA security check method
* Added Swedish translation by [Thord D. Hedengren](http://tdh.me)
* Added Russian translation by [Howard Steele from SuperbWebsiteBuilders.com](http://superbwebsitebuilders.com/)

### 1.4.0
* Added Serbo-Croatian translation by [Andrijana Nikolic from WebHostingGeeks](https://webhostinggeeks.com)
* Added French translation by Frédérick Baldo
* Fixed subtraction sums always being marked incorrect

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
