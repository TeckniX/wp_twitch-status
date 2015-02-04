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

function twitchStatusUpdate()
{
	var data = {
		'action': 'get_twitch_channel_status'
	};

	jQuery.post(twitchStatus.ajaxurl, data, function(response, status) {
		if (status !== 'success')
			return;
		for(var i = 0; i < response.length; i++){
			var twitchStatusData = response[i];
			var twitchIndex = i;
	
			var w = jQuery('.twitch-widget').width();
			var h = jQuery('.twitch-widget').width() / (16/9);
	
			// Update status button
			if (twitchStatusData.status === 'online')
			{
				jQuery('.twitch-status-tag:eq('+twitchIndex+')').removeClass('twitch-offline');
				jQuery('.twitch-status-tag:eq('+twitchIndex+')').addClass('twitch-online');
				jQuery('.twitch-status-tag:eq('+twitchIndex+')').html(twitchStatus.buttonHTML.online);
			}
			else
			{
				jQuery('.twitch-status-tag:eq('+twitchIndex+')').removeClass('twitch-online');
				jQuery('.twitch-status-tag:eq('+twitchIndex+')').addClass('twitch-offline');
				jQuery('.twitch-status-tag:eq('+twitchIndex+')').html(twitchStatus.buttonHTML.offline);
			}
	
			twitchStatusRefreshWidget(twitchStatusData, i);
		}
	});
}

function twitchStatusRefreshWidget(twitchStatusData, twitchIndex)
{
	var w = jQuery('.twitch-widget:eq('+twitchIndex+')').width();
	var h = jQuery('.twitch-widget:eq('+twitchIndex+')').width() / (16/9);

	if (twitchStatusData.status === 'online')
	{
		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-channel-topic').html(twitchStatusData.channel.status);
		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-game').html(twitchStatusData.playingHTML);

		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-viewers').html(twitchStatusData.stream.viewers);
		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-followers').html(twitchStatusData.channel.followers);

		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-thumbnail-image').html('<img src="' + twitchStatusData.stream.preview.large + '">');
		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-play-button').css({lineHeight: h + 'px', width: w + 'px', height: h + 'px', marginTop: -h + 'px'});

		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-preview').show();
		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-preview-offline').hide();
	}
	else
	{
		if (typeof twitchStatusData.channel != 'undefined')
			jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-offline-image').html('<img src="' + twitchStatusData.channel.video_banner + '">');
		else
			jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-offline-image').css({width: w + 'px', height: h + 'px'});

		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-offline-caption').css({lineHeight: h + 'px', width: w + 'px', height: h + 'px', marginTop: -h + 'px'});

		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-preview').hide();
		jQuery('.twitch-widget:eq('+twitchIndex+') .twitch-preview-offline').show();
	}
}
