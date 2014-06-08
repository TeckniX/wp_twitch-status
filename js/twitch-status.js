jQuery(document).ready(function() {

	// Add Twitch online button containers
	for (var i in twitchStatus.selectors)
		jQuery(twitchStatus.selectors[i]).append('<span class="twitch-status-tag"></span>');

	twitchStatusUpdate(); // Update Twitch status buttons
	setInterval(twitchStatusUpdate, 30000); // Update ever 30 seconds
});

function twitchStatusUpdate()
{
	var data = {
		'action': 'get_twitch_channel_status',
		'channel': twitchStatus.channel
	};

	jQuery.post(twitchStatus.ajaxurl, data, function(response, status) {
		if (status !== 'success')
			return;

		if (response.twitch_status === 'online')
		{
			jQuery('.twitch-status-tag').removeClass('twitch-offline');
			jQuery('.twitch-status-tag').addClass('twitch-online');
			jQuery('.twitch-status-tag').html(twitchStatus.buttonHTML.online);
		}
		else
		{
			jQuery('.twitch-status-tag').removeClass('twitch-online');
			jQuery('.twitch-status-tag').addClass('twitch-offline');
			jQuery('.twitch-status-tag').html(twitchStatus.buttonHTML.offline);
		}
	});
}