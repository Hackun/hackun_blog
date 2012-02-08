<?php
/*
Plugin Name: Ultimate tag cloud widget
Plugin URI: http://www.0x539.se/wordpress/ultimate-tag-cloud-widget/
Description: This plugin aims to be the most configurable tag cloud widget out there, able to suit all your wierd tag cloud needs.
Version: 1.3.12
Author: Rickard Andersson
Author URI: http://www.0x539.se
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

// Default values if the widget isn't configured properly
DEFINE("UTCW_DEFAULT_TITLE",              __("Tag Cloud", 'utcw'));
DEFINE("UTCW_DEFAULT_ORDER",              "name");
DEFINE("UTCW_DEFAULT_SIZE_FROM",          10);
DEFINE("UTCW_DEFAULT_SIZE_TO",            30);
DEFINE("UTCW_DEFAULT_MAX",                45);
DEFINE("UTCW_DEFAULT_TAXONOMY",           "post_tag");
DEFINE("UTCW_DEFAULT_REVERSE",            false);
DEFINE("UTCW_DEFAULT_COLOR",              "none");
DEFINE("UTCW_DEFAULT_LETTER_SPACING",     "normal");
DEFINE("UTCW_DEFAULT_WORD_SPACING",       "normal");
DEFINE("UTCW_DEFAULT_CASE",               "off");
DEFINE("UTCW_DEFAULT_CASE_SENSITIVE",     false);
DEFINE("UTCW_DEFAULT_MINIMUM",		      1);
DEFINE("UTCW_DEFAULT_TAGS_LIST_TYPE",     "exclude");
DEFINE("UTCW_DEFAULT_SHOW_TITLE",         true);
DEFINE("UTCW_DEFAULT_LINK_UNDERLINE",     "default");
DEFINE("UTCW_DEFAULT_LINK_BOLD",          "default");
DEFINE("UTCW_DEFAULT_LINK_ITALIC",        "default");
DEFINE("UTCW_DEFAULT_LINK_BG_COLOR",      "transparent");
DEFINE("UTCW_DEFAULT_LINK_BORDER_STYLE",  "none");
DEFINE("UTCW_DEFAULT_LINK_BORDER_WIDTH",  "0px");
DEFINE("UTCW_DEFAULT_LINK_BORDER_COLOR",  "none");
DEFINE("UTCW_DEFAULT_HOVER_UNDERLINE",    "default");
DEFINE("UTCW_DEFAULT_HOVER_BOLD",         "default");
DEFINE("UTCW_DEFAULT_HOVER_ITALIC",       "default");
DEFINE("UTCW_DEFAULT_HOVER_BG_COLOR",     "transparent");
DEFINE("UTCW_DEFAULT_HOVER_COLOR",        "default");
DEFINE("UTCW_DEFAULT_HOVER_BORDER_STYLE", "none");
DEFINE("UTCW_DEFAULT_HOVER_BORDER_WIDTH", "0px");
DEFINE("UTCW_DEFAULT_HOVER_BORDER_COLOR", "none");
DEFINE("UTCW_DEFAULT_TAG_SPACING",        "auto");
DEFINE("UTCW_DEFAULT_PAGE_TAGS",          false);
DEFINE("UTCW_DEFAULT_DEBUG",			  false);
DEFINE("UTCW_DEFAULT_DAYS_OLD", 		  0);
DEFINE("UTCW_DEFAULT_LINE_HEIGHT",		  "inherit");
DEFINE("UTCW_DEFAULT_SEPARATOR",		  " ");
DEFINE("UTCW_DEFAULT_PREFIX", 			  "");
DEFINE("UTCW_DEFAULT_SUFFIX", 			  "");
DEFINE("UTCW_DEFAULT_BG_COLOR",			  "transparent");
		

// Allowed values for miscellanious options in the widget  
$utcw_allowed_orders          = array('random', 'name', 'slug', 'id', 'color', 'count');
$utcw_allowed_taxonomies      = array(); // Will be set dynamically at load
$utcw_allowed_colors          = array('none', 'random', 'set', 'span');
$utcw_allowed_cases           = array('lowercase', 'uppercase', 'capitalize', 'off');
$utcw_allowed_tags_list_types = array('exclude', 'include');
$utcw_allowed_booleans        = array('yes', 'no', 'default');
$utcw_allowed_border_styles   = array('none', 'dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset');

/**
 * Ultimate tag cloud widget class
 * @package UTCW
 * @author Rickard Andersson <rickard@0x539.se>
 */
class UTCW extends WP_Widget {

	/**
	 * Constructor
	 * @return void
	 */
	function UTCW() {
		$options = array('description' => __("Highly configurable tag cloud", 'utcw'));
		parent::WP_Widget(false, __('Ultimate Tag Cloud', 'utcw'), $options);
	}
    
