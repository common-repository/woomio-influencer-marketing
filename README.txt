=== Woomio (Influencer marketing) ===
Contributors: iclicksee
Donate link: https://iclicksee.com
Tags: woomio, influencer marketing, woomio for woocommerce
Requires at least: 5.0.0
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Woomio Plugin allows Shop Owners to add & track purchases made with token/coupon (campaign/affiliate) combinations which we supply to our affiliates.

== Description ==

Woomio Plugin allows Shop Owners to add and track purchases made with token/coupon (campaign/affiliate) combinations which we supply to our affiliates.

This allows us to accurately pay affiliates for their marketing contributions and track the success of token/coupon (campaign/affiliate) combinations.

Each time a purchase is made with a token/coupon combination the data is sent to our API and stored in our database.

Your affiliate will use URL like **http://example.com/?wmtoken=abcd1234&wmcoupon=7890wxyz** for promotion.

IMPORTANT: This plugin uses Cookie to record affiliate token/coupon so make sure that you take consent from your visitors regarding this.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/woomio-influencer-marketing` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress admin
3. Go to **Woomio -> Add Token** page to add token you get from [Woomio](https://www.woomio.com/ "Woomio").
4. You are now ready to record the sales that has gone through the added token.

== Frequently Asked Questions ==

= Does this plugin work for Woocommerce only? =

Yes. You should run your store using Woocommerce for this plugin to work.

= When are sales recorded? =

Sales data are recorded only when you mark order as completed on Woocommerce.

= How does coupon code work? =

First, you attach coupon code to token. If your visitors come to store with associated token, this coupon will be automatically applied in checkout page.

== Screenshots ==

1. Add token on the admin page
2. View your sales record on plugin dashboard page

== Changelog ==

= 1.0.0 =
* First Release

= 1.0.1 =
* Hide token/coupon as meta data in Order complete page
* Fix bug where same token could be added multiple times
* Added feature to attach discount code to token

= 1.0.2 =
* Implementation changes and bug fixes

= 1.0.3 =
* Changed token addition flow and other bug fixes

= 1.0.4 =
* Changed the way sales are recorded
* Tested on WordPress version 5.8