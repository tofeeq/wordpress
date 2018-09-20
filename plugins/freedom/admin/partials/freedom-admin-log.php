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
 ?>
<div class="wrap">
	<p>
 		<a href="<?php echo admin_url('edit.php?post_type=freedom_log') ?>" class="page-title-action">Go Back</a>
 	</p>
	<h1>Log</h1>
	<div class="card">
		<h2><?php echo $args['title']; ?> </h2>
		<p><?php echo $args['content']; ?> </p>
	</div>
</div>