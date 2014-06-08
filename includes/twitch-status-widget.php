<?php

class TwitchStatus_Widget extends WP_Widget
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'twitch-status_widget',
			__('Twitch status widget', 'twitch-status'),
			array('description' => __('Shows a preview of your Twitch.tv stream.', 'twitch-status'),)
		);
	}

	/**
	 * Widget front end
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance)
	{
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if (!empty($title))
			echo $args['before_title'] . $title . $args['after_title'];

		?>
			<div class="twitch-widget">
				<div class="twitch-preview" style="display: none">
					<div class="twitch-channel-topic"></div>
					<div class="twitch-game"></div>
					<div class="twitch-thumbnail">
						<a>
							<div class="twitch-thumbnail-image"></div>
							<div class="twitch-play-button"></div>
						</a>
					</div>
					<span class="twitch-followers"></span>
					<span class="twitch-viewers"></span>
				</div>
				<div class="twitch-preview-offline" style="display: none">
					<div class="twitch-thumbnail-offline">
						<div class="twitch-offline-image"></div>
						<div class="twitch-offline-caption"><?php echo __('Offline', 'twitch-status'); ?></div>
					</div>
				</div>
			</div>
		<?php

		echo $args['after_widget'];
	}

	/**
	 * Widget admin form
	 * @param array $instance
	 */
	public function form($instance)
	{
		if (isset($instance['title']))
			$title = $instance['title'];
		else
			$title = __('Twitch', 'twitch-status');

		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</p>
		<?php
	}

	/**
	 * Updating widget replacing old instances with new
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title']))?strip_tags($new_instance['title']):'';
		return $instance;
	}

	/**
	 * Register the widget
	 * called by widgets_init action
	 * @return void
	 */
	public static function register()
	{
		register_widget('TwitchStatus_Widget');

	}
}

// Register and load the widget
add_action( 'widgets_init', array('TwitchStatus_Widget', 'register'));