	/**
	 * Action handler for the form in the admin panel
	 * @param array $new_instance
	 * @param array $old_instance
	 * @retrn array
	 */
	function update($new_instance, $old_instance) {

		global $utcw_allowed_orders,
			$utcw_allowed_taxonomies,
			$utcw_allowed_colors,
			$utcw_allowed_cases,
			$utcw_allowed_tags_list_types,
			$utcw_allowed_booleans,
			$utcw_allowed_border_styles;


		extract($new_instance);

		// If the load configuration option was selected
		if ( isset($load_config)  && $load_config == "on" && isset($load_config_name) && strlen($load_config_name) > 0 ) {

			// Get the currently saved configurations
			$configurations = get_option('utcw_saved_configs');

			// Try to get the selected configuration
			$config = $configurations[ $load_config_name ];

			if ( isset($config) && is_array($config) && count($config) > 0 ) {
				return $config;
			}
		}


		// Check all input values and set the default value if any value is invalid or empty
		$instance = $old_instance;
		$instance['title']              = strlen($title) > 0              ? apply_filters('widget_title', $title) : apply_filters('widget_title', UTCW_DEFAULT_TITLE);
		$instance['size_from']          = is_numeric($size_from)          ? $size_from : UTCW_DEFAULT_SIZE_FROM;
		$instance['size_to']            = is_numeric($size_to)            ? $size_to : UTCW_DEFAULT_SIZE_TO;
		$instance['max']                = is_numeric($max)                ? $max : UTCW_DEFALUT_MAX;
		$instance['letter_spacing']     = is_numeric($letter_spacing)     ? $letter_spacing : UTCW_DEFAULT_LETTER_SPACING;
		$instance['word_spacing']       = is_numeric($word_spacing)       ? $word_spacing : UTCW_DEFAULT_WORD_SPACING;
		$instance['tag_spacing']        = is_numeric($tag_spacing)        ? $tag_spacing : UTCW_DEFAULT_TAG_SPACING;
		$instance['line_height'] 		= is_numeric($line_height) 		  ? $line_height : UTCW_DEFAULT_LINE_HEIGHT;
		$instance['minimum'] 		    = is_numeric($minimum) 		      ? $minimum : UTCW_DEFAULT_MINIMUM;
		$instance['hover_border_width'] = is_numeric($hover_border_width) ? $hover_border_width : UTCW_DEFAULT_HOVER_BORDER_WIDTH;
		$instance['link_border_width']  = is_numeric($link_border_width)  ? $link_border_width : UTCW_DEFAULT_LINK_BORDER_WIDTH;
		$instance['days_old'] 			= is_numeric($days_old) 		  ? $days_old : UTCW_DEFAULT_DAYS_OLD;
		$instance['separator'] 			= strlen($separator) > 0 		  ? $separator : UTCW_DEFAULT_SEPARATOR;
		$instance['prefix']				= strlen($prefix) > 0 			  ? $prefix : UTCW_DEFAULT_PREFIX;
		$instance['suffix'] 			= strlen($suffix) > 0 			  ? $suffix : UTCW_DEFAULT_SUFFIX;
		$instance['reverse']            = ($reverse == "on");
		$instance['tags_list']          = strlen($tags_list) > 0      ? @explode(",", $tags_list) : array();
		$instance['color_set']          = strlen($color_set) > 0      ? @explode(",", $color_set) : array();
		$instance['authors']            = array();
		$instance['case_sensitive']     = ($case_sensitive == "on");
		$instance['page_tags']      	= ($page_tags == "on");
		$instance['show_title']         = ($show_title == "on");
		$instance['debug'] 				= ($debug == "on");

		$instance['color_span_from']    = preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $color_span_from) > 0     ? $color_span_from : "";
		$instance['color_span_to']      = preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $color_span_to) > 0       ? $color_span_to : "";
		$instance['link_bg_color']      = preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $link_bg_color) > 0       ? $link_bg_color : "";
		$instance['link_border_color']  = preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $link_border_color) > 0   ? $link_border_color : "";
		$instance['hover_bg_color']     = preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $hover_bg_color) > 0      ? $hover_bg_color : "";
		$instance['hover_color']        = preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $hover_color) > 0         ? $hover_color : "";
		$instance['hover_border_color'] = preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $hover_border_color) > 0  ? $hover_border_color : "";

		$instance['taxonomy']           = in_array($taxonomy, $utcw_allowed_taxonomies)              ? $taxonomy : UTCW_DEFAULT_TAXONOMY;
		$instance['order']              = in_array($order, $utcw_allowed_orders)                     ? $order : UTCW_DEFAULT_ORDER;
		$instance['color']              = in_array($color, $utcw_allowed_colors)                     ? $color : UTCW_DEFAULT_COLOR;
		$instance['case']               = in_array($case, $utcw_allowed_cases)                       ? $case : UTCW_DEFAULT_CASE;
		$instance['tags_list_type']     = in_array($tags_list_type, $utcw_allowed_tags_list_types)   ? $tags_list_type : UTCW_DEFAULT_TAGS_LIST_TYPE;
		$instance['link_underline']     = in_array($link_underline, $utcw_allowed_booleans)          ? $link_underline : UTCW_DEFAULT_LINK_UNDERLINE;
		$instance['link_bold']          = in_array($link_bold, $utcw_allowed_booleans)               ? $link_bold : UTCW_DEFAULT_LINK_BOLD;
		$instance['link_italic']        = in_array($link_italic, $utcw_allowed_booleans)             ? $link_italic : UTCW_DEFAULT_LINK_ITALIC;
		$instance['link_border_style']  = in_array($link_border_style, $utcw_allowed_border_styles)  ? $link_border_style : UTCW_DEFAULT_LINK_BORDER_STYLE;
		$instance['hover_underline']    = in_array($hover_underline, $utcw_allowed_booleans)         ? $hover_underline : UTCW_DEFAULT_HOVER_UNDERLINE;
		$instance['hover_bold']         = in_array($hover_bold, $utcw_allowed_booleans)              ? $hover_bold : UTCW_DEFAULT_HOVER_BOLD;
		$instance['hover_italic']       = in_array($hover_italic, $utcw_allowed_booleans)            ? $hover_italic : UTCW_DEFAULT_HOVER_ITALIC;
		$instance['hover_border_style'] = in_array($hover_border_style, $utcw_allowed_border_styles) ? $hover_border_style : UTCW_DEFAULT_HOVER_BORDER_STYLE;

		// Only accept numeric authors (user ID)
		if (is_array($authors)) {
			foreach ($authors as $author) {
				if (is_numeric($author)) {
					$instance['authors'][] = $author;
				}
			}
		}

		// Remove spaces in the comma separated list
		foreach ($instance['tags_list'] as $key => $value) {
			$instance['tags_list'][$key] = trim($value);
		}

		// Only allow hexadecimal color values in the format #ffffff and #fff
		foreach ($instance['color_set'] as $key => $color) {
			if (preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $color) == 0) {
				unset($instance['color_set'][$key]);
			}
		}

		// If the save_config option was selected
		if ( isset($save_config) && $save_config == "on" ) {

			// If no or empty name was submitted, fallback to the current date and time
			if ( !isset($save_config_name) || strlen($save_config_name) == 0 ) {
				$save_config_name = date( get_option('date_format')  . " " . get_option('time_format') );
			}

			// Load previously saved configurations
			$configurations = get_option('utcw_saved_configs');

			// If no configurations has been saved before, create a new array
			if ( $configurations === false ) {
				$configurations = array();
			}

			// Save
			$configurations[ $save_config_name ] = $instance;
			update_option('utcw_saved_configs', $configurations);

		}

		return $instance;
	}

	/**
	 * Function for displaying the widget on the page
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget($args, $instance) {
		do_utcw(array_merge($instance, $args));
	}

	/**
	 * Function for handling the widget control in admin panel
	 * @param array $instance
	 * @return void
	 */
	function form($instance) {
	
		// Get stored preferences
		$title              = isset( $instance['title'] ) ? esc_attr($instance['title']) : '' ;
		$order              = isset( $instance['order'] ) ? esc_attr($instance['order']) : '' ;
		$size_from          = isset( $instance['size_from'] ) ? esc_attr($instance['size_from']) : '' ;
		$size_to            = isset( $instance['size_to'] ) ? esc_attr($instance['size_to']) : '' ;
		$max                = isset( $instance['max'] ) ? esc_attr($instance['max']) : '' ;
		$taxonomy           = isset( $instance['taxonomy'] ) ? esc_attr($instance['taxonomy']) : '' ;
		$color              = isset( $instance['color'] ) ? esc_attr($instance['color']) : '' ;
		$color_span_from    = isset( $instance['color_span_from'] ) ? esc_attr($instance['color_span_from']) : '' ;
		$color_span_to      = isset( $instance['color_span_to'] ) ? esc_attr($instance['color_span_to']) : '' ;
		$letter_spacing     = isset( $instance['letter_spacing'] ) ? esc_attr($instance['letter_spacing']) : '' ;
		$word_spacing       = isset( $instance['word_spacing'] ) ? esc_attr($instance['word_spacing']) : '' ;
		$tag_spacing        = isset( $instance['tag_spacing'] ) ? esc_attr($instance['tag_spacing']) : '' ;
		$case               = isset( $instance['case'] ) ? esc_attr($instance['case']) : '' ;
		$minimum		    = isset( $instance['minimum'] ) ? esc_attr($instance['minimum']) : '' ;
		$tags_list          = isset( $instance['tags_list'] ) && is_array($instance['tags_list']) ? $instance['tags_list'] : array();
		$tags_list_type     = isset( $instance['tags_list_type'] ) ? esc_attr($instance['tags_list_type']) : '' ;
		$reverse            = isset( $instance['reverse'] ) ? $instance['reverse'] : '' ;
		$authors            = isset( $instance['authors'] ) && is_array($instance['authors']) ? $instance['authors'] : array();
		$color_set          = isset( $instance['color_set'] ) && is_array($instance['color_set']) ? $instance['color_set'] : array();
		$case_sensitive     = isset( $instance['case_sensitive'] ) ? $instance['case_sensitive'] : '' ;
		$page_tags          = isset( $instance['page_tags'] ) ? $instance['page_tags'] : '' ;
		$debug 				= isset( $instance['debug'] ) ? $instance['debug'] : '' ;
		$show_title         = isset( $instance['show_title'] ) ? $instance['show_title'] : '' ;
		$link_underline     = isset( $instance['link_underline'] ) ? $instance['link_underline'] : '' ;
		$link_bold          = isset( $instance['link_bold'] ) ? $instance['link_bold'] : '' ;
		$link_italic        = isset( $instance['link_italic'] ) ? $instance['link_italic'] : '' ;
		$link_bg_color      = isset( $instance['link_bg_color'] ) ? esc_attr($instance['link_bg_color']) : '' ;
		$link_border_width  = isset( $instance['link_border_width'] ) ? esc_attr($instance['link_border_width']) : '' ;
		$link_border_style  = isset( $instance['link_border_style'] ) ? $instance['link_border_style'] : '' ;
		$link_border_color  = isset( $instance['link_border_color'] ) ? esc_attr($instance['link_border_color']) : '' ;
		$hover_underline    = isset( $instance['hover_underline'] ) ? $instance['hover_underline'] : '' ;
		$hover_bold         = isset( $instance['hover_bold'] ) ? $instance['hover_bold'] : '' ;
		$hover_italic       = isset( $instance['hover_italic'] ) ? $instance['hover_italic'] : '' ;
		$hover_bg_color     = isset( $instance['hover_bg_color'] ) ? esc_attr($instance['hover_bg_color']) : '' ;
		$hover_color        = isset( $instance['hover_color'] ) ? esc_attr($instance['hover_color']) : '' ;
		$hover_border_width = isset( $instance['hover_border_width'] ) ? esc_attr($instance['hover_border_width']) : '' ;
		$hover_border_style = isset( $instance['hover_border_style'] ) ? $instance['hover_border_style'] : '' ;
		$hover_border_color = isset( $instance['hover_border_color'] ) ? esc_attr($instance['hover_border_color']) : '' ;
		$days_old 			= isset( $instance['days_old'] ) ? esc_attr($instance['days_old']) : '' ;
		$line_height 		= isset( $instance['line_height'] ) ? esc_attr($instance['line_height']) : '' ;
		$separator 			= isset( $instance['separator'] ) ? esc_attr($instance['separator']) : '';
		$prefix 			= isset( $instance['prefix'] ) ? esc_attr($instance['prefix']) : '';
		$suffix 			= isset( $instance['suffix'] ) ? esc_attr($instance['suffix']) : '';

		$configurations = get_option('utcw_saved_configs');

		// Content of the widget settings form
		require "settings.php";
	}
	
	function get_users() {
	
		global $wp_version;
		
		if ( (float)$wp_version < 3.1 ) {
			return get_users_of_blog(); 
		} else {
			return get_users(); 
		}		
	}
}

