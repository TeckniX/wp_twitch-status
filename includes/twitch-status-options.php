<?php

/**
 * Add the admin options page
 * Called by admin_menu action
 * @return void
 */
function twitch_status_admin_add_page()
{
	add_options_page('Twitch status', 'Twitch status', 'manage_options', 'twitch_status', 'twitch_status_options_page');
}
add_action('admin_menu', 'twitch_status_admin_add_page');

/**
 * Admin options page
 */
function twitch_status_options_page()
{
	?>
	<div>
		<h2>Twitch Status</h2>
		<form action="options.php" method="post">
			<?php settings_fields('twitch_status_options'); ?>
			<?php do_settings_sections('twitch_status'); ?>
			<input name="Submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>
	</div>

	<?php
}

/**
 * Init admin options
 */
function twitch_status_admin_init()
{
	add_settings_section('twitch_status_main', __('Main settings', 'twitch-status'), 'twitch_status_section_text', 'twitch_status');

	// Channel name
	register_setting('twitch_status_options', 'twitch_status_channel');
	add_settings_field('twitch_status_channel',  __('Channel name', 'twitch-status'),    'twitch_status_channel_edit',  'twitch_status', 'twitch_status_main');

	// jQuery selector
	register_setting('twitch_status_options', 'twitch_status_selector', 'twitch_status_selector_validate');
	add_settings_field('twitch_status_selector', __('jQuery selectors', 'twitch-status'), 'twitch_status_selector_edit', 'twitch_status', 'twitch_status_main');
}
add_action('admin_init', 'twitch_status_admin_init');

function twitch_status_section_text()
{
	echo '<p></p>';
}

function twitch_status_channel_edit()
{
	echo '<input id="twitch_status_channel" name="twitch_status_channel" size="40" type="text" value="' . htmlspecialchars(get_option('twitch_status_channel')) . '" /><p class="description">' . __("Your Twitch channel name", 'twitch-status') . '</p>';
}

function twitch_status_selector_edit()
{
	echo '<textarea id="twitch_status_selector" class="large-text code" name="twitch_status_selector" rows="4">' . htmlspecialchars(get_option('twitch_status_selector')) . '</textarea><p class="description">' . __("<a href=\"http://api.jquery.com/category/selectors/\" target=\"_blank\">jQuery selectors</a> matching the places you want to insert the stream status tags.<br />Enter one selector per line. You can add as much selectors as you want.", 'twitch-status') . '</p>';
}
function twitch_status_selector_validate($input)
{
	$input = trim(str_replace("\r", "", $input));
	$rows = explode("\n", $input);

	$filtered = array();
	foreach($rows as $row)
		if (trim($row) != "")
			$filtered[]= trim($row);

	return implode("\n", $filtered);
}