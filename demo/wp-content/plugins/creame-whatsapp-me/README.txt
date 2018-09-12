=== WhatsApp me ===
Contributors: creapuntome, pacotole, davidlillo
Donate link: https://www.paypal.me/creapuntome/
Tags: whatsapp, button, chat, support, contact
Requires at least: 3.0.1
Tested up to: 4.9.4
Requires PHP: 5.3
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add support to your visitors directly with WhatsApp.

== Description ==

### The perfect plugin to engage and retain customers.

#### Communication with your customers can be very easy

With **WhatsApp me** you will get the visitors of your website to contact you through WhatsApp with a single click.

#### Why WhatsApp?
WhatsApp is used in more than 100 countries and supports more than 50 languages. Recent surveys say that 96% of users prefer to use a *messaging app* before calling by phone. If none of this has convinced you, think one thing, your grandmother knows how to use WhatsApp. Do not lose more customers and sales. Try **WhatsApp me**.

Options:

1. Phone: Enter the phone number.
2. Mobile only: Select if you want the button to be visible only on mobile devices. WhatsApp Web/App will open on the desktop (if available).
3. Call to action: Write a message to encourage users to contact you through WhatsApp.
4. Delay: You can define a timeout to display the call-to-action message.
5. Message: You can define the first message to send.
6. Advanced: configure on which pages you want to show or hide your WhatsApp button.
7. If you have Google Analytics, an event is triggered when the user launches WhatsApp.
8. Can override call to action, message or visibility on every post, page or custom post.

== Installation ==

1. Upload the entire `creame-whatsapp-me` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= I can't see the button or it's over / under another thing =

You can change the position of the button so that nothing covers it by adding this CSS in *Appearance > Customize > Custom CSS*:
`.whatsappme { z-index:9999; }`
Greater values of z-index are left over, the default value is 400.

= What about GDPR? =

WhatsApp me don't save any personal data and don't use cookies.

== Screenshots ==

1. WhatsApp me general settings.
2. WhatsApp me advanced visibility settings.
3. Button on desktop.
4. Call to action on desktop.
5. Button and call to action on mobile.

== Changelog ==

= 2.0.0 =
* **NEW: Advanced visibility settings to define where to show *WhatsApp me* button.**
* **NEW:** WooCommerce integration.
* UPDATED International Telephone Input library to v.13.
* Minor fixes on fields cleanup and other improvements.

= 1.4.3 =
* NEW support for Google Analytics Global Site Tag (gtag.js).
* CHANGE events label now is the destination URL to match general behavior.
* UPDATED International Telephone Input library

= 1.4.2 =
* FIX JavaScript error introduced on v1.4.1.

= 1.4.1 =
* Fix JS frontend sometimes can't load WhatsApp me settings.
* Fix better Google Analitycs event tracking when leave page.

= 1.4.0 =
* **NEW:** Added the option to define the first message to send. You can include variables such as {SITE}, {URL} or {TITLE}.
* Fix PHP notice when global $post is null (e.g. search results or login page).

= 1.3.2 =
* Only set admin/public hooks when it corresponds to improve performance and fix a notice on admin.

= 1.3.1 =
* Fix fatal error when the PHP mbstring extension is not active

= 1.3.0 =
* Added option to change position of button to left
* Added formatting styles for Call to action text like in WhatsApp: *italic* **bold** strikethrough

= 1.2.0 =
* Added International Telephone Input for enhanced phone input
* Phone number is cleared to generate correct WhatsApp links

= 1.1.0 =
* Added posts/pages option to override CTA or hide button
* Don't enqueue assets if not show button
* Added filters for developers

= 1.0.3 =
* Readme texts

= 1.0.2 =
* Fix plugin version

= 1.0.1 =
* Fix text domain

= 1.0.0 =
* First version