/**
 * do_utcw - Prints markup for the widget
 * @param array $args   An array with widget settings. See {@link http://wordpress.org/extend/plugins/ultimate-tag-cloud-widget/other_notes/} for details on which options you can set.  
 * @return void|string
 */
function do_utcw($args) {

	global $wpdb;

	if (is_array($args)) {
		extract($args);	
	}

	global $utcw_allowed_orders,
		$utcw_allowed_taxonomies,
		$utcw_allowed_colors,
		$utcw_allowed_cases,
		$utcw_allowed_tags_list_types,
		$utcw_allowed_booleans,
		$utcw_allowed_border_styles;

	// Parse settings from $instance and set default values where empty or invalid
	$title              = strlen($title) > 0              ? $title : UTCW_DEFAULT_TITLE;
	$size_from          = is_numeric($size_from)          ? $size_from : UTCW_DEFAULT_SIZE_FROM;
	$size_to            = is_numeric($size_to)            ? $size_to : UTCW_DEFAULT_SIZE_TO;
	$max                = is_numeric($max)                ? $max : UTCW_DEFAULT_MAX;
	$reverse            = is_bool($reverse)               ? $reverse : UTCW_DEFAULT_REVERSE;
	$minimum		    = is_numeric($minimum) 		      ? $minimum : UTCW_DEFAULT_MINIMUM;
	$letter_spacing     = is_numeric($letter_spacing)     ? $letter_spacing . "px" : UTCW_DEFAULT_LETTER_SPACING;
	$word_spacing       = is_numeric($word_spacing)       ? $word_spacing . "px" : UTCW_DEFAULT_WORD_SPACING;
	$tag_spacing        = is_numeric($tag_spacing)        ? $tag_spacing . "px" : UTCW_DEFAULT_TAG_SPACING;
	$color_span_from    = is_string($color_span_from)     ? $color_span_from : "";
	$color_span_to      = is_string($color_span_to)       ? $color_span_to : "";
	$case_sensitive     = is_bool($case_sensitive)        ? $case_sensitive : UTCW_DEFAULT_CASE_SENSITIVE;
	$page_tags          = is_bool($page_tags)             ? $page_tags : UTCW_DEFAULT_PAGE_TAGS;
	$show_title         = is_bool($show_title)            ? $show_title : UTCW_DEFAULT_SHOW_TITLE;
	$link_bg_color      = is_string($link_bg_color)       ? $link_bg_color : UTCW_DEFAULT_BG_COLOR;
	$link_border_width  = is_numeric($link_border_width)  ? $link_border_width . "px" : UTCW_DEFAULT_LINK_BORDER_WIDTH;
	$link_border_color  = is_string($link_border_color)   ? $link_border_color : UTCW_DEFAULT_LINK_BORDER_COLOR;
	$hover_bg_color     = is_string($hover_bg_color)      ? $hover_bg_color : UTCW_DEFAULT_HOVER_BG_COLOR;
	$hover_color        = is_string($hover_color)         ? $hover_color : UTCW_DEFAULT_HOVER_COLOR;
	$hover_border_width = is_numeric($hover_border_width) ? $hover_border_width . "px"  : UTCW_DEFAULT_HOVER_BORDER_WIDTH;
	$hover_border_color = is_string($hover_border_color)  ? $hover_border_color : UTCW_DEFAULT_HOVER_BORDER_COLOR;
	$days_old 			= is_numeric($days_old) 		  ? $days_old : UTCW_DEFAULT_DAYS_OLD;
	$line_height 		= is_numeric($line_height) 		  ? $line_height : UTCW_DEFAULT_LINE_HEIGHT;
	$separator 			= is_string($separator) 		  ? $separator : UTCW_DEFAULT_SEPARATOR;
	$prefix 			= is_string($prefix) 			  ? $prefix : UTCW_DEFAULT_PREFIX;
	$suffix 			= is_string($suffix) 			  ? $suffix : UTCW_DEFAULT_SUFFIX;

	$order              = in_array($order, $utcw_allowed_orders)                     ? $order : UTCW_DEFAULT_ORDER;
	$taxonomy           = in_array($taxonomy, $utcw_allowed_taxonomies)              ? $taxonomy : UTCW_DEFAULT_TAXONOMY;
	$color              = in_array($color, $utcw_allowed_colors)                     ? $color : UTCW_DEFAULT_COLOR;
	$case               = in_array($case, $utcw_allowed_cases)                       ? $case : UTCW_DEFAULT_CASE;
	$tags_list_type     = in_array($tags_list_type, $utcw_allowed_tags_list_types)   ? $tags_list_type : UTCW_DEFAULT_TAGS_LIST_TYPE;
	$link_underline     = in_array($link_underline, $utcw_allowed_booleans)          ? $link_underline : UTCW_DEFAULT_LINK_UNDERLINE;
	$link_bold          = in_array($link_bold, $utcw_allowed_booleans)               ? $link_bold : UTCW_DEFAULT_LINK_BOLD;
	$link_italic        = in_array($link_italic, $utcw_allowed_booleans)             ? $link_italic : UTCW_DEFAULT_LINK_ITALIC;
	$link_border_style  = in_array($link_border_style, $utcw_allowed_border_styles)  ? $link_border_style : UTCW_DEFAULT_LINK_BORDER_STYLE;
	$hover_underline    = in_array($hover_underline, $utcw_allowed_booleans)         ? $hover_underline : UTCW_DEFAULT_HOVER_UNDERLINE;
	$hover_bold         = in_array($hover_bold, $utcw_allowed_booleans)              ? $hover_bold : UTCW_DEFAULT_HOVER_BOLD;
	$hover_italic       = in_array($hover_italic, $utcw_allowed_booleans)            ? $hover_italic : UTCW_DEFAULT_HOVER_ITALIC;
	$hover_border_style = in_array($hover_border_style, $utcw_allowed_border_styles) ? $hover_border_style : UTCW_DEFAULT_HOVER_BORDER_STYLE;
	 
	$return = (isset($return) && $return == true);
	
	// Auhtors can be given as a comma separated list from the shortcode, 
	// try to explode it and validate the values 
	if (isset($authors)) {
		if (!is_array($authors)) {
			$authors = explode(",", $authors);
			
			foreach ($authors as $key => $author) {
				if (!is_numeric($author)) {
					unset($authors[ $key ]);
				}
			}
		}
	} else {
		$authors = array();
	}
	
	// Same for $tags_list
	if (isset($tags_list)) {
		if (!is_array($tags_list)) {
			$tags_list = explode(",", $tags_list);
			
			foreach ($tags_list as $key => $tl) {
				if (!is_numeric($tl)) {
					unset($tags_list[ $key ]);
				}
			}
		}
	} else {
		$tags_list = array();
	}
	
	// Same for $color_set but with different validation
	if (isset($color_set)) {
		if (!is_array($color_set)) {
			$color_set = explode(",", $color_set); 
			
			foreach ($color_set as $key => $cs) {
				if (!preg_match('/#([a-f0-9]{6}|[a-f0-9]{3})/i', $cs)) {
					unset($color_set[ $key ]);
				}
			}
		}
	} else {
		$color_set = array();
	}
		 
	// Fallback values
	$counts = array();
	$tag_array = array();

	// Build SQL query
	$sql[] = "SELECT t.term_id, t.name, t.slug, COUNT(tr.term_taxonomy_id) AS `count`";
	$sql[] = "FROM `$wpdb->posts` AS p";
	$sql[] = "LEFT JOIN `$wpdb->term_relationships` AS tr ON tr.object_id = p.ID";
	$sql[] = "LEFT JOIN `$wpdb->term_taxonomy` AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
	$sql[] = "LEFT JOIN `$wpdb->terms` AS t ON t.term_id = tt.term_id";
	$sql[] = "WHERE tt.taxonomy = '$taxonomy'";

	// Setting post_type directive
	if ($page_tags === true) {
		$sql[] = "AND (post_type = 'post' OR post_type = 'page')";
	} else {
		$sql[] = "AND post_type = 'post'";
	}

	// Setting post status directive
	if (is_user_logged_in() === true) {
		$sql[] = "AND (post_status = 'publish' OR post_status = 'private')";
	} else {
		$sql[] = "AND post_status = 'publish'";
	}

	// Setting post author directive
	if (count($authors) > 0) {
		$sql[] = "AND post_author IN (" . implode(",", $authors) . ")";
	}
	
	// Setting include or exclude directive
	if (count($tags_list) > 0) {

		$tags_list_operator = ($tags_list_type == "include") ? "IN" : "NOT IN";

		if (is_array_numeric($tags_list)) {
			$sql[] = "AND t.term_id ". $tags_list_operator ." ('" . implode("', '", $tags_list) . "')";
		} else {
			$sql[] = "AND t.name ". $tags_list_operator ." ('" . implode("', '", $tags_list) . "')";
		}
	}
	
	// Setting to only include posts newer then a 
	if (is_numeric($days_old) && $days_old > 0) {
		$sql[] = "AND post_date > '" . date("Y-m-d", strtotime("-" . $days_old . " days") ) . "'";		
	}

	// Setting minimum post count directive
	if (is_numeric($minimum)) {
		$sql[] = "GROUP BY tr.term_taxonomy_id HAVING count >= $minimum";
	} else {
		$sql[] = "GROUP BY tr.term_taxonomy_id";
	}

	$sql[] = "ORDER BY count DESC";
	$sql[] = "LIMIT $max";

	$query = implode("\n", $sql);

	$tag_data = $wpdb->get_results($query);

	if (count($tag_data) > 0) {

		// Extract counts and create an array to work with
		foreach ($tag_data as $tag) {
			$counts[] = $tag->count;
			$tag_array[] = array(
		        'term_id' => $tag->term_id, 
		        'count' => $tag->count, 
		        'slug' => $tag->slug,
		        'name' => $tag->name,
		        'link' => get_term_link(intval($tag->term_id), $taxonomy),
		        'color' => ""
        	);
		}
    
		// Highest and lowest values
		$min_count = min($counts);
		$max_count = max($counts);

		// Get the step size
		$font_step = calc_step($min_count, $max_count, $size_from, $size_to);

		// Calculate sizes for all tags
		foreach ($tag_array as $key => $tag) {
			$tag_array[$key]['size'] = $size_from + ( ( $tag['count'] - $min_count ) * $font_step );
		}

		// Check the coloring preference, default is none
		switch ($color) {

			// Just get an randomized value, who would ever use this?!
			case "random":
				foreach ($tag_array as $key => $tag) {
					$tag_array[$key]['color'] = sprintf("#%s%s%s", dechex(rand() % 255), dechex(rand() % 255), dechex(rand() % 255));
				}
				break;

				// Select a random value from the preset colors
			case "set":
				if (is_array($color_set) && count($color_set) > 0) {
					foreach ($tag_array as $key => $tag) {
						$tag_array[$key]['color'] = $color_set[ array_rand($color_set) ];
					}
				}
				break;

				// Calculate colors in a span between two values
			case "span":

				// Check the color format, #fff or #fffff
				if (strlen($color_span_from) == 4) {
					$red_from    = hexdec(sprintf("%s%s", $color_span_from[1], $color_span_from[1]));
					$green_from  = hexdec(sprintf("%s%s", $color_span_from[2], $color_span_from[2]));
					$blue_from   = hexdec(sprintf("%s%s", $color_span_from[3], $color_span_from[3]));
				} else {
					$red_from    = hexdec(substr($color_span_from, 1, 2));
					$green_from  = hexdec(substr($color_span_from, 3, 2));
					$blue_from   = hexdec(substr($color_span_from, 5, 2));
				}
				if (strlen($color_span_to) == 4) {
					$red_to    = hexdec(sprintf("%s%s", $color_span_to[1], $color_span_to[1]));
					$green_to  = hexdec(sprintf("%s%s", $color_span_to[2], $color_span_to[2]));
					$blue_to   = hexdec(sprintf("%s%s", $color_span_to[3], $color_span_to[3]));
				} else {
					$red_to    = hexdec(substr($color_span_to, 1, 2));
					$green_to  = hexdec(substr($color_span_to, 3, 2));
					$blue_to   = hexdec(substr($color_span_to, 5, 2));
				}

				// Calculate steps for all the colors.
				$red_step   = calc_step($min_count, $max_count, $red_from, $red_to);
				$green_step = calc_step($min_count, $max_count, $green_from, $green_to);
				$blue_step  = calc_step($min_count, $max_count, $blue_from, $blue_to);

				// Iterate all tags and calculate their color
				foreach ($tag_array as $key => $tag) {
					$red    = round($red_from + ( ( $tag['count'] - $min_count ) * $red_step ));
					$green  = round($green_from + ( ( $tag['count'] - $min_count ) * $green_step ));
					$blue   = round($blue_from + ( ( $tag['count'] - $min_count ) * $blue_step ));

					$tag_array[$key]['color'] = sprintf("rgb(%s,%s,%s)", $red, $green, $blue);
				}
				break;
		}

		// Check the ordering preference, default is name
		switch ($order) {
			case "random":
				shuffle($tag_array);
				break;

			case "count":
				usort($tag_array, 'utcw_cmp_count');
				break;

			case "slug";
			usort($tag_array, $case_sensitive === true ? 'utcw_cmp_slug' : 'utcw_icmp_slug');
			break;

			case "id":
				usort($tag_array, 'utcw_cmp_id');
				break;

			case "color":
				usort($tag_array, 'utcw_cmp_color');
				break;

			case "name":
			default:
				usort($tag_array, $case_sensitive === true ? 'utcw_cmp_name' : 'utcw_icmp_name');
				break;
		}

		// Reverse the list if the user prefers it that way. Reversing an random sorted result seems correct.
		if ($reverse === true) {
			$tag_array = array_reverse($tag_array);
		}
	}

	switch ($case) {
		case 'uppercase':
			$text_transform = 'text-transform: uppercase;';
			break;

		case 'lowercase':
			$text_transform = 'text-transform: lowercase;';
			break;

		case 'capitalize':
			$text_transform = 'text-transform: capitalize;';
			break;

		default:
			$text_transform = '';
	}
	
	if ($return === true) {
		ob_start();
	}

	// Print the tag cloud content
	echo $before_widget;
	echo $before_title . $title . $after_title;

	printf('<div class="widget_tag_cloud" style="letter-spacing:%s;word-spacing:%s;%s">', $letter_spacing, $word_spacing, $text_transform);

	$hover_style = "";
	$link_style = "";

	if ($link_underline != "default") {
		$link_style .= "text-decoration:" . ($link_underline == "yes" ? "underline !important;" : "none !important;");
	}
	if ($link_bold != "default") {
		$link_style .= "font-weight:" . ($link_bold == "yes" ? "bold !important;" : "normal !important;");
	}
	if ($link_italic != "default") {
		$link_style .= "font-style:" . ($link_italic == "yes" ? "italic !important;" : "normal !important;");
	}
	if ($link_bg_color != "transparent") {
		$link_style .= sprintf("background-color:%s !important;", $link_bg_color);
	}
	if (strlen($link_border_style) > 0) {
		$link_style .= sprintf("border-style:%s !important;", $link_border_style);
	}
	if (strlen($link_border_color) > 0) {
		$link_style .= sprintf("border-color:%s !important;", $link_border_color);
	}
	if (strlen($link_border_width) > 0) {
		$link_style .= sprintf("border-width:%s !important;", $link_border_width);
	}

	if ($hover_underline != "default") {
		$hover_style .= "text-decoration:" . ($hover_underline == "yes" ? "underline !important;" : "none !important;");
	}
	if ($hover_bold != "default") {
		$hover_style .= "font-weight:" . ($hover_bold == "yes" ? "bold !important;" : "normal !important;");
	}
	if ($hover_italic != "default") {
		$hover_style .= "font-style:" . ($hover_italic == "yes" ? "italic !important;" : "normal !important;");
	}
	if ($hover_bg_color != "transparent") {
		$hover_style .= sprintf("background-color:%s !important;", $hover_bg_color);
	}
	if ($hover_color != "default") {
		$hover_style .= sprintf("color:%s !important;", $hover_color);
	}
	if (strlen($hover_border_style) > 0) {
		$hover_style .= sprintf("border-style:%s !important;", $hover_border_style);
	}
	if (strlen($hover_border_color) > 0) {
		$hover_style .= sprintf("border-color:%s !important;", $hover_border_color);
	}
	if (strlen($hover_border_width) > 0) {
		$hover_style .= sprintf("border-width:%s !important;", $hover_border_width);
	}
	if (is_numeric($tag_spacing)) {
		$link_style .= sprintf("margin-right: %s !important;", $tag_spacing);
	}
	if (is_numeric($line_height)) {
		$link_style .= sprintf("line-height: %spx !important;", $line_height);
	}

	if (strlen($hover_style) > 0 || strlen($link_style)) {
		printf('<style type="text/css">.utcw-tag-link{%s}.utcw-tag-link:hover{%s}</style>', $link_style, $hover_style);
	}
	
	$tags_left = count($tag_array);

	foreach ($tag_array as $tag) {
		extract($tag);
		
		if ($tags_left-- == 1) {
			unset($separator);
		}

		$link_title = $show_title === true ? sprintf(' title="' . _n("%s topic", "%s topics", $count) . '"', $count) : "";

		printf('<span style="font-size:%spx;%s">%s<a class="utcw-tag-link tag-link-%s" href="%s" style="font-size:%spx;%s"%s>%s</a>%s</span>%s',
			$size, strlen($color) > 0 ? "color:$color;" : "", $prefix, $term_id, $link, $size, strlen($color) > 0 ? "color:$color;" : "", $link_title, $name, $suffix, $separator);
	}

	echo "</div>";

	if ($debug == true) {
		echo "<!-- Ultimate Tag Cloud Debug information: "; var_dump($args);
		echo "\n\n SQL Query:" . $query;
		echo "\n\n Tag Data: "; var_dump($tag_data);
		echo "-->";
	}

	echo $after_widget;
	
	if ($return === true) {
		$markup = ob_get_contents();
		ob_end_clean();
		return $markup;
	}
}

