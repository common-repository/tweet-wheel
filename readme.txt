=== Plugin Name ===
Contributors: NerdCow
Tags: automated tweeting, posting to twitter, scheduled tweeting, increasing traffic, social share, auto publish
Requires at least: 4.4
Tested up to: 4.9.1
Stable tag: 1.1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automated & scheduled tweeting using your WordPress content.

== Description ==

**Tweet Wheel** plugin helps you to share your WordPress posts, pages, products and any post type on Twitter automatically without your supervision. Promote your WordPress website on Twitter by tweeting regularly to earn more followers and drive more traffic.

[Live Demo](http://tweet-wheel.com/live-demo/)*

_*demo shows the pro version of the plugin, some features aren't available in the free one, so please read the list below_


**Current features**

* Automated queueing system, which is the core of the plugin. It handles all the automation.
* Multi-templating for posts helps you to specify limitless amount of tweet variations for each post.
* Advanced scheduling gives you more control over time of tweetings. Specify days and times at which you want your post published.
* Handling of custom post types - fully compatible with woocommerceshop products!
* Customising the queue let's you to supervise the order in which posts are tweeted.
* Looping is optional, but very useful. If on, it will automatially append just tweeted post at the end of queue. Keeps going infinitely this way.
* Pausing and resuming queue comes useful when you need a bit more control. No need to deactivate the plugin to put it on hold.
* Convenient bulk actions - queue, dequeue and exclude multiple posts at once.
* Simple view which minifies the queue look so you can fit more items on your screen - helpful for shuffling!
* Health check tab that let's you know if your website is ready for Tweet Wheel and what to fix.
* And much more...

[Online Documentation](https://tweet-wheel.com/docs/)

#### Upgrade to PRO 

**We have just empowered the PRO version with many amazing changes suggested by our community. Make sure to check out the demo link above and our website for more information!**

* One-click Twitter authorisation - no need for Twitter Developer account!
* Unlimited queues
* New type of scheduling by specific date
* Attach multiple media per template
* Use your favorite domain for **shortening URLs** (by Bit.ly)
* Tweet on demand
* E-mail notifications about occurred events
* History log telling you what was happening within a queue
* User role management - restrict access to the plugin by a role
* Enjoy the **mobile-friendly** interface
* Fill up the queue using **filtering by date range, amount and post type**
* Plenty improvements which overally boost user experience and easy of use
* **Premium support**

[CLICK HERE TO UPGRADE](http://tweet-wheel.com/pricing/)

[CLICK HERE TO JOIN THE AFFILIATES PROGRAMME AND EARN MONEY](http://tweet-wheel.com/affiliates/)

If you have a suggestion for improvement or a new feature, feel free to use [the Support forum](https://tweet-wheel.com/support/forum/tweet-wheel/).

Want regular updates? Follow us on [Twitter](https://twitter.com/tweetwheelwp/) 

== Installation ==

**Minimum requirements**

* WordPress 4.4
* PHP version 5.4
* MySQL version 5.0 or greater
* WP Cron enabled in your WordPress installation
* Twitter application with read & write permissions

There are two ways to install Tweet Wheel

### via WordPress plugin upload - automated
Navigate to Plugins > Upload New Plugin and upload the zip file you have downloaded. Voilla!

### via FTP client - manual
1. Unzip the zip file you have downloaded
1. Upload unzipped folder `tweet-wheel` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Tweet Wheel > Authorize, provide required details and authorize our plugin to access your Twitter acount

== Frequently Asked Questions ==

[https://tweet-wheel.com/docs/](https://tweet-wheel.com/docs/)

== Screenshots ==

[http://tweet-wheel.com/screenshots/](http://tweet-wheel.com/screenshots/)

== Changelog ==

= 1.1.6 - 08/10/2018 =
* Added - Compatibility with WordPress up to 5.0

= 1.1.5 - 04/11/2017 =
* Changed - Increased character limit to 280 characters

= 1.1.4 - 04/11/2017 =
* Added - WP 4.9.1. compatibility

= 1.1.3 - 11/08/2017 =
* Fixed - remove HTML tags from the post title
* Added - WP 4.8.1 compatibility

= 1.1.2 - 24/07/2017 =
* Fixed - character counter for templates with images

= 1.1.1 - 28/06/2017 =
* Fixed - various bugs

= 1.1.0 - 13/06/2017 =
* Added - compatibility with WP 4.8

= 1.0.9 =
* Fixed - templates weren't rotating correctly when following the order

= 1.0.8 =
* Eliminated redirection while attempt to edit a post, which doesn't support excerpts 

= 1.0.7 =
* Bug fixes

= 1.0.6 =
* Added compatibility with WP 4.6

= 1.0.5 =
* "Tweet Now" error popup will now display a meaningful message returned from Twitter

= 1.0.4 =
* Fixed weekly schedule to correctly save and display created times for each day
* Added compatiblity with WordPress 4.5

= 1.0.3.3 =
* Important vulnerability fix

= 1.0.3.2 =
* Fixed a few bugs
* Revised the code

= 1.0.3.1 =
* Fixed a fatal error for new users upon the plugin activation

= 1.0.3 =
* Fixed: JS conflicts with Customiser
* Fixed: Widget not reflecting settings on the front-end
* Fixed: Slow loading of the Queues screen by replacing JavaScript tab switching with page reloading
* Fixed: Broken widget layout after real-time refresh
* Fixed: Widget not caching tweets after real-time refresh
* Fixed: High usage of memory and other server resources resulting in slow admin panel
* Fixed: Window not scrolling to invalid tweet templates on the post edit screen

* Added: Ability to generate times at fixed intervals within the weekly schedule
* Added: Ability to copy times from other days within the weekly schedule
* Added: Ability to clear times within the weekly schedule
* Added: WP Pointers showing new users next steps when using the plugin for the first time
* Added: New template tag {{EXCERPT}} which will use the default Excerpt field
* Added: Template tags such as {{TITLE}} and {{EXCERPT}} will refresh it's values on-fly keeping character counters up-to-date
* Added: Admin toolbar dropdown for easy access to queues

* Other: Hid WP admin footer from plugin's admin pages
* Other: Centralised the authorisation form on the screen
* Other: Code improvements

= 1.0.2 =
* Fixed an internal error ocurring when tweet template was too long causing entire queue to malfunction
* Eliminated all notices and warnings in the debug mode
* Tidied up code to avoid redundant assets
* Fixed a couple of bugs which could have caused queue to post duplicated content on user's Twitter wall

* Added compatibility with PHP7

= 1.0.1 = 
* Fix for plugin tweeting over and over the same tweet without acknowledging the schedule

= 1.0 =
* Major plugin overhaul.

= < 1.0 =
Please refer to this link for history of changes prior to 1.0 version [https://nerdcow.co.uk/doc/tweet-wheel/updates-support/tweet-wheel-changelog/](https://nerdcow.co.uk/doc/tweet-wheel/updates-support/tweet-wheel-changelog/)