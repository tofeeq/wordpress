<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Freedom
 * @subpackage Freedom/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
 
 // check user capabilities
 if ( ! current_user_can( 'manage_options' ) ) {
 	return;
 }
 
 // add error/update messages
 
 // check if the user have submitted the settings
 // wordpress will add the "settings-updated" $_GET parameter to the url
 if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 	add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'wporg' ), 'updated' );
 }
 
 // show error/update messages
 settings_errors( 'wporg_messages' );
 ?>
 <div class="wrap">
	 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	 <form action="options.php" method="post">
		 <?php 
		 	settings_fields( $this->plugin_name . '-settings');	//pass slug name of page, also referred to in Settings API as option group name
			do_settings_sections( $this->plugin_name . '-settings' ); 	//pass slug name of page
		 	submit_button( 'Save Settings' );
		 ?>
	 </form>
 </div>