# GoDaddy Reseller Store #
![Banner Image](.dev/wp-org-assets/banner-1544x500.png)

**Contributors:** [godaddy](https://profiles.wordpress.org/godaddy), [fjarrett](https://profiles.wordpress.org/fjarrett), [bfocht](https://profiles.wordpress.org/bfocht), [eherman24](https://profiles.wordpress.org/eherman24)  
**Tags:**              [admin](https://wordpress.org/plugins/tags/admin/), [posts](https://wordpress.org/plugins/tags/posts/), [users](https://wordpress.org/plugins/tags/users/)  
**Requires at least:** 4.6  
**Tested up to:**      4.8  
**Stable tag:**        1.0.0  
**License:**           GPL-2.0  
**License URI:**       https://www.gnu.org/licenses/gpl-2.0.html  

Design your own storefront for your GoDaddy Reseller plan and have more control over your customers experience and business!

[![Build Status](https://travis-ci.org/godaddy/wp-reseller-store.svg?branch=master)](https://travis-ci.org/godaddy/wp-reseller-store) [![devDependencies Status](https://david-dm.org/godaddy/wp-reseller-store/master/dev-status.svg)](https://david-dm.org/godaddy/wp-reseller-store/master?type=dev) [![License](https://img.shields.io/badge/license-GPL--2.0-brightgreen.svg)](https://github.com/godaddy/wp-reseller-store/blob/master/license.txt) [![PHP >= 5.4](https://img.shields.io/badge/php-%3E=%205.4-8892bf.svg)](https://secure.php.net/supported-versions.php) [![WordPress >= 4.6](https://img.shields.io/badge/wordpress-%3E=%204.6-blue.svg)](https://wordpress.org/download/release-archive/)  

## Description ##

**Note: This plugin requires PHP 5.4 or higher**

With this plugin, you have the option to easily design a site with the imported product catalog, complete with your pricing, preferred currency and language. You can update your site, themes, product description, and images, easily and painlessly as well as use key features like domain search and cart widgets!

[![Play video on YouTube](https://img.youtube.com/vi/mx7sRwXh444/maxresdefault.jpg)](https://www.youtube.com/watch?v=mx7sRwXh444)

**Features**
* Easily design a site that is for desktop or mobile devices in your theme
* Imports product catalog with your pricing in your preferred currency and language
* Update products/descriptions/images
* Easily create pages with different layouts and products on the page
* Add core functions to your site with domain search and cart widgets

**Languages Supported**

English - Dansk - Deutsch - Ελληνικά - Español - Español de México - Suomi - Français - हिन्दी - Bahasa Indonesia - Italiano - 日本語 - 한국어 - मराठी - Bahasa Melayu - Norsk bokmål - Nederlands - Polski - Português do Brasil - Português - Русский - Svenska - ไทย - Tagalog - Türkçe - Українська - Tiếng Việt - 简体中文 - 香港中文版 - 繁體中文

**Support**

If you run into a problem, post your question in [UserVoice](https://godaddy.uservoice.com/forums/598645-reseller-custom-storefront) or send an email to resellersupport@godaddy.com and we would be happy to help. Remember, the more information you can provide up-front, the easier it is for us to verify the problem and the faster we can help!
    * Screenshot(s) - How-to guide
    * Name and version of your theme - Video tutorial
    * List of all active plugins on your site - Video tutorial
    * Steps taken or details we should know to reproduce and verify the problem

You can call our support team at (480) 505-8857

**Contributing**

Development of this plugin is done on [GitHub](https://github.com/godaddy/wp-reseller-store). If you believe you have found a bug, or have a killer feature idea, please open a [open a new issue](https://github.com/godaddy/wp-reseller-store/issues) there. Pull requests on existing issues are also welcome!

## Changelog ##

### 1.0.0 - July 2017 ###

* New: Plugin activation is easier with integration of the [Reseller Control Center](https://reseller.godaddy.com)
* New: Add Product Widget
* New: Add demo reseller
* New: Add product widget
* New: Unit tests
* New: Add filters for language and currency settings
* New: Localization settings are now set in the [Reseller Control Center](https://reseller.godaddy.com)
* Tweak: Default language and currency settings from the RCC instead of WordPress user settings
* Tweak: Don't delete posts on uninstall
* Tweak: Language updates
* Fix: Show full post for embedded custom post type
* Fix: No longer delete custom posts on uninstall of plugin
* Fix: Pressing the "Enter" key now properly triggers a domain search

Props [@fjarrett](https://github.com/fjarrett), [@bfocht](https://github.com/bfocht), [@evanherman](https://github.com/EvanHerman), [@cberesford](https://github.com/cberesford)

### 0.2.0 - April 2017 ###

* New: Domain search shortcode
* Tweak: Verify setup JS is enqueued
* Fix: Stop using `INPUT_SERVER` as it is unreliable in FastCGI mode
* Fix: Post meta not updating after sync
* Fix: WordPress coding standards updates
* Fix: Do admin referrer check on Permalinks save

Props [@fjarrett](https://github.com/fjarrett), [@bfocht](https://github.com/bfocht), [@evanherman](https://github.com/EvanHerman)

### 0.1.0 - January 2017 ###

* Initial release

Props [@fjarrett](https://github.com/fjarrett), [@bfocht](https://github.com/bfocht)
