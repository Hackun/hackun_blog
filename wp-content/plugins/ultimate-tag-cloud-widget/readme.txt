=== Ultimate Tag Cloud Widget ===
Contributors: exz
Tags: widget, tags, configurable, tag cloud
Requires at least: 2.8
Tested up to: 3.3
Stable tag: 1.3.12
Donate link: https://flattr.com/thing/112193/Ultimate-Tag-Cloud-Widget

This plugin aims to be the most configurable tag cloud widget out there, able to suit all your wierd tag cloud needs.

== Description ==

This is the highly configurable tag cloud widget, current version supports the following preferences:

* Multiple instances
* Choose which authors tags should be shown
* Ordering of the tags
* Exclude tags you don't want to show
* Include only the tags that you want to show
* Minimum amount of posts for tags to be included
* Number of days back to search for posts
* Title
* Size and color customization
* Max amount of tags in your cloud
* Spacing between tags, letters, words and rows
* Transform tags into lowercase, uppercase or Capitalize them 
* Can also show categories
* Fully internationalized and translated into two languages
* Separator, suffix and prefix for the tags
* Load/save configuration
* It also works with the [page tagging](http://wordpress.org/extend/plugins/page-tagger/) plugin

This plugin is under active development and my goal is to try to help everyone who have issues or suggestions for this plugin. If you have issues please post them in the forums, if you have suggestions I've got a new suggestion system up on my blog at http://0x539.se/wordpress/ultimate-tag-cloud-widget/. You're also always welcome to contact me by e-mail or Google Talk; rickard at 0x539.se

== Installation ==

This is the same procedure as with all ordinary plugins.

1. Download the zip file, unzip it 
2. Upload it to your /wp-content/plugins/ folder 
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Use the widgets settings page under 'Appearance' to add it to your page

All the configuration options is set individually in every instance. Some default values are set if you're unsure on how to configure it. 

If your theme doesn't use widgets, you can still use it in your theme by calling the function do_utcw(). See "Other Notes" for more information.

== Frequently Asked Questions ==

This is a new plugin, haven't had any questions yet. If you have any, be sure to send them to me. 

== Screenshots ==

1. This shows my widget with the default settings on the default wordpress theme.
2. This is a more colorful example with random colors and all tags in uppercase. I'd like to actually see someone use it like this. 
3. Maybe a more realistic usage of the widget with spanning colors and capitalized tags. 
4. The settings page of the widget

== Changelog ==

= 1.3.12 =
* Fixed bug which made the default "data" tab disappear when adding the widget to a sidebar
* Added setting for link hover font color
* Added option to save or load configuration

= 1.3.11 =
* Proper namespacing of the CSS classes to prevent interference with other plugins

= 1.3.10 = 

* Fixed shortcode problem where the content would appear at the top of a post/page instead of where the shortcode was placed.
* Fixed shortcode problem where you couldn't possibly enter some values as array types, now accepting a comma separated list for $tags_list, $color_set and $authors
* Updated spelling error in the documentation which caused some confusion

= 1.3.9 = 

* Added shortcode [utcw]

= 1.3.8 = 

* Improved the tabbed settings when using multiple tag clouds
* Improved the tabbed settings so that the same tab is reloaded after saving the settings
* Updated screenshot 
* Bugfix; The help texts now also shows after saving the settings
* Added a setting for separator, prefix and suffix

= 1.3.7 =

* Added more detailed descriptions of all the settings
* Added the tabs for the sections in the widget settings
* Switched from deprecated function get_users_of_blog() to get_users() for WP 3.1+

= 1.3.6 = 

* Added a setting for row spacing
* Added a setting for post age 

= 1.3.5 = 

* Now also showing private posts when signed in. 

= 1.3.4 = 

* Added support for [page tagging](http://wordpress.org/extend/plugins/page-tagger/) (thanks again Andreas Bogavcic)
* Added a setting for including debug information to help troubleshooting 

= 1.3.3 = 

* Added new styling options upon requests from the forum
* Testing out the new HTML5 input type "number" in the settings form

= 1.3.2 = 

* Fixed bug in the SQL query making the plugin also count posts that isn't published
* Added a new option to set the minimum amount of posts a tag should have to be included

= 1.3.1 = 

* Added Swedish translation
* Minor internationalization changes

= 1.3 = 

* As requested, support for calling a function to display the widget was added. Se other notes for information on how to use it.  
* Javascript changes in order to fix problems with the options page in WP 3.1 beta 1

= 1.2 = 

* Removed all the PHP short tags
* Can now sort by name, slug, id or color (!) case sensitive or case insensitive
* Exclude now takes either tag name or id 

= 1.1 = 

* Fixed bug with options page 
* Improved link generation to create correct tag links  

= 1.0 =

* Initial release 

== Upgrade Notice ==

= 1.3.12 =

* Minor bug fix and added support for saving/loading configurations.

= 1.3.11 =

* Minor CSS fix

= 1.3.10 = 

* Some shortcode bugfixes

= 1.3.9 =

* Added shortcode

= 1.3.8 = 

* Minor bug fixes from previous version
* Added separator, prefix and suffix settings

= 1.3.7 = 

* Removed deprecated function get_users_of_blog() for WP 3.1+

= 1.3.6 = 

* Added two new features; post age and row spacing

= 1.3.5 = 

* Now also showing private posts when signed in. 

= 1.3.4 = 

Support for page tagging and an option for debug information

= 1.3.3 =

* New styling options added

= 1.3.2 =

* Small bug fix in the SQL-query and a new option added

= 1.3.1 =

* Added Swedish translateion

= 1.3 = 

* Support for integrating the widget within your theme added. 
* New javascript fixing problem with options page in WP 3.1 beta 1 

= 1.1 and 1.2 =

* Just bug fixes, should be safe to upgrade. 

= 1.0 =

* Initial release

== Feedback ==

This plugin is under active development and my goal is to try to help everyone who have issues or suggestions for this plugin. If you have issues please post them in the forums, if you have suggestions I've got a new suggestion system up on my blog at http://0x539.se/wordpress/ultimate-tag-cloud-widget/. If you use this plugin and like it, please consider giving me some [flattr love](https://flattr.com/thing/112193/Ultimate-Tag-Cloud-Widget).

My contact information is

* rickard (a) 0x539.se (email, gtalk, msn, you name it)
* [twitter.com/rickard2](http://twitter.com/rickard2)

== Theme integration / Shortcode == 

You can integrate the widget within your own theme even if you're not using standard wordpress widgets. Just install and load the plugin as described and use the function 

`<?php do_utcw($args); ?>`

...with $args being a array of key => value pairs for the options you would like to set. For example if you'd like to change the title of the widget:

`<?php 
$args = array( "title" => "Most awesome title ever" );
  
do_utcw( $args );
?>`

If you're not able to change your theme you can also use the shortcode `[utcw]` anywhere in your posts or pages. You can pass any of the settings along with the shortcode in the format of key=value, for instance if you'd like to change the widget title:

`[utcw title="Most awesome title ever"]` 

This is the list of all the options that you can set, which works both in the shortcode and the function call.

* before_widget (string)
* after_widget (string)
* before_title (string)
* after_title (string)
* title (string)
* size_from (integer)
* size_to (integer)
* max (integer)
* reverse (boolean)
* authors (array of user IDs or CSV, integers)
* tags_list (array of taxonomies or CSV, IDs or names to be included or excluded)
* tags_list_type (include or exclude, defines how to handle the tag_list)
* minimum (integer)
* days_old (integer)
* page_tags (boolean)
* color_set (array of hex colors or CSV, like #fff or #ffffff)
* letter_spacing (integer)
* word_spacing (integer)
* tag_spacing (integer)
* line_height (integer)
* color_span_from (hex color, like #fff or #ffffff)
* color_span_to (hex color, dito)
* case_sensitive (boolean)
* order (string, valid values: 'random', 'name', 'slug', 'id', 'color', 'count')
* taxonomy (string, valid values: 'post_tag', 'category')
* color (string, valid values: 'none', 'random', 'set', 'span')
* case (string, valid values: 'lowercase', 'uppercase', 'capitalize', 'off')
* show_title (boolean)
* link_bold (string, valid values: 'yes', 'no', 'default')
* link_underline (string, valid values: 'yes', 'no', 'default')
* link_italic (string, valid values: 'yes', 'no', 'default) 
* link_bg_color (hex color)
* link_border_color (hex color)
* link_border_style (string, valid values: 'none', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset')
* link_border_width (integer)
* hover_bold (string, valid values: 'yes', 'no', 'default')
* hover_underline (string, valid values: 'yes', 'no', 'default')
* hover_italic (string, valid values: 'yes', 'no', 'default) 
* hover_bg_color (hex color)
* hover_border_color (hex color)
* hover_border_style (string, valid values: 'none', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset')
* hover_border_width (integer)
* debug (boolean)
* separator (string)
* prefix (string)
* suffix (string)
* return (boolean) 

All options are optional, default values can be found in the widget php file. Valid values for order, taxonomy, color and case can be found in the arrays $utcw_allowed_orders, taxonomys, colors and cases respectively.

Good luck and remember to give me feedback if you run into any problems

== Thanks == 

The power of the open source community is being able to help out and submitting patches when bugs are found. I would like to thank the following contributors for submitting patches and helping out with the development: 

* Andreas Bogavcic
* Fabian Reck

With your help this list will hopefully grow in the future ;)
