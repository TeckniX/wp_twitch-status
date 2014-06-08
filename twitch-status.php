<?php
/*
Plugin Name: Twitch Status
Description: Insert Twitch.tv online status in WordPress
Version: 1.1
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
define('TWITCH_STATUS_VER', '1.1');
define('TWITCH_STATUS_URL', plugins_url('/' . basename(dirname(__FILE__))));

include_once(TWITCH_STATUS_BASE . 'includes/twitch-status-options.php');
include_once(TWITCH_STATUS_BASE . 'includes/twitch-status-widget.php');

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
	$jsConfig = array(
		'ajaxurl'    => admin_url('admin-ajax.php'),
		'channel'    => get_option('twitch_status_channel'),
		'selectors'  => explode("\n", get_option('twitch_status_selector')),
		'buttonHTML' => array(
			'online' => __('LIVE!', 'twitch-status'),
			'offline' => __('offline', 'twitch-status'),
		),
	);

	?>
		<script type="text/javascript">
			var twitchStatus = <?php echo json_encode($jsConfig); ?>
		</script>
    <link rel="stylesheet" href="<?php echo TWITCH_STATUS_URL ?>/font/fontello/css/fontello.css">
    <link rel="stylesheet" href="<?php echo TWITCH_STATUS_URL ?>/font/fontello/css/animation.css"><!--[if IE 7]><link rel="stylesheet" href="<?php echo TWITCH_STATUS_URL ?>/font/fontello/css/fontello-ie7.css"><![endif]-->
	<?php
}
add_action('wp_head','twitch_status_js_vars');

/**
 * Retrieve channel and stream data from Twitch.tv
 * Called by AJAXaction
 * @return void
 */
function twitch_status_get_channel_status_ajax()
{
	header('Content-type: application/json; charset=utf-8');

	// Fetch stream and channel information from Twitch
	$now = time();

	$channel = get_option('twitch_status_channel');
	$channelName = preg_replace('/[^0-9a-zA-Z_-]/', '', get_option('twitch_status_channel'));
	$channelFilename = TWITCH_STATUS_BASE . 'cache/' . $channelName . '-channel.json';
	$streamFilename  = TWITCH_STATUS_BASE . 'cache/' . $channelName . '-stream.json';

	// Update channel information from cache
	if ($now - @filemtime($channelFilename) >= 15)
	{
		$rawChannelData = @file_get_contents('https://api.twitch.tv/kraken/channels/' . $channel);

		if (!empty($rawChannelData))
			file_put_contents($channelFilename, $rawChannelData);
	}
	else
		$rawChannelData = @file_get_contents($channelFilename);

	// Update stream status information from cache
	if ($now - @filemtime($streamFilename) >= 15)
	{
		$rawStreamData = @file_get_contents('https://api.twitch.tv/kraken/streams/' . $channel);

		if (!empty($rawStreamData))
			file_put_contents($streamFilename, $rawStreamData);
	}
	else
		$rawStreamData = @file_get_contents($streamFilename);

	$data = array();
	$channelData = @json_decode($rawChannelData, true);
	$streamData  = @json_decode($rawStreamData, true);

	if (!empty($channelData) && !empty($streamData))
	{
		if (!empty($channelData['error']) || !empty($streamData['error']))
			$data['status'] = 'error';
		else
		{
			$data['status']  = empty($streamData['stream'])?'offline':'online';
			$data['channel'] = $channelData;
			$data['stream']  = $streamData['stream'];

			$data['playingHTML'] = sprintf(__("%s playing %s", 'twitch-status'),
			                               '<a href="http://www.twitch.tv/' . urlencode($channelData['name']) . '/profile" target="_blank">' . htmlspecialchars($channelData['display_name']) . '</a>',
			                               '<a href="http://www.twitch.tv/directory/game/' . str_replace('+', ' ', urlencode($channelData['game'])) . '" target="_blank">' . htmlspecialchars($channelData['game']) . '</a>');
		}
	}
	else
		$data['status'] = 'error';

	echo json_encode($data);

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


