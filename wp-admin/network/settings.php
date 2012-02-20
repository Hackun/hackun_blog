<?php
/**
 * Multisite network settings administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_network_options' ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$title = __( 'Network Settings' );
$parent_file = 'settings.php';

get_current_screen()->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __('Overview'),
		'content' =>
			'<p>' . __('This screen sets and changes options for the network as a whole. The first site is the main site in the network and network options are pulled from that original site&#8217;s options.') . '</p>' .
			'<p>' . __('Operational settings has fields for the network&#8217;s name and admin email.') . '</p>' .
			'<p>' . __('Dashboard Site is an option to give a site to users who do not have a site on the system. Their default role is Subscriber, but that default can be changed. The Admin Notice Feed can provide a notice on all dashboards of the latest post via RSS or Atom, or provide no such notice if left blank.') . '</p>' .
			'<p>' . __('Registration settings can disable/enable public signups. If you let others sign up for a site, install spam plugins. Spaces, not commas, should separate names banned as sites for this network.') . '</p>' .
			'<p>' . __('New site settings are defaults applied when a new site is created in the network. These include welcome email for when a new site or user account is registered, and what&#8127;s put in the first post, page, comment, comment author, and comment URL.') . '</p>' .
			'<p>' . __('Upload settings control the size of the uploaded files and the amount of available upload space for each site. You can change the default value for specific sites when you edit a particular site. Allowed file types are also listed (space separated only).') . '</p>' .
			'<p>' . __('Checkboxes for media upload buttons set which are shown in the visual editor. If unchecked, a generic upload button is still visible; other media types can still be uploaded if on the allowed file types list.') . '</p>' .
			'<p>' . __('Menu setting enables/disables the plugin menus from appearing for non super admins, so that only super admins, not site admins, have access to activate plugins.') . '</p>' .
			'<p>' . __('Super admins can no longer be added on the Options screen. You must now go to the list of existing users on Network Admin > Users and click on Username or the Edit action link below that name. This goes to an Edit User page where you can check a box to grant super admin privileges.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Network_Admin_Settings_Screen" target="_blank">Documentation on Network Settings</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

if ( $_POST ) {
	do_action( 'wpmuadminedit' , '' );

	check_admin_referer( 'siteoptions' );

	if ( isset( $_POST['WPLANG'] ) && ( '' === $_POST['WPLANG'] || in_array( $_POST['WPLANG'], get_available_languages() ) ) )
		update_site_option( 'WPLANG', $_POST['WPLANG'] );

	if ( is_email( $_POST['admin_email'] ) )
		update_site_option( 'admin_email', $_POST['admin_email'] );

	$illegal_names = split( ' ', $_POST['illegal_names'] );
	foreach ( (array) $illegal_names as $name ) {
		$name = trim( $name );
		if ( $name != '' )
			$names[] = trim( $name );
		}
	update_site_option( 'illegal_names', $names );

	if ( $_POST['limited_email_domains'] != '' ) {
		$limited_email_domains = str_replace( ' ', "\n", $_POST['limited_email_domains'] );
		$limited_email_domains = split( "\n", stripslashes( $limited_email_domains ) );
		$limited_email = array();
		foreach ( (array) $limited_email_domains as $domain ) {
			$domain = trim( $domain );
			if ( ! preg_match( '/(--|\.\.)/', $domain ) && preg_match( '|^([a-zA-Z0-9-\.])+$|', $domain ) )
				$limited_email[] = trim( $domain );
		}
		u