=== Import Listings into WP Job Manager ===
Contributors: soflyy, wpallimport
Tags: Tags: wp job manager, import listings, import job listings, import directory, job directory, import job directory, wp job manager, import wp job manager, import wp job manager listings, import job board, job board
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 6.6
Stable tag: 1.2.1

Easily import job listings from any XML or CSV file to the WP Job Manager plugin with the WP Job Manager Add-On for WP All Import.

== Description ==

The WP Job Manager Add-On for [WP All Import](https://wordpress.org/plugins/wp-all-import/ "WordPress XML & CSV Import") makes it easy to bulk import your job listings to the WP Job Manager plugin in less than 10 minutes.

The left side shows all of the fields that you can import to and the right side displays a job from your XML/CSV file. Then you can simply drag & drop the data from your XML or CSV into the WP Job Manager fields to import it.

The importer is so intuitive it is almost like manually adding a job opening in WP Job Manager.

https://www.youtube.com/watch?v=foL8u1ObaPY

= Why you should use the WP Job Manager Add-On for WP All Import =

* Instead of using the Custom Fields section of WP All Import, you are shown the fields like Listing Expiry Date, Company Name, etc. in plain English.

* Automatically find the job location just like manually adding a job.

* Full support for WP Job Manager's job search dropdowns, categories, regions, settings, and fields.

* Supports files in any format and structure. There are no requirements that the data in your file be organized in a certain way. CSV imports into WP Job Manager is easy, no matter the structure of your file.

* Supports files of practically unlimited size by automatically splitting them into chunks. WP All Import is limited solely by your server settings.

= WP All Import Professional Edition =

The WP Job Manager Add-On for WP All Import is fully compatible with [the free version of WP All Import](https://wordpress.org/plugins/wp-all-import/ "WordPress XML & CSV Import").

However, [the professional edition of WP All Import](https://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=wpjm) includes premium support and adds the following features:

* Import files from a URL: Download and import files from external websites, even if they are password protected with HTTP authentication. 

* Cron Job/Recurring Imports: WP All Import Pro can check periodically check a file for updates, and add, edit, delete, and update your job listings.

* Custom PHP Functions: Pass your data through custom functions by using [my_function({data[1]})] in your import template. WP All Import will pass the value of {data[1]} through my_function and use whatever it returns.

* Access to premium technical support.

[Upgrade to the professional edition of WP All Import now.](https://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=wpjm)

= Developers: Create Your Own Add-On =
This Add-On was created using the [Rapid Add-On API](https://www.wpallimport.com/documentation/addon-dev/overview/) for WP All Import. We've made it really easy to write your own Add-On. 

Don't have time? We'll write one for you.

[Read more about getting an Add-On made for your plugin or theme.](https://www.wpallimport.com/developers/)

= Related Plugins =
[Import Listings into Inventor WP](https://wordpress.org/plugins/import-xml-csv-listings-to-inventor-wp/) 
[Import Listings into the Jobify Theme](https://wordpress.org/plugins/jobify-xml-csv-listings-import/) 
[Import Listings into the Listify Theme](https://wordpress.org/plugins/listify-xml-csv-listings-import/)
[Import Listings into the Listable Theme](https://wordpress.org/plugins/import-xml-csv-listings-to-listable-theme)

== Installation ==

First, install [WP All Import](https://wordpress.org/plugins/wp-all-import/ "WordPress XML & CSV Import").

Then install the WP Job Manager Add-On.

To install the WP Job Manager Add-On, either:

* Upload the plugin from the Plugins page in WordPress

* Unzip import-property-listings-into-wp job manager.zip and upload the contents to /wp-content/plugins/, and then activate the plugin from the Plugins page in WordPress

The WP Job Manager Add-On will appear in the Step 3 of WP All Import.

== Frequently Asked Questions ==

= WP All Import works with any theme or plugin, so what’s the point of using the WP Job Manager Add-On? =

Aside from making your import easier and simpler, the WP Job Manager Add-On will fully support WP Job Manager's various fields and image settings as well as allow you to easily import location data.

== Changelog ==

= 1.2.1 =
* bug fix: job post status setting is not respected.

= 1.2.0 =
* improvement: add WP-CLI support
* improvement: add failed geocoding explanations in import history log
* improvement: add history log entries for all updates
* improvement: update rapid-addon API
* improvement: use Google Maps API key from WP Job Manager settings if key unspecified in import template
* improvement: use "City, State" format when importing location from latitude/longitude coordinates
* improvement: set "menu_order" to -1 for featured jobs
* API: add filter wpai_wp_jobm_addon_enable_logs
* bug fix: PHP notices and warnings during imports
* bug fix: "No API Key" option for geocoding shouldn't be available (Google requires an API key)
* bug fix: jobs with listing expiry date older than today have an "active" status

= 1.1.0 =
* bug fix: warnings & notices in debug log
* improvement: update rapid add-on api

= 1.0.9 =
* Geolocation fix.

= 1.0.8 =
* Update rapid addon api to fix field mapping issues.

= 1.0.7 =
* Fixed expiry date in the case that no expiry date is imported.

= 1.0.6 =
* Added dedicated geolocation section

= 1.0.5 =
* Removed fields that are no longer available in WP Job Manager.

= 1.0.4 =
* Fixed field update permission check and updated rapid addon api.

= 1.0.3 =
* Update functions to avoid conflicts

= 1.0.2 =
* Update WP All Import add-on API

= 1.0.1 =
* Improve company video import
* Bug fixes

= 1.0.0 =
* Initial release on WP.org.

== Support ==

We do not handle support in the WordPress.org community forums.

We do try to handle support for our free version users at the following e-mail address:

E-mail: support@wpallimport.com

Support for free version customers is not guaranteed and based on ability. For premium support, purchase [WP All Import Pro](https://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=wpjm).
