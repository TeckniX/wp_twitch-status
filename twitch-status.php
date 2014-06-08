<?php
/*
Plugin Name: Twitch Status
Plugin URI: http://www.synagila.com
Description: Insert Twitch.tv online status in WordPress
Version: 1.0
Author: Nicolas Bernier
Author URI: http://www.synagila.com
License: GPL v2

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

define('TWITCH_STATUS_BASE', plugin_dir_path(__FILE__));
define('TWITCH_STATUS_VER', '1.0');
define('TWITCH_STATUS_URL', plugins_url('/' . basename(dirname(__FILE__))));

include_once(TWITCH_STATUS_BASE . 'includes/twitch-status-options.php');

/**
 * Enqueue scripts and CSS
 * Called by enqueue_scripts action
 * @return void
 */
function twitch_status_enqueue_scripts()
{
	wp_enqueue_style('twitch_status',  TWITCH_STATUS_URL .'/css/twitch-status.css', array(), TWITCH_STATUS_VER);
	wp_enqueue_script('twitch_status', TWITCH_STATUS_URL .'/js/twitch-status.js',   array(), TWITCH_STATUS_VER, true);
}
add_action('wp_enqueue_scripts', 'twitch_status_enqueue_scripts');

/**
 * Declares Javascript variables and custom fonts
 * Called by wp_head action
 * @return void
 */
function twitch_status_js_vars()
{
	?>
		<script type="text/javascript">
			var twitchStatus = {
				ajaxurl   : <?php echo json_encode(admin_url('admin-ajax.php')); ?>,
				channel   : <?php echo json_encode(get_option('twitch_status_channel')); ?>,
				selectors : <?php echo json_encode(explode("\n", get_option('twitch_status_selector'))); ?>,
				buttonHTML : {
					online : <?php echo json_encode(__('LIVE!', 'twitch-status')); ?>,
					offline : <?php echo json_encode(__('offline', 'twitch-status')); ?>,
				}
			}
		</script>
    <link rel="stylesheet" href="<?php echo TWITCH_STATUS_URL ?>/font/fontello/css/fontello.css">
    <link rel="stylesheet" href="<?php echo TWITCH_STATUS_URL ?>/font/fontello/css/animation.css"><!--[if IE 7]><link rel="stylesheet" href="<?php echo TWITCH_STATUS_URL ?>/font/fontello/css/fontello-ie7.css"><![endif]-->
	<?php
}
add_action('wp_head','twitch_status_js_vars');

/**
 * Retrieve channel data from Twitch.tv
 * Called by AJAXaction
 * @return void
 */
function twitch_status_get_channel_status_ajax()
{
	$data = @json_decode(@file_get_contents('https://api.twitch.tv/kraken/streams/' . $_REQUEST['channel']), true);

	header('Content-type: application/json; charset=utf-8');

	if (!empty($data))
	{
		if (!empty($data['error']))
			$data['twitch_status'] = 'error';
		else
			$data['twitch_status'] = empty($data['stream'])?'offline':'online';

		echo json_encode($data);
	}
	else
		echo json_encode(array('twitch_status' => 'error'));

	die();
}
add_action( 'wp_ajax_get_twitch_channel_status', 'twitch_status_get_channel_status_ajax' );
add_action( 'wp_ajax_nopriv_get_twitch_channel_status', 'twitch_status_get_channel_status_ajax' );

/**
 * Initializes localization
 * Called by plugins_loaded action
 * @return void
 */
function twitch_status_lang_init()
{
	load_plugin_textdomain('twitch-status', false, dirname(plugin_basename(__FILE__)) . '/languages/' );
}
add_action('plugins_loaded', 'twitch_status_lang_init');


