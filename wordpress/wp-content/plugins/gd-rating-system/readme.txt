=== GD Rating System ===
Contributors: GDragoN
Donate link: https://plugins.dev4press.com/gd-rating-system/
Version: 2.3
Codename: Phoebe
Tags: rating, stars, stars rating, star rating, rate posts, rate, vote
Requires at least: 4.4
Requires PHP: 5.5
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Powerful, highly customizable and versatile ratings plugin to allow your users to vote for anything you want.

== Description ==
GD Rating System is the successor to GD Star Rating plugin, but it has nothing in common with the old plugin. GD Rating System uses modular structure with most features split into addons and rating methods. For front end display plugin uses templates similar to WordPress theme templates and allows you to override existing templates or add new ones.

= Overview of plugin features =
With GD Rating System you can rate anything. Plugin supports several basic rating entities and for each one you can have more than one rating type. This includes posts with all default and custom post types, comments, users, terms for default or custom taxonomies.

Here is the list of most important plugin features:

* Rating Method: Stars Rating
* Addon: Posts Integration
* Addon: Comments Integration
* Addon: Dynamic Load
* Addon: Rich Snippets Support
* Addon: Feeds Support (v1.3)
* Addon: Shortcake UI Plugin Support (v1.4)
* Presentation: Default set of templates
* Presentation: Shortcode for rating block
* Presentation: Shortcode for rating list
* Widget: Shortcode for rating list
* Posts Addon: Sort by rating (v1.2)
* Comments Addon: Sort by rating (v1.2)
* Stars Rating: 20 icons in a font
* Stars Rating: 2 image based sets
* Administration: Rating objects log
* Administration: Votes log
* Administration: Custom rating rules
* Data Transfer: Import from GD Star Rating
* Data Transfer: Import from WP PostRatings
* Data Transfer: Import from Yet Another Stars Rating
* Data Transfer: Import from KK Star Ratings (v1.3)

= Upgrade to GD Rating System Pro =
Pro version contains many more great features:

* Rating Method: Emote This (v1.4)
* Rating Method: Like This (v1.2)
* Rating Method: Stars Review
* Rating Method: Thumbs Rating
* Addon: WP REST API Plugin Support
* Addon: Dummy Ratings
* Addon: Edit Rating Votes
* Addon: GEO Location for Votes
* Addon: Admin Enhancer
* Addon: bbPress Integration (v1.1)
* Addon: BuddyPress Integration (v2.0)
* Addon: Bayesian Calculation (v1.1)
* Addon: Client Detection (v1.3)
* Addon: Tooltip (v2.0)
* Date based period queries (v2.2)
* Aggregate ratings (v2.2)
* Posts Addon: Auto sort by rating (v1.2)
* bbPress Addon: Rating topics views (v1.2)
* Font Icons: FontAwesome (v1.3)
* Stars Rating: 4 extra image sets
* Thumbs Rating: 4 font icons sets in a font
* Thumbs Rating: 1 image based set
* Like This Rating: 2 font icons sets in a font
* Like This Rating: 1 image based set
* Emote This Rating: 2 image based sets
* PSD Source Files for all image sets
* Presentation: Additional Templates
* Presentation: Additional Widgets
* List Shortcode: Filter by terms and authors (v1.3)
* List Widget: Filter by terms and authors (v1.3)

With more features on the roadmap exclusively for Pro version.

