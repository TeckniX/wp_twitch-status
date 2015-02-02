Twitch Status
=============

* Contributors: nicolas.bernier, TeckniX
* Tags: Twitch.tv, tag, AJAX, status, widget, thumbnail, multi-channel
* Requires at least: 3.0.1
* Tested up to: 3.9.1
* License: GPLv2 or later
* License URI: [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

Inserts Twitch.tv stream(s) status widget and tags for your blog menus, pages and whatever following jQuery selectors.

Description
===========

Inserts Twitch.tv stream(s) status tags in your blog. The tags just indicate if the stream is live with a blinking red cirle or offline. 

Also implements a simple widget showing the stream(s) status including the thumbnail, title, game name and number of viewers. 

The tags and the widget are updated every 30 seconds.

Support for multi-channel was added, allowing you to list multiple channels via the admin tool, by separating them with a comma (,). Further improvements would add a channel specific tag to each widget, allowing for more direct interaction if needed.

Installation
============

1. Download and unzip twitch-status archive contents to the `/wp-content/plugins/twitch-status` directory or add it using WordPress' plugin manager.
2. Activate the plugin through the 'Plugins' menu in WordPress

Configuration
=============

1. Go to *Settings* / *Twitch status*
2. Enter your channel name and the jQuery selectors matching the places you want to insert the tags.
3. Modify the 'cache' folder permissions within the twitch-status directory, allowing apache to write

Frequently Asked Questions
==========================

I want to add a stream status tag on my "Twitch" tab. How do I find the matching jQuery selector?
-------------------------------------------------------------------------------------------------

You can find the jQuery selector by using the browser developers tools (right click / inspect on element) to get the id and/or classes of the element. If you have an id, just prepend the # symbol to it and you have it. For example, if your element has ID `menu-item-582`, the jQuery selector would be `#menu-item-582`. If the menu element has a link inside it (`a` element), add the a element in the selector `#menu-item-582 a`.

If your element doesn't have an id but a class, use the class instead. The matching selector would have a `.` instead of a `#` (ie `.menu-item-582 a`).

Fore more information about jQuery selectors, check out http://api.jquery.com/category/selectors/ 

How to I add the stream status tag in a blog post or page?
----------------------------------------------------------

Just add the following HTML code anywhere you want one : `<span class="twitch-status-tag"></span>`

Screenshots
===========

1. The stream status tag when online and offline.
2. The stream status widget when online.
3. The stream status widget when offline.

Changelog
=========

### 1.2
* Added multi-stream status widget support

### 1.1
* Added stream status widget

### 1.0

* First release