/**
 * Function for using the widget with a shortcode
 * @param array $args
 * @return string
 */
function do_utcw_shortcode($args) {
	
	// Shortcodes should return values, not echo out
	$args['return'] = true;
	
	return do_utcw($args);
}

/**
 * Used to calculate how step size in spanning values
 * @param integer $min
 * @param integer $max
 * @param integer $from
 * @param integer $to
 * @return integer 
 */
function calc_step($min, $max, $from, $to) {

	// Thank you wordpress for this
	$spread = $max - $min;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $to - $from;
	if ( $font_spread < 0 && $negative === false)
		$font_spread = 1;
	$step = $font_spread / $spread;

	return $step;
}

load_plugin_textdomain('utcw', false, '/ultimate-tag-cloud-widget/language/');

// Register widget with wordpress
add_action('widgets_init', create_function('', 'return register_widget("UTCW");'));
add_action('wp_loaded', 'utcw_wp_loaded');

//Register scripts and css with wordpress
wp_register_script('utcw-js', '/wp-content/plugins/ultimate-tag-cloud-widget/utcw.js', array('jquery'), "1.3", false);
wp_register_style('utcw-css', '/wp-content/plugins/ultimate-tag-cloud-widget/utcw.css', array(), "1.3", 'all');

/**
 * Action handler for wordpress init used to attach scripts and styles
 * @return void
 */
