<?php
/*
Plugin Name: Fisrt-Plugin
Plugin URI: https://www.facebook.com/tii.itus
Description: A simple wordpress plugin
Version: 1.0
Author: T4
Author URI: https://www.facebook.com/tii.itus
License: GPL
*/
?>
<?php
/* Runs when plugin is activated */
register_activation_hook(__FILE__,'hello_world_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'hello_world_remove' );

function hello_world_install() {
/* Creates new database field */
add_option("hello_world_data", 'Default', '', 'yes');
}

function hello_world_remove() {
/* Deletes the database field */
delete_option('hello_world_data');
}

?>
<?php
if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'hello_world_admin_menu');

function hello_world_admin_menu() {
add_options_page('Hello World', 'Hello World', 'administrator',
'hello-world', 'hello_world_html_page');
}
}
?>
<?php
function hello_world_html_page() {
?>
<div>
<h2>Hello World Options</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table width="510">
<tr valign="top">
<th width="92" scope="row">Enter Text</th>
<td width="406">
<input name="hello_world_data" type="text" id="hello_world_data"
value="<?php echo get_option('hello_world_data'); ?>" />
(ex. Hello World)</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hello_world_data" />

<p>
<input type="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
<?php
}
?>