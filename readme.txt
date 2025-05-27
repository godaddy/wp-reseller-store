=== Reseller Store ===
Contributors:      godaddy, fjarrett, bfocht, eherman24
Tags:              reseller, program, storefront, products, posts, shortcode, ecommerce, blocks
Requires at least: 4.6
Tested up to:      6.8
Requires PHP:      5.4
Stable tag:        2.2.16
License:           GPL-2.0
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Design your own storefront for your Basic and Pro Reseller plan and have more control over your customers experience and business!

== Description ==

With this plugin, you have the option to easily design a site with the imported product catalog, complete with your pricing, preferred currency and language. You can update your site, themes, product description, and images, easily and painlessly as well as use key features like domain search, domain transfer, and cart widgets!

The product catalog includes cPanel and Plesk hosting, Wordpress, Website Builder, Workspace Email, Security, and Web Marketing applications.

[youtube https://youtu.be/5xVSIrYCZKw]

**Features**

* Easily design a site that is for desktop or mobile devices in your theme
* Add domain registration and domain transfer forms to you storefront
* Imports product catalog with your pricing in your preferred currency and language
* Update products/descriptions/images
* Easily create pages with different layouts and products on the page
* Add core functions to your site with shopper and cart widgets

**Languages Supported**

English - Dansk - Deutsch - Ελληνικά - Español - Español de México - Suomi - Français - हिन्दी - Bahasa Indonesia - Italiano - 日本語 - 한국어 - मराठी - Bahasa Melayu - Norsk bokmål - Nederlands - Polski - Português do Brasil - Português - Русский - Svenska - ไทย - Tagalog - Türkçe - Українська - Tiếng Việt - 简体中文 - 香港中文版 - 繁體中文

**Support**

If you run into a problem send an email to resellersupport@godaddy.com and we would be happy to help. Remember, the more information you can provide up-front, the easier it is for us to verify the problem and the faster we can help!

* Screenshot(s) of issues or errors
* Name and version of your theme
* List of all active plugins on your site
* Steps taken or details we should know to reproduce and verify the problem

You can call our support team at (480) 505-8857

**Contributing**

Development of this plugin is done on [GitHub](https://github.com/godaddy/wp-reseller-store). Pull requests on existing issues are welcome!

**Note: This plugin requires PHP 5.4 or higher**

== Frequently Asked Questions ==

= What reseller programs will this plugin work with? =
This plugin is designed for the [GoDaddy Reseller Program](https://www.godaddy.com/reseller-program). If you have not purchased a reseller program, you can activate the plugin with our "in house" reseller. At any time, you can upgrade the reseller to your plan by deactivating the plugin and reactivating.

= What settings should I update in the Reseller Control Center? =
We recommend you set the _Home Url_ to your WordPress storefront. We also suggest you set up a _Custom Domain_ and point the DNS to our server.
The _Custom Domain_ must be on a subdomain that matches the same root domain of your WordPress storefront. For example, your WordPress storefront is hosted on _www_ and your custom domain is hosted on _shop_. Follow these [instructions to set your custom domain](https://www.godaddy.com/help/use-a-custom-domain-for-my-storefront-5040).

You can update both the home url and custom domain setting at [https://reseller.godaddy.com/settings/branding](https://reseller.godaddy.com/settings/branding)

= How do I set the currency and language for the product catalog? =
The currency and language are set in the [Reseller Control Center](https://http://reseller.godaddy.com)

= Do you have a pre-built WordPress site? =
We encourage each reseller to create their own unique site and content for their storefronts. This provides a better experience for your customers and more opportunities for search engine optimization.
While we can't test every theme with our plugin, we try our best to make the plugin compatible. GoDaddy provides several free themes including [Go](https://wordpress.org/themes/go/) and [Turnkey Storefront](https://wordpress.org/themes/turnkey-storefront/) that we test with our plugin. You can use content from your turnkey website to help you build your own WordPress site.

= I am getting an error while trying to activate the plugin. What should I do? =
If you are having an issue, we recommend you deactivate your other plugins and then try to activate the Reseller Store plugin. You can also try changing to another theme and the try re activating the plugin. Before making any of these changes, we recommend you make a backup of your site and database.

While we strive to make the plugin compatible with many themes and plugins, our plugin creates a custom post type which can cause some themes and custom builder plugins to interfere with the registration of the custom post type.

= What shortcodes are supported? =
While we recommend you use our widgets for your storefront, we do have a shortcodes for each widget.
  *Domain Registration*
    [rstore_domain]

  *Domain Transfer*
    [rstore_domain_transfer]

  *Domain Search (React Widget)*
    [rstore_domain_search page_size="5" ]

  *View Cart Button*
    [rstore_cart_button ]

  *Product Post Pod*
    [rstore_product post_id=10 show_title=1 show_content=1 show_price=1 redirect=1 button_label="Add to cart" text_cart="Continue to cart" image_size="original" ]

  *Log in/out Button*
    [rstore_login welcome_message="Welcome Back" login_button_text="Log In" logout_button_text="Log Out" ]

  *Product SVG Icon*
    [rstore_icon icon="domains | domain-registration | domain-transfer | websites | wordpress | hosting-cpanel | hosting-windows | hosting | dedicated-server | dedicated-ip | email | email-marketing | online-calendar | online-storage | online-store | seo | website-backup | website-security | ssl | code-signing-certificate | reseller | sale" ]

= How do I make the domain search return results on page load? =
You can add `?domainToCheck=example.com` to your query string on any page that has the domain search widget and the widget will perform an automatic search on page load.

== Changelog ==

= 2.2.16 - May 2025 =

* Update: Tested with WordPress 6.8

= 2.2.15 - June 2024 =

* Update: Tested with Wordpress 6.5

= 2.2.14 - August 2023 =

* Update: Add option to open cart and search results in new tab

= 2.2.13 - July 2023 =

* Update: Fix for compatibility with Beaver Builder and Classic Editor Plugins

= 2.2.12 - July 2023 =

* Update: Remove currencies ARS, BRL, CZK, EGP, HUF, MAD, RON, RUB, TRY, UYU

= 2.2.11 - June 2023 =

* Update: Asset updates

= 2.2.10 - June 2023 =

* Update: Asset updates, CI job

= 2.2.9 - May 2023 =

* Update: Updates for PHP 8.1, tested with Wordpress 6.2.2

= 2.2.8 - April 2023 =

* Update: Adding composer, removing duplicate asset includes

= 2.2.7 - March 2023 =

* Update: Verify compatibility with WordPress 6.0

= 2.2.6 - February 2021 =

* Update: Increase API TTL from 15 minutes to 4 hours

= 2.2.5 - February 2021 =

* Fix: Resolve product pricing synchronization issue with the API
* Fix: Exclude webpack config from release

= 2.2.4 - December 2020 =

* Update: Verify compatibility with WordPress 5.6

= 2.2.2 - November 2019 =

* Fix: Warnings and error with Gutenberg product blocks

* Update: Add markets to settings page

= 2.2.1 - August 2019 =

* Update: Add markets to settings page

= 2.2.0 - August 2019 =

* New: Settings page

= 2.1.3 - July 2019 =

* Fix: Issue with WPBakery Page Builder v6

= 2.1.2 - February 2019 =

* Fix: Domain search theme compatibility fixes
* Fix: Remove Add to cart button from Domain Registration products
* Fix: Compatibility with older versions of Turnkey Storefront theme

= 2.1.1 - February 2019 =

* Update: Domain search widget package

= 2.1.0 - February 2019 =

* Update: Add code signing certificate icon.
* Fix: Use a form post to cart instead of AJAX call to fix third party cookie issues with Safari browser.
* Fix: Domain widget 4.0 which uses form post to fix third party cookie issues
* Fix: WordPress 5.1 compatibility fixes

= 2.0.10 - January 2019 =

* Fixed: Add classic layout option to the product widgets
* New: Filter default options on widgets and shortcodes
* New: Filter cart options with `rstore_cart_options`
* Update: Remove unused echo statements

= 2.0.9 - January 2019 =

* Update: Update Product icon SVGs

= 2.0.8 - December 2018 =

* Fix: Domain search by adding form as an allowed tag

= 2.0.7 - December 2018 =

* Update: Add rstore_icon_html filter
* Update: CSS in Gutenberg block editor for product icons

= 2.0.6 - November 2018 =

* Fix: Restore thumbnail image import

= 2.0.5 - November 2018 =

* Update: Use SVG images for thumbnails

= 2.0.4 - November 2018 =

* New: Add SVG icons to all products
* New: rstore_icon shortcode

= 2.0.1 - November 2018 =

* Update: Apply WordPress ruleset fixes
* Update: Update svg icons for blocks

= 2.0.0 - November 2018 =

* New: Gutenberg update
* Add: Product and domain search blocks

= 1.7.1 - October 2018 =

* Update: Set height of product widget to 250px
* Update: Add button to activate with secureserver.net

= 1.7.0 - September 2018 =

* Update: Products default to redirect to cart
* Update: Use `nextStepUrl` from API after adding product to cart
* Update: Move `rstore_append_add_to_cart_form` as a filter for `the_content` and `the_excerpt`
* Update: Bumped Domain Search to version 3.1.1
* Update: Domain search prevents user navigation away from the page when domains are selected

= 1.6.1 - May 2018 =

* Publish to WordPress.org

= 1.6.0 - May 2018 =

* New: Add domain transfer widget and shortcode
* New: Add domain registration widget and shortcode
* Update: Rename Domain search widget to Domain Search (Advanced)
* New: Beaver Builder module for widgets
* New: Visual Composer (WPBakery) module for widgets

= 1.5.2 - February 2018 =

* Update: API endpoint
* Update: New demo Youtube video link in the README
* Fix: Exclude the _Squiz.PHP.CommentedOutCode.Found_ from the phpcs ruleset to fix the build
* Update: Domain search widget 2.1.6

= 1.5.1 - December 2017 =

* Update: Change logic on how urls are built
* Update: CSS fixes for domain search results when displayed in a modal

= 1.5.0 - December 2017 =

* Fix: Add bootstrap classes to buttons
* Fix: CSS and display issues when using shortcodes
* Fix: Display domain name in taken and available message
* New: Add support for domainToCheck on query string

= 1.4.1 - December 2017 =

* Fix: Fail softly on older PHP versions

= 1.4.0 - December 2017 =

* Fix: Remove menu position

= 1.3.0 - December 2017 =

* New: Bulk restore product post
* Fix: Compatibility issues with other themes/plugins
* Fix: Short codes render in wrong place

= 1.2.0 - November 2017 =

* Update: Domain search 2.1.2

= 1.1.1 - November 2017 =

* Fix: Compatibility issues with Page Builder by SiteOrigin
* New: Add Product Options to Edit Product Screen
* Tweak: Moved reset button meta box to Product Options

= 1.1.0 - October 2017 =

* New: Import tags
* Update: Domain search widget 2.1.0
* Fix: Issue with shortcodes and Visual Composer
* New: Add `rstore_api_query_args` filter
* Remove: `rstore_api_currency` and `rstore_api_market_id` filters
* New: Add Reset button to product page
* Tweak: Add span tags to product pricing
* New: Add empty index.php to every directory

= 1.0.3 - August 2017 =

*  New: Shopper login widget
*  New: Add more options including _cart redirect_ to the product widget
*  New: Move shortcodes to a new class and add new shortcodes.
*  Fix: No longer store cart count in a cookie
*  Fix: Image size on the product widget
*  Fix: Display error if post id cannot be found on product widget
*  Fix: No longer remove html from widget labels

= 1.0.0 - July 2017 =

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

= 0.2.0 - April 2017 =

* New: Domain search shortcode
* Tweak: Verify setup JS is enqueued
* Fix: Stop using `INPUT_SERVER` as it is unreliable in FastCGI mode
* Fix: Post meta not updating after sync
* Fix: WordPress coding standards updates
* Fix: Do admin referrer check on Permalinks save

Props [@fjarrett](https://github.com/fjarrett), [@bfocht](https://github.com/bfocht), [@evanherman](https://github.com/EvanHerman)

= 0.1.0 - January 2017 =

* Initial release

Props [@fjarrett](https://github.com/fjarrett), [@bfocht](https://github.com/bfocht)