function utcw_init() {
	wp_enqueue_script('utcw-js');
	wp_enqueue_style('utcw-css');
	
	add_shortcode('utcw', 'do_utcw_shortcode');
}
add_action('init', 'utcw_init');

/*
 * Compare functions
 */
function utcw_cmp_count($a, $b) {
	if ($a['count'] == $b['count']) {
		return 0;
	} else {
		return ($a['count'] < $b['count']) ? -1 : 1;
	}
}

function utcw_icmp_name($a, $b) {
	return strcasecmp($a['name'], $b['name']);
}

function utcw_cmp_name($a, $b) {
	return strcmp($a['name'], $b['name']);
}

function utcw_icmp_slug($a, $b) {
	return strcasecmp($a['slug'], $b['slug']);
}

function utcw_cmp_slug($a, $b) {
	return strcmp($a['slug'], $b['slug']);
}

function utcw_cmp_id($a, $b) {
	if ($a['term_id'] == $b['term_id']) {
		return 0;
	} else {
		return ($a['term_id'] < $b['term_id']) ? -1 : 1;
	}
}

function utcw_cmp_color($a, $b) {
	return strcasecmp($a['color'], $b['color']);
}

/**
 * Function for checking if every item within an array is a numeric value
 * @param array $array
 * @return boolean
 */
function is_array_numeric($array) {

	foreach ($array as $item) {
		if (!is_numeric($item)) {
			return false;
		}
	}

	return true;
}

/**
 * Function to get the list of custom taxonomies
 * @return void
 */
function utcw_wp_loaded() {
	global $utcw_allowed_taxonomies;

	$utcw_allowed_taxonomies = get_object_taxonomies("post");
}
