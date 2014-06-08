jQuery(document).ready(function() {

	// Add Twitch online button containers
	for (var i in twitchStatus.selectors)
		jQuery(twitchStatus.selectors[i]).append('<span class="twitch-status-tag"></span>');

	twitchStatusUpdate(); // Update Twitch status buttons
	setInterval(twitchStatusUpdate, 30000); // Update ever 30 seconds

	// Refresh widget after resize
	jQuery(window).resize(twitchStatusRefreshWidget);
	window.addEventListener("orientationchange", twitchStatusRefreshWidget, false);
});

var twitchStatusData = {};
function twitchStatusUpdate()
{
	var data = {
		'action': 'get_twitch_channel_status',
		'channel': twitchStatus.channel
	};

	jQuery.post(twitchStatus.ajaxurl, data, function(response, status) {
		if (status !== 'success')
			return;

		twitchStatusData = response;

		var w = jQuery('.twitch-widget').width();
		var h = jQuery('.twitch-widget').width() / (16/9);

		// Update status button
		if (twitchStatusData.status === 'online')
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

		twitchStatusRefreshWidget();
	});
}

function twitchStatusRefreshWidget()
{
	var w = jQuery('.twitch-widget').width();
	var h = jQuery('.twitch-widget').width() / (16/9);

	if (twitchStatusData.status === 'online')
	{
		jQuery('.twitch-channel-topic').html(twitchStatusData.channel.status);
		jQuery('.twitch-game').html(twitchStatusData.playingHTML);

		jQuery('.twitch-viewers').html(twitchStatusData.stream.viewers);
		jQuery('.twitch-followers').html(twitchStatusData.channel.followers);

		jQuery('.twitch-thumbnail-image').html('<img src="' + twitchStatusData.stream.preview.large + '">');
		jQuery('.twitch-play-button').css({lineHeight: h + 'px', width: w + 'px', height: h + 'px', marginTop: -h + 'px'});

		jQuery('.twitch-preview').show();
		jQuery('.twitch-preview-offline').hide();
	}
	else
	{
		if (typeof twitchStatusData.channel != 'undefined')
			jQuery('.twitch-offline-image').html('<img src="' + twitchStatusData.channel.video_banner + '">');
		else
			jQuery('.twitch-offline-image').css({width: w + 'px', height: h + 'px', backgroundColor: '#00FF00'});

		jQuery('.twitch-offline-caption').css({lineHeight: h + 'px', width: w + 'px', height: h + 'px', marginTop: -h + 'px'});

		jQuery('.twitch-preview').hide();
		jQuery('.twitch-preview-offline').show();
	}
}