* More information about [GD Rating System Pro](https://plugins.dev4press.com/gd-rating-system/)
* Premium addons for [GD Rating System Pro](https://plugins.dev4press.com/gd-rating-system/addons/)
* Compare [Free vs. Pro Plugin](https://plugins.dev4press.com/gd-rating-system/free-vs-pro-plugin/)

Premium addons:

* Multi Rating Addon
* User Reviews Addon
* Code Builder Addon (free with GD Rating System Pro license)
* Comments Form Addon (free with GD Rating System Pro license)

Premium graphics packs:

* Halloween Pack
* Christmas Pack
* Emoji Pack (free with GD Rating System Pro license)

= Documentation and Support =
You need to register for free account on [Dev4Press](https://www.dev4press.com/):

* [Frequently Asked Questions](https://support.dev4press.com/kb/product/gd-rating-system/faqs/)
* [Knowledge Base Articles](https://support.dev4press.com/kb/product/gd-rating-system/articles/)
* Support Forum: [Free](https://support.dev4press.com/forums/forum/plugins-lite/gd-rating-system-lite/) & [Pro](https://support.dev4press.com/forums/forum/plugins/gd-rating-system/)

== Installation ==
= General Requirements =
* PHP: 5.5 or newer

= PHP Notice =
* Plugin should work with PHP 5.3 and 5.4, but these versions are no longer used for testing, and they are no longer supported.
* Plugin doesn't work with PHP 5.2 or older versions.

= WordPress Requirements =
* WordPress: 4.4 or newer

= WordPress Notice =
* Plugin should work with WordPress 4.0, 4.1, 4.2 and 4.3, but these versions are no longer used for testing, and they are no longer supported.
* Plugin doesn't work with WordPress 3.9 or older versions.

= Basic Installation =
* Plugin folder in the WordPress plugins folder must be `gd-rating-system`.
* Upload `gd-rating-system` folder to the `/wp-content/plugins/` directory.
* Activate the plugin through the 'Plugins' menu in WordPress.
* Plugin adds top level menu 'Rating System' where you can start using it.

== Frequently Asked Questions ==
= Does plugin works with WordPress MultiSite installations? =
Yes. Each website in network can activate and use plugin on it's on.

= Can I translate plugin to my language? =
Yes. POT file is provided as a base for translation. Translation files should go into Languages directory. You can join WordPress Translation project and translate online: [GD Rating System Translations](https://translate.wordpress.org/projects/wp-plugins/gd-rating-system/).

= How can I upgrade to Pro version? =
You need to buy Pro plugin license from plugin [home page](https://plugins.dev4press.com/gd-rating-system/), and download Pro plugin and replace free plugin with Pro plugin. All data and settings will be used by Pro version, no data will be lost.

= What about the support for cache plugins? =
Plugin uses WordPress internal object cache to store data about rating items. And, it also includes Dynamic Load addon that will load rating blocks through AJAX. This addon is disabled by default, and if you use cache plugins, just enable this addon from plugin Settings -> Extensions panel.

= Is the GD Rating System compatible with GD Star Rating? =
No. GD Rating System is completely new and different plugin, that has nothing in common with old GD Star Rating plugin. You can transfer data from GD Star Rating to the GD Rating System.

= Can I import data from other rating plugins? =
Yes. GD Rating System supports import of rating data from: GD Star Rating, WP PostRatings, Yet Another Stars Rating, KK Star Ratings. PRO version of GD Rating System can import thumbs and review ratings too.

= Can I use shortcodes to add ratings to posts? =
Yes. There are several shortcodes available. You can get more information in the knowledge base. Or, you can use Shortcake Shortcode UI plugin, and add and edit shortcodes visually from the editor without the need to learn shortcodes attributes.

= Can GD Rating System work with cache plugins? =
Yes. Plugin includes Dynamic Load addon that will load rating blocks through AJAX. This addon is disabled by default, and if you use cache plugins, just enable this addon from plugin Settings -> Extensions panel.

Find more FAQ here: [Frequently Asked Questions](https://support.dev4press.com/kb/product/gd-rating-system/faqs/).

== Translations ==
* English

== Upgrade Notice ==
= 2.3 =
Rating accessibility. Core loop object. Improved dynamic load. Improved transfer. Database cache. A lot of improvements, updates and fixes.

== Changelog ==
= 2.3 - 2017.12.06 =
* New: core loop object for easier templates control
* New: additional abstract classes for some plugin objects
* New: dynamic load addon: performance optimizations
* New: dynamic load addon: render multiple blocks at once
* New: transfer: option to control records to process per call
* New: transfer: various improvements to the transfer script
* New: stars rating: accessibility for the rating block
* New: additional abstract classes for some plugin objects
* New: all templates modified to use new loop object
* New: base widget class with improved code sharing
* New: database table: for storing cached data
* New: database cache handling object
* New: posts sorting: added parameter for minimal rating
* New: comments sorting: added parameter for minimal rating
* Edit: stars rating: various rendering improvements
* Edit: stars rating: various JavaScript improvements
* Edit: changed order of the plugin initialization on admin side
* Edit: posts sorting: various minor updates
* Edit: comments sorting: various minor updates
* Edit: improvements to the rating method load classes
* Edit: various improvements to the votes and rating logs
* Edit: d4pLib 2.2.1
* Fix: dynamic load addon: problems with addon initialization
* Fix: posts sorting: problem with the ASC sort order
* Fix: lists rendering: missing default query elements
* Fix: lists rendering: shows ratings for deleted posts
* Fix: user rating object: init problem for non-logged visitors
* Fix: query object: problem with limit and offset arguments
* Fix: query object: problem with filtering the taxonomy entities

= 2.2 - 2017.05.17 =
* New: dynamic load: filter to control loading
* New: admin interface is now fully accessible
* New: widgets tabbed interface using ARIA markup
* Edit: widgets interface using proper HTML input types
* Edit: main rating query object perfromance improvements
* Edit: improvements with the font handling widget settings
* Edit: various improvements to the plugin core
* Edit: d4pLib 1.9.6
* Fix: stars rating: division by zero error when removing votes
* Fix: widgets font settings problems with some rating methods
* Fix: problem with custom settings rule loading
* Fix: small issue with the global post set to null

= 2.1.1 - 2017.03.04 =
* Edit: d4pLib 1.9.0.1
* Fix: custom settings rules get deleted on plugin update
* Fix: minor issue in the function to load templates

= 2.1 - 2017.02.24 =
* New: stars rating: hidden input field and passive mode
* New: rating item: method to get all data for a method
* New: filter to register additional widgets
* New: filter to provide extra templates storage locations
* New: filters for getting external methods templates
* New: font base class updated to support new rating methods
* New: more actions and filters in the main JavaScript file
* Edit: posts: improved inserting of the rating block
* Edit: all main form files preventing direct loading
* Edit: improved rating item data validation
* Edit: improved validation for the post rating rendering function
* Edit: minor improvements to data validation for all methods
* Edit: additional optimization for main JavaScript file
* Edit: code optimization for several core classes
* Edit: many improvements to the main CSS stylesheet
* Edit: various small improvements to admin side panels
* Edit: d4pLib 1.9
* Fix: XSS security issue with the log.php form file
* Fix: posts addon: triggered by home page queries in some cases
* Fix: main log item meta database method missing unserialization
* Fix: few problems with badge/symbol rendering
* Fix: php warning in the rich snippet addon

= 2.0.2 - 2017.01.27 =
* Edit: d4pLib 1.8.9
* Fix: multisite issue with the blog switching functions
* Fix: multisite issue with deletion of the blog tables

= 2.0.1 - 2017.01.03 =
* Edit: display error messages in the rating block
* Edit: improvements in the way AJAX response handles errors
* Edit: improvements to main AJAX error response handling
* Fix: rich snippets: problem with adding snippets in some cases

= 2.0 - 2016.12.30 =
* New: transfer: rewritten to use threaded AJAX based transfer
* New: debug: option to force loading of source JS and CSS files
* New: rich snippet: support for JSON-LD snippet format
* New: rating items log: filters and query expanding for the log
* New: multisite: remove tables for deleted blogs
* New: database: function to get list of users who votes for an item
* New: javascript: improved method for handling AJAX errors
* New: debug: log AJAX error to console, display alert or hide
* New: many small updates to improve plugin extensibility
* New: all rating methods render text: before and after wrapper
* New: theme overridable functions to render various elements
* New: several new functions for dealing with rating methods
* Edit: transfer: various speed optimizations and improvements
* Edit: shortcake addon: improved code organization
* Edit: shortcake addon: improvements to the editor elements display
* Edit: shortcake addon: display wrapper and block CSS class options
* Edit: metabox: updated interface for better usability
* Edit: dynamic load: check if the rendering is inside the feed
* Edit: many improvements to core methods objects
* Edit: many improvements to the addon architecture
* Edit: many updates to the core objects initialization
* Edit: few improvements to the administration panels
* Edit: few improvements organization of the plugin code
* Edit: d4pLib 1.8.7
* Edit: updated plugin system requirements
* Fix: shortcake addon: wrong options for the stars review shortcodes
* Fix: shortcake addon: showing unsupported shortcode attributes
* Fix: votes log: display of some rating items titles
* Fix: votes log: filter user ID set to zero (visitors)
* Fix: shortcodes: few issues with preprocessing of attributes
* Fix: stars rating: problem with formatting rating value
* Fix: stars rating: few issues with rendering of votes text
* Fix: stars rating: list widget template missing list item classes
* Fix: dynamic load: doesn't take into account the feed
* Fix: transfer: few problems with YASR plugin data transfer
* Fix: transfer: many warnings happening during the transfer
* Fix: transfer: missing item preparation for saving transferred data
* Fix: translation missing for the word 'ago' for rating text
* Fix: issues with scanning for theme override templates
* Fix: rating query object: wrong sorting by rating

= 1.4 - 2016.10.23 =
* New: addon: Shortcake UI Plugin Support
* New: administration: panel for managing rating entities and types
* New: rules panel: buttons for delete and activation of rules
* New: rules panel: color coding for list of available rules
* New: rules panel: confirmation dialog for deleting the rule
* New: votes log: filters and query expanding for the log
* New: stars rating: translation templates for rendering blocks
* New: database: new column for 'series' to the items table
* New: database: indexing more columns in the items table
* New: debug: embed SQL query for the rating lists
* New: protect entities and types array from tampering
* New: update plugin panel rechecks for broken settings
* Edit: votes log: refactored SQL query to get log items
* Edit: plugin now uses d4pLib settings core class
* Edit: refactored access to entities array to use access functions
* Edit: refactored main rendering JavaScript file
* Edit: changes in the way JavaScript files are compressed
* Edit: improvements to rating method core classes
* Edit: improvements to generated inline CSS for font icons
* Edit: improvements to rating method core classes
* Edit: d4pLib 1.8.3
* Edit: removed some outdated code from rating methods
* Fix: annonymous verify option can cause broken SQL log queries
* Fix: some votes log URL's were not working in every case
* Fix: missing sanitation of the settings for some operation
* Fix: few minor issues with the rendering objects

= 1.3.1 - 2016.09.29 =
* Edit: d4pLib 1.8.2
* Fix: removal of rating items not working in some cases

= 1.3 - 2016.08.05 =
* New: addon: Feeds - for RSS, AMP, FIP, ANF integration support
* New: font icons: support for adding custom icon fonts
* New: font icons: 10 more icons in default icons font
* New: rewriten handling for the font icons
* New: allow or prevent authors to vote for own rating items
* New: improved sanitation of plugins settings on save
* New: transfer data from KK Star Ratings plugin
* New: actions run when voting items data is saved to the database
* New: filter to modify votes meta data added to the database
* New: ratings and votes grids allow for adding of new columns
* New: support for IP detection when behind CloudFlare
* Edit: improved sanitation of plugins settings on save
* Edit: improved buttons on all dialogs on the admin side
* Edit: many improvements in handling shortcodes and widgets
* Edit: ratings grid proper use of the rating item objects
* Edit: many small improvements to the JavaScript code
* Edit: d4pLib 1.7.8
* Fix: few small issues when the user agent is not set
* Fix: saving option to disbale custom settings rule not working
* Fix: few minor styling issues related to rating lists
* Fix: warnings generated by the missing style type and/or name
* Fix: logs display warnings with post types are missing
* Fix: small issue with type option in the shortcodes processing
* Fix: duplicated classes added for star rating font icons styles
* Fix: minor problems in applying default styles for widgets
* Fix: few minor problems with the main JavaScript file
* Fix: warnings related to query object SQL query in some cases
* Fix: wrong attribute name for item ID for some shortcodes

= 1.2 - 2016.05.10 =
* New: posts addon: sort posts by rating
* New: posts addon: additional control filters
* New: comments addon: sort posts by rating
* New: comments addon: additional control filters
* New: query engine: posts filter by author, meta and terms
* New: query engine: comments filter by author and meta
* New: query engine: terms filter by meta
* New: query engine: users filter meta
* New: debug information added for shortcodes and widgets
* New: separate security and debug settings panels
* New: settings panel now uses dividers for settings groups
* Edit: posts addon: do not embed if inside the rss feed
* Edit: new settings and updates for rendering of rating blocks
* Edit: protect expanded rendering functions
* Edit: extra information for transfer data panels
* Edit: check if method is active for transfer data panels
* Edit: minor updates to some of the templates
* Edit: some minor improvements on the admin side interface
* Edit: d4pLib 1.6.9
* Fix: minor issue with the GD Star Rating transfer process
* Fix: comments addon: rating display when no comment is detected
* Fix: posts addon: rating display when no comment is detected
* Fix: small issue with the d4pLib shortcodes class

= 1.1.1 - 2016.01.23 =
* New: front page knowledge base and support links
* Edit: check for rating method validity before render
* Edit: few minor updates to the default styling
* Fix: templates in theme subfolder 'gdrts' not loaded

= 1.1 - 2016.01.18 = 
* New: stars rating: set custom colors for font icons based stars
* New: daily maintenance background job
* New: background job: calculate rating type based statistics
* New: background job: recalculate ratings on stars number change
* Edit: rating query object allows filtering of query elements
* Edit: main posts metabox improves default filters and actions
* Edit: font icons: includes spinner icon and some extra classes
* Edit: font icons: font files included with plugin version parameter
* Edit: posts integration addon shows help for the addon
* Edit: posts integration addon hides bbPress post types
* Edit: d4pLib 1.5.8
* Fix: comment rating type missing title when display in lists
* Fix: using FontAwesome spinner icon for loading message
* Fix: method and addon issues with missing custom rule values
* Fix: several smaller issues with the admin interface
* Fix: problems with rating lists ordering by votes

= 1.0.3 - 2016.01.04 = 
* Edit: some addons function moved into plugin core
* Edit: improvements to some rendering functions
* Edit: switched to new admin object base class from D4PLib
* Edit: d4pLib 1.5.6
* Fix: extensions panel allows for all methods to be disabled
* Fix: rich snippet addon: broken if selected method is disabled
* Fix: rating method dropdown lists show disabled methods

= 1.0.2.1 - 2015.12.29. =
* Fix: abstract method class inheritance problem

= 1.0.2 - 2015.12.28. =
* Edit: improvements to loading of the templates list
* Edit: d4pLib 1.5.5
* Fix: deleting custom rules not working
* Fix: showing disabled methods and addons on rules panel
* Fix: loading conflict with GD bbPress Toolbox Pro
* Fix: minor metabox initialization issue

= 1.0.1 - 2015.12.27. =
* New: metabox for posts to override embed settings
* Edit: plugin initialization priority changed

= 1.0 - 2015.12.25. =
* First official release

== Screenshots ==
1. Example rating block
2. Example rating block with votes distribution
3. Votes log
4. Stars rating method settings
5. Rich snippets settings
6. Example rating block with widget and shortcode lists
