=== infoSNIPER ===
Contributors: connecticallc
Tags: geolocation, tracking
Requires at least: 3.0.1
Tested up to: 5.5
Stable tag: 1.3.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Track incoming IP Addresses and display information about them using infoSNIPER.net

== Description ==

infoSNIPER is the latest geolocation, mapping, and proximity search plugin for your WordPress site! 

The infoSNIPER plugin helps you understand where your website users are originating from. We do this by using our internal IP geolocation databases that let you precisely geotarget 99.9% of users traveling to your website.
We do NOT store any IP addresses parsed to us by the infoSNIPER plugin nor do we transmit or pass on the IP addresss to any third party service. All Geolocation results are processed on our servers. Once the infoSNIPER plugin does the call to our servers we simply parse back the GeoData to the plugin for storage on your WordPress server.
Our service can be found at [infoSNIPER.net](https://infosniper.net).
Use of this plugin to track geolocation data requires a infoSNIPER key. You can either [purchase])(https://infosniper.net/lb.php) one or try our [free trial](https://infosniper.net/free-trial.php) on our website.

infoSNIPER WP Plugin Features:

* Track all the users that visit your website
* View them by country, city, or state
* Custom display options (Last 100 users from last week, last month, last year, etc.)
* Sort by users, bots, potential threats, admins, or show all
* Detailed user-logs that find out all the details on your users (IP address, approximate location, internet provider, etc.)
* Built-in shortcodes that display various maps and information to your website users

This plugin serves as an interface for infoSNIPER.com's geolocation services. You purchase credits on infoSNIPER.com to access the functionality of this plugin.
This plugin records your user's IP Address and User Agent. It then uses our infoSNIPER API to provide you with the following information:

* Hostname
* Provider
* Country
* Country Code
* Flag
* State
* City
* Area Code
* Postal Code
* DMA Code
* Time Zone
* GMT Off Set 
* Continent
* Latitude
* Longitude
* Accuracy

It is important to note that the latitude and longitude are not exact positions. They represent the center of a circle with a radius in kilometers equal to the accuracy where we believe the user to have come from.

infoSNIPER Privacy Policy - https://infosniper.net/privacy-policy.php
infoSNIPER Terms of Service - https://infosniper.net/terms-conditions.php

== Installation ==

1. Upload the infoSNIPER folder to your wp-content/plugins/ directory.
2. Purchase or begin a free trial for an API Key.
3. Begin Tracking.

== Frequently Asked Questions ==

= Why do I have so many user's from other countries/regions? =
These are likely automated systems trying to get through the security on your wordpress site. We do our best to filter out bots to inform you, but it is difficult to identify bots when they do not explicity make it clear that they are bots.

== Changelog ==

= 1.3.0 =
* Implemented marker clustering for the dashboard map to improve load times and usability.
* Dashboard settings are now session specific and will not be saved when you leave the page.
* Parts of the user logs are now loaded dynamically when clicked on to improve initial load time.
* Added key validation to the settings menu.

= 1.2.4 =
* Fixed improper error handling that would result in error emails

= 1.2.3 =
* Fixed various bug where query errors would cause queries to cease.

= 1.2.2 =
* Fixed bug where plugin said you had no queries remaining in a valid key.
* Fixed map selection preview not working.
* Fixed invisible selection on radio menu in dashboard.
* Minor styling tweaks.

= 1.2.1 = 
* Updated logic on when a key runs out of queries
* Updated delete options functionality to ensure all options are removed correctly

= 1.2.0 = 
* Added delete and refresh buttons to user logs.
* Improved general performance and styling.

= 1.1.0 =
* Major style overhaul in user logs.

= 1.0.1 =
* Updated database logic.
* Added system for updating database from version to version to prevent loss of data.
* Added ability to change map settings on dashboard and otherwise filter.
* is-display_map now only show users with the 'User' user_type.
* Updated CSS in admin section.
* Miscellaneous code improvements.

= 1.0.0 =
* Initial Release of infoSNIPER.

== Screenshots ==
