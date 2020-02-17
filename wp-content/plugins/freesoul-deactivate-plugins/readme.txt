=== Freesoul Deactivate Plugins ===
Contributors: giuse
Donate link:
Tags: disable plugins, backend speed up, plugins deactivation, speed optimization, debugging plugins, plugin preview
Requires at least: 4.6
Tested up to: 5.3.3
Stable tag: 1.7.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Freesoul Deactivate Plugins allows you to disable specific plugins on specific pages for speed optimization, debugging and problem-solving even when many plugins are active.

== Description ==

Freesoul Deactivate Plugins allows you to disable plugins on specific pages and archives for speed optimization, debugging and problem-solving even if many plugins are active.

It works for every page, blog posts, custom posts that are publicly queryable, archives and backend pages.

Usually, the number of the needed plugin on a specific page is lower than the number of globally active plugins.

In this case, selectively disabling plugins will drastically increase the performance of your website, both for the frontend and the backend.

Disabling unused plugins in the backend, is the most effective method to speed up the backend when you have many plugins. You will not find many other easy solutions for the backend.


If you would like to see how a specific page would be enabling or disabling a particular plugin or switching to another theme, you can see a preview clicking on the preview icon.

In the following video, the plugin [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) is causing a fatal error, and the web page is not displayed at all. Looking at how the page would be disabling specific plugins and switching to the default WordPress theme, you will quickly find the guilty.

You can do all this in preview mode, without showing to the rest of the world what you are doing.


[youtube https://youtu.be/ZaqXY8psdCs]


You will find the Settings Page Submenu under the admin plugins menu.

In the Settings Page, you have global control of the plugins deactivation for each post type, page, post, and archive.

Moreover, you will find a section on every single page and post.

[vimeo https://player.vimeo.com/video/278470253]



If you want to disable plugins for the mobile version only on specific pages, you should also install [Specific Content for Mobile](https://wordpress.org/plugins/specific-content-for-mobile/). Then if you create a page mobile version, you will see it in the Single plugins deactivation page settings.

For globally disabling plugins on mobile, you don't need any other plugins.

In any case, if you disable plugins only for the mobile version, be sure you have a server cache plugin that distinguishes between mobile and desktop devices, as, e.g. [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/) or [WP Fastest Cache](https://wordpress.org/plugins/wp-fastest-cache/).


Only the permalinks structures "Day and name", "Month and name", "Post name"  and the custom ones ending with "%postname%" are supported for permanently deactivating plugins (they are also better for SEO).


The same as above if you are using plugins to change the permalinks, as, e.g., Permalink Manager Lite.



Remember that if you have a Multisite Installation, in every single site you will be able to manage only the not Network globally active plug-ins and you have to activate this plugin on every single site, not globally on the Network.



Here you will find <a href="https://freesoul-deactivate-plugins.com/documentation/">the plugin documentation</a>
The documentation is still in progress, if you don't find what you are looking for, please open a thread on the <a href="https://wordpress.org/support/plugin/freesoul-deactivate-plugins/">Support Forum</a>

With the free version you can:
- disable plugins on pages, posts, custom posts, archives, search results page, mobile pages, terms archives
- disable plugins and the theme on specific backend pages
- disable JavaScript for preview (only for front-end) for problem solving
- disable plugins by custom URL both for frontend and backend
- change plugins firing order
- have an overview of the tests performed on the official plugins, including code risk and PHP errors
- preview disabling specific plugins and switching to another theme without affecting the public site
- preview of Google Page Speed Insights for single posts, pages and custom posts (preview without cache, the page may give lower score without cache, use it for comparisons during the optimization)

With the premium version that is coming soon you will also be able to:
- automatically detect unused plugins both for frontend and backend
- have automatic GTMetrix reports to show to your customer after the speed optimization activity
- remove specific action and filter hooks
- record ajax activities to find out on which ajax action you need to disable specific plugins

We want to give for free all that you need to disable plugins both for speed optimization and problem solving.
And we will offer soon a premium and paid version to make the life easier for the users who work on the websites of their customers.

We would love to hear also your ideas. 
If you would like a premium feature that we haven't considered yet, we will be happy to read your comment at <a href="https://freesoul-deactivate-plugins.com/ideas-for-freesoul-deactivate-plugins-pro/">https://freesoul-deactivate-plugins.com/ideas-for-freesoul-deactivate-plugins-pro/</a>



FOR DEVELOPERS: if in your custom code you want to check if a plugin is globally active, you can use the constant 'EOS_'.$const.'_ACTIVE'.

Where $const is str_replace( '-','_',strtoupper( str_replace( '.php','',$plugin_file_name ) ) ).

$plugin_file name is the name of the main file of the plugin.

For example, you have deactivated WooCommerce in a specific page, but you want that some code related to WooCommerce runs in any case (e.g. code for displaying the cart link).
You can check if WooCommerce is globally active using this condition:

`
if( defined(  'EOS_WOOCOMMERCE_ACTIVE' ) && EOS_WOOCOMMERCE_ACTIVE ){
    //your code here
}
`

In the following example we disable the Revolution Slider shortcode when we disable it on mobile
`
if( wp_is_mobile() && defined( 'EOS_REVSLIDER_ACTIVE' ) && EOS_REVSLIDER_ACTIVE ){
	add_shortcode( 'rev_slider','__return_false' );
}
`



== Installation ==

1. Upload the entire `freesoul-deactivate-plugins` folder to the `/wp-content/plugins/` directory or install it using the usual installation button in the Plugins administration page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. After successful activation you will be automatically redirected to the plugin global settings page.
4. All done. Good job!


== Frequently Asked Questions ==

Here you will find <a href="https://freesoul-deactivate-plugins.com/documentation/faqs/">the frequently asked questions</a>.

== Changelog ==

= 1.7.1 =
* Removed: link to the plugin website from the console

= 1.7.0 =
* Added: backend singles settings page to disable plugins and theme on the backend pages
* Added: custom URLs for backend 
* Added: translations in Spanish (Spain, Colombia, Mexico), French (France,Canada,Belgium), German (Germany, Switzerland, Austria)


*<a href="https://freesoul-deactivate-plugins.com/documentation/change-log/">Complete Change Log</a>

== Upgrade Notice ==

Version 1.7.0 introduces the possibility to disable plugins and theme on backend pages



== Screenshots ==

1. Global settings page (you find it under admin plugins menu)
2. Settings in each single page and post