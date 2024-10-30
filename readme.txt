=== LH Native Comments ===
Contributors: shawfactor
Donate link: 	   https://lhero.org/plugins/lh-native-comments/
Tags: comments, emails, comment, thml5, validate, mobile, localstorage
Requires at least: 4.0
Tested up to: 4.9
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make native WordPress comments better by using modern standards

== Description ==

Make comments better for visitors by modifying the comment form.

This plugin adds some small hacks around core WordPress comment forms including:

* Hide the url field (will be configurabel in future releases)
* Makes all fields html5 compliant.
* Make email, name and comment required fields on the front end via required attribute.
* Make the comment textarea expandable for better use of space.
* Hide the email and name fields until a visitor begins adding a comment.
* Saves unfinished comments in localstorage for repeat visitors.
* If the commenter doesn't exists saves their details in the database with an unclaimed user role
* Give each comment its own permalink


== Installation ==

**Install through your backend**

1. Search for "LH Native Comments", click install.
1. You're done.

**Install manually**

1. Download and unzip the plugin.
1. Upload the `lh-native-comments` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.


== Changelog ==

= 1.00 =
* Initial version.

**1.20 April 06, 2016** 
* Upgrade to 4.4.2 compliant

**1.30 April 06, 2016** 
* Imposter protection

**1.40 April 06, 2016** 
* Create unclaimed users

**1.50 April 16, 2016** 
* Added Comment permalinks

**1.60 June 06, 2016** 
* Better translation support

**1.70 June 22, 2016** 
* Add names on user creation

**1.71 June 22, 2016** 
* Cronned user created

**1.72 March 30, 2017** 
* Use isset

**1.73 May 10, 2017** 
* More code improvement

**1.74 October 18, 2017** 
* do_action user_register

**1.75 December 03, 2017** 
* work with wp event calender