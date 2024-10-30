<?php

/**
 * TikTok widget Class
 */
class WTIK_Widget extends WP_Widget {

	private static $app;

	/**
	 * @var WTIK_Plugin
	 */
	public $plugin;

	/**
	 * @var array
	 */
	public $sliders;

	/**
	 * @var array
	 */
	public $options_linkto;

	/**
	 * @var array
	 *
	 */
	public $defaults;

	/**
	 * @var WTIK_Api
	 */
	public $api;

	public static function app() {
		return self::$app;
	}

	/**
	 * Initialize the plugin by registering widget and loading public scripts
	 *
	 */
	public function __construct() {
		self::$app = $this;

		// Widget ID and Class Setup
		parent::__construct( 'wtiktok_feed', __( 'TikTok Feed - CreativeMotion', 'tiktok-feed' ), array(
			'classname'   => 'wtiktok-feed',
			'description' => __( 'A widget that displays a TikTok videos ', 'tiktok-feed' )
		) );

		$this->plugin         = WTIK_Plugin::app();
		$this->sliders        = array(
			"slider"           => 'Slider - Normal',
			"slider-overlay"   => 'Slider - Overlay Text',
			"thumbs"           => 'Thumbnails',
			"thumbs-no-border" => 'Thumbnails - Without Border',
		);
		$this->options_linkto = array(
			"post_page"  => 'TikTok post page',
			"video_url"  => 'Video URL',
			"custom_url" => 'Custom Link',
			"none"       => 'None'
		);

		$this->defaults = array(
			'title'                => __( 'TikTok Feed', 'tiktok-feed' ),
			'search'               => '',
			'blocked_users'        => '',
			'blocked_words'        => '',
			'template'             => 'slider',
			'images_link'          => 'post_page',
			'custom_url'           => '',
			'orderby'              => 'rand',
			'images_number'        => 20,
			'columns'              => 2,
			'refresh_hour'         => 5,
			'controls'             => 'prev_next',
			'animation'            => 'slide',
			'caption_words'        => 20,
			'slidespeed'           => 7000,
			'description'          => array( 'username', 'time', 'caption' ),
			'support_author'       => 0,
			'gutter'               => 0,
			'masonry_image_width'  => 200,
			'slick_slides_to_show' => 3,
			'slick_slides_padding' => 0,
			'show_feed_header'     => 1,
			'highlight_offset'     => 1,
			'highlight_pattern'    => 6,
		);

		$this->api = new WTIK_Api();


		/**
		 * Фильтр для добавления слайдеров
		 */
		$this->sliders = apply_filters( 'wtik/sliders', $this->sliders );

		/**
		 * Фильтр для добавления popup
		 */
		$this->options_linkto = apply_filters( 'wtik/options/link_to', $this->options_linkto );


		// Enqueue Plugin Styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue' ) );

		// Enqueue Plugin Styles and scripts for admin pages
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

		// Shortcode
		add_shortcode( 'cm_tiktok_feed', array( $this, 'shortcode' ) );
		// Action to display posts
		add_action( 'wtiktok_feed', array( $this, 'display_posts' ) );

		//AJAX
		add_action( 'wp_ajax_wtik_add_account_by_token', array( $this, 'add_account_by_token' ) );

	}

	/**
	 * Register widget on widgets init
	 */
	public static function register_widget() {
		register_widget( __CLASS__ );
		register_sidebar( array(
			'name'        => __( 'TikTok Feed - Shortcode Generator', 'tiktok-feed' ),
			'id'          => 'wtiktok-shortcodes',
			'description' => __( "1. Drag TikTok Feed Widget here. 2. Fill in the fields and hit save. 3. Copy the shortocde generated at the bottom of the widget form and use it on posts or pages.", 'tiktok-feed' )
		) );
	}

	/**
	 * Enqueue public-facing Scripts and style sheet.
	 */
	public function public_enqueue() {

		wp_enqueue_style( WTIK_Plugin::app()->getPrefix() . 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );

		wp_enqueue_style( WTIK_Plugin::app()->getPrefix() . 'instag-slider', WTIK_PLUGIN_URL . '/assets/css/templates.css', array(), WTIK_Plugin::app()->getPluginVersion() );
		wp_enqueue_script( WTIK_Plugin::app()->getPrefix() . 'jquery-pllexi-slider', WTIK_PLUGIN_URL . '/assets/js/jquery.flexslider-min.js', array( 'jquery' ), WTIK_Plugin::app()->getPluginVersion(), false );
		//wp_enqueue_script( WTIK_Plugin::app()->getPrefix() . 'wtiktok', WTIK_PLUGIN_URL.'/assets/js/wtiktok.js', array(  ), WTIK_Plugin::app()->getPluginVersion(), false );
		wp_enqueue_style( WTIK_Plugin::app()->getPrefix() . 'wtik-header', WTIK_PLUGIN_URL . '/assets/css/wtik-header.css', array(), WTIK_Plugin::app()->getPluginVersion() );
		wp_localize_script( WTIK_Plugin::app()->getPrefix() . 'wtiktok', 'ajax', array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( "addAccountByToken" ),
		) );
	}

	/**
	 * Enqueue admin side scripts and styles
	 *
	 * @param string $hook
	 */
	public function admin_enqueue( $hook ) {


		if ( 'widgets.php' != $hook && 'post.php' != $hook ) {
			return;
		}
		wp_enqueue_style( 'wtiktok-admin-styles', WTIK_PLUGIN_URL . '/admin/assets/css/wtiktok-admin.css', array(), WTIK_Plugin::app()->getPluginVersion() );
		wp_enqueue_script( 'wtiktok-admin-script', WTIK_PLUGIN_URL . '/admin/assets/js/wtiktok-admin.js', array( 'jquery' ), WTIK_Plugin::app()->getPluginVersion(), true );

	}

	/**
	 * The Public view of the Widget
	 *
	 */
	public function widget( $args, $instance ) {

	    //Our variables from the widget settings.
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		// Display the widget title
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		do_action( 'wtiktok_feed', $instance );

		echo $args['after_widget'];
	}

	/**
	 * Widget Settings Form
	 *
	 */
	public function form( $instance ) {

		$accounts = WTIK_Plugin::app()->getPopulateOption( WTIK_ACCOUNT_OPTION_NAME, array() );
		if ( ! is_array( $accounts ) ) {
			$accounts = array();
		}
		$sliders        = $this->sliders;
		$options_linkto = $this->options_linkto;

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
        <div class="wtik-container">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php _e( 'Title:', 'tiktok-feed' ); ?></strong></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>"
                       value="<?php echo $instance['title']; ?>"/>
            </p>
            <p>
                <span class="wtik-search-for-container">
                    <?php
                    if ( count( $accounts ) ) {
	                    ?>
                        <label for="<?php echo $this->get_field_id( 'search' ); ?>"><strong><?php _e( 'Feed:', 'tiktok-feed' ); ?></strong></label>
                        <select id="<?php echo $this->get_field_id( 'search' ); ?>" class="widefat"
                                name="<?php echo $this->get_field_name( 'search' ); ?>"><?php
	                    foreach ( $accounts as $feed ) {
		                    if ( $feed['type'] == 'hashtag' ) {
			                    $username = "#{$feed['username']}";
		                    } else {
			                    $username = $feed['username'];
		                    }
		                    $selected = $instance['search'] == $feed['id'] ? "selected='selected'" : "";
		                    echo "<option value='{$feed['id']}' data-type='{$feed['type']}' {$selected}>{$username}</option>";
	                    }
	                    ?>
                        </select><?php
                    } else {
	                    echo "<a href='" . WTIK_Plugin::app()->getPluginPageUrl( 'feed' ) . "'>" . __( 'Add feed in settings', 'tiktok-feed' ) . "</a>";
                    }
                    ?>
                </span>
            </p>
            <p id="img_to_show">
                <label for="<?php echo $this->get_field_id( 'images_number' ); ?>"><strong><?php _e( 'Count of images to show:', 'tiktok-feed' ); ?></strong>
                    <input class="small-text" type="number" min="1" max=""
                           id="<?php echo $this->get_field_id( 'images_number' ); ?>"
                           name="<?php echo $this->get_field_name( 'images_number' ); ?>"
                           value="<?php echo $instance['images_number']; ?>"/>
                    <span class="wtik-description">
                        <?php if ( ! $this->plugin->is_premium() ) {
	                        _e( 'Maximum 20 images in free version.', 'tiktok-feed' );
	                        echo " " . sprintf( __( "More in <a href='%s'>PRO version</a>", 'tiktok-feed' ), $this->plugin->get_support()->get_pricing_url( true, "wtik_widget_settings" ) );
                        }
                        ?>
                    </span>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'refresh_hour' ); ?>"><strong><?php _e( 'Check for new posts every:', 'tiktok-feed' ); ?></strong>
                    <input class="small-text" type="number" min="1" max="200"
                           id="<?php echo $this->get_field_id( 'refresh_hour' ); ?>"
                           name="<?php echo $this->get_field_name( 'refresh_hour' ); ?>"
                           value="<?php echo $instance['refresh_hour']; ?>"/>
                    <span><?php _e( 'hours', 'tiktok-feed' ); ?></span>
                </label>
            </p>
            <p class="show_feed_header">
                <strong><?php _e( 'Show feed header:', 'tiktok-feed' ); ?></strong>
                <label class="switch" for="<?php echo $this->get_field_id( 'show_feed_header' ); ?>">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'show_feed_header' ); ?>"
                           name="<?php echo $this->get_field_name( 'show_feed_header' ); ?>" type="checkbox"
                           value="1" <?php checked( '1', $instance['show_feed_header'] ); ?> />
                    <span class="slider round"></span>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'template' ); ?>"><strong><?php _e( 'Template', 'tiktok-feed' ); ?></strong>
                    <select class="widefat" name="<?php echo $this->get_field_name( 'template' ); ?>"
                            id="<?php echo $this->get_field_id( 'template' ); ?>">
						<?php
						if ( count( $sliders ) ) {
							foreach ( $sliders as $key => $slider ) {
								$selected = ( $instance['template'] == $key ) ? "selected='selected'" : '';
								echo "<option value='{$key}' {$selected}>{$slider}</option>\n";
							}
						}
						if ( ! $this->plugin->is_premium() ) {
							?>
                            <optgroup label="Available in PRO">
                                <option value='slick_slider' disabled="disabled">Slick</option>
                                <option value='masonry' disabled="disabled">Masonry</option>
                                <option value='highlight' disabled="disabled">Highlight</option>
                            </optgroup>
							<?php
						}
						?>
                    </select>
                </label>
            </p>
            <span id="masonry_notice"
                  class="masonry_notice wtik-description <?php if ( 'masonry' != $instance['template'] ) {
				      echo 'hidden';
			      } ?>"><?php _e( "Not recommended for <strong>sidebar</strong>" ) ?></span>
            <p class="<?php if ( 'thumbs' != $instance['template'] && 'thumbs-no-border' != $instance['template'] ) {
				echo 'hidden';
			} ?>">
                <label for="<?php echo $this->get_field_id( 'columns' ); ?>"><strong><?php _e( 'Number of Columns:', 'tiktok-feed' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'columns' ); ?>"
                           type="number" min="1" max="10"
                           name="<?php echo $this->get_field_name( 'columns' ); ?>"
                           value="<?php echo $instance['columns']; ?>"/>
                    <span class='wtik-description'><?php _e( 'max is 10 ( only for thumbnails template )', 'tiktok-feed' ); ?></span>
                </label>
            </p>
            <p class="masonry_settings <?php if ( 'masonry' != $instance['template'] ) {
				echo 'hidden';
			} ?>">
                <label for="<?php echo $this->get_field_id( 'gutter' ); ?>"><strong><?php _e( 'Vertical space between item elements:', 'tiktok-feed' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'gutter' ); ?>"
                           name="<?php echo $this->get_field_name( 'gutter' ); ?>"
                           value="<?php echo $instance['gutter']; ?>"/>
                    <span><?php _e( 'px', 'tiktok-feed' ); ?></span>
                </label>
                <br>
                <label for="<?php echo $this->get_field_id( 'masonry_image_width' ); ?>"><strong><?php _e( 'Image width:', 'tiktok-feed' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'masonry_image_width' ); ?>"
                           name="<?php echo $this->get_field_name( 'masonry_image_width' ); ?>"
                           value="<?php echo $instance['masonry_image_width']; ?>"/>
                    <span><?php _e( 'px', 'tiktok-feed' ); ?></span>
                </label>
            </p>
            <p class="slick_settings <?php if ( 'slick_slider' != $instance['template'] ) {
				echo 'hidden';
			} ?>">
                <label for="<?php echo $this->get_field_id( 'slick_slides_to_show' ); ?>"><strong><?php _e( 'Pictures per slide:', 'tiktok-feed' ); ?></strong>
                    <input class="small-text" id="<?php echo $this->get_field_id( 'slick_slides_to_show' ); ?>"
                           name="<?php echo $this->get_field_name( 'slick_slides_to_show' ); ?>"
                           value="<?php echo $instance['slick_slides_to_show']; ?>"/>
                    <span><?php _e( 'pictures', 'tiktok-feed' ); ?></span>
                </label>
                <br>
                <strong><?php _e( 'Space between pictures:', 'tiktok-feed' ); ?></strong>
                <label class="switch" for="<?php echo $this->get_field_id( 'slick_slides_padding' ); ?>">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'slick_slides_padding' ); ?>"
                           name="<?php echo $this->get_field_name( 'slick_slides_padding' ); ?>" type="checkbox"
                           value="1" <?php checked( '1', $instance['slick_slides_padding'] ); ?> />
                    <span class="slider round"></span>
                </label>
            </p>
            <p class="highlight_settings <?php if ( 'highlight' != $instance['template'] ) {
				echo 'hidden';
			} ?>">
                <label for="<?php echo $this->get_field_id( 'highlight_offset' ); ?>"><strong><?php _e( 'Offset', 'tiktok-feed' ); ?></strong>
                    <input type="number" min="1" class="small-text"
                           id="<?php echo $this->get_field_id( 'highlight_offset' ); ?>"
                           name="<?php echo $this->get_field_name( 'highlight_offset' ); ?>"
                           value="<?php echo $instance['highlight_offset']; ?>"/>
                </label>
                <br>
                <label for="<?php echo $this->get_field_id( 'highlight_pattern' ); ?>"><strong><?php _e( 'Pattern', 'tiktok-feed' ); ?></strong>
                    <input type="number" min="0" class="small-text"
                           id="<?php echo $this->get_field_id( 'highlight_pattern' ); ?>"
                           name="<?php echo $this->get_field_name( 'highlight_pattern' ); ?>"
                           value="<?php echo $instance['highlight_pattern']; ?>"/>
                </label>
            </p>
            <p class="slider_normal_settings wtik-slider-options <?php if ( 'slider' != $instance['template'] && 'slider-overlay' != $instance['template'] ) {
				echo 'hidden';
			} ?>">

				<?php _e( 'Slider Navigation Controls:', 'tiktok-feed' ); ?><br>
                <label class="wtik-radio"><input type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>"
                                                 name="<?php echo $this->get_field_name( 'controls' ); ?>"
                                                 value="prev_next" <?php checked( 'prev_next', $instance['controls'] ); ?> /> <?php _e( 'Prev & Next', 'tiktok-feed' ); ?>
                </label>
                <label class="wtik-radio"><input type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>"
                                                 name="<?php echo $this->get_field_name( 'controls' ); ?>"
                                                 value="numberless" <?php checked( 'numberless', $instance['controls'] ); ?> /> <?php _e( 'Dotted', 'tiktok-feed' ); ?>
                </label>
                <label class="wtik-radio"><input type="radio" id="<?php echo $this->get_field_id( 'controls' ); ?>"
                                                 name="<?php echo $this->get_field_name( 'controls' ); ?>"
                                                 value="none" <?php checked( 'none', $instance['controls'] ); ?> /> <?php _e( 'No Navigation', 'tiktok-feed' ); ?>
                </label>
                <br>
				<?php _e( 'Slider Animation:', 'tiktok-feed' ); ?><br>
                <label class="wtik-radio"><input type="radio" id="<?php echo $this->get_field_id( 'animation' ); ?>"
                                                 name="<?php echo $this->get_field_name( 'animation' ); ?>"
                                                 value="slide" <?php checked( 'slide', $instance['animation'] ); ?> /> <?php _e( 'Slide', 'tiktok-feed' ); ?>
                </label>
                <label class="wtik-radio"><input type="radio" id="<?php echo $this->get_field_id( 'animation' ); ?>"
                                                 name="<?php echo $this->get_field_name( 'animation' ); ?>"
                                                 value="fade" <?php checked( 'fade', $instance['animation'] ); ?> /> <?php _e( 'Fade', 'tiktok-feed' ); ?>
                </label>
                <br>
                <label for="<?php echo $this->get_field_id( 'slidespeed' ); ?>"><?php _e( 'Slide Speed:', 'tiktok-feed' ); ?>
                    <input type="number" min="1000" max="10000" step="100" class="small-text"
                           id="<?php echo $this->get_field_id( 'slidespeed' ); ?>"
                           name="<?php echo $this->get_field_name( 'slidespeed' ); ?>"
                           value="<?php echo $instance['slidespeed']; ?>"/>
                    <span><?php _e( 'milliseconds', 'tiktok-feed' ); ?></span>
                    <span class='wtik-description'><?php _e( '1000 milliseconds = 1 second', 'tiktok-feed' ); ?></span>
                </label>
                <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Slider Text Description:', 'tiktok-feed' ); ?></label>
                <select size=3 class='widefat' id="<?php echo $this->get_field_id( 'description' ); ?>"
                        name="<?php echo $this->get_field_name( 'description' ); ?>[]" multiple="multiple">
                    <option value='username' <?php $this->selected( $instance['description'], 'username' ); ?>><?php _e( 'Username', 'tiktok-feed' ); ?></option>
                    <option value='time'<?php $this->selected( $instance['description'], 'time' ); ?>><?php _e( 'Time', 'tiktok-feed' ); ?></option>
                    <option value='caption'<?php $this->selected( $instance['description'], 'caption' ); ?>><?php _e( 'Caption', 'tiktok-feed' ); ?></option>
                </select>
                <span class="wtik-description"><?php _e( 'Hold ctrl and click the fields you want to show/hide on your slider. Leave all unselected to hide them all. Default all selected.', 'tiktok-feed' ) ?></span>
            </p>
            <p class="words_in_caption <?php if ( 'thumbs' == $instance['template'] || 'thumbs-no-border' == $instance['template'] || 'highlight' == $instance['template'] || 'slick_slider' == $instance['template'] ) {
				echo 'hidden';
			} ?>">
                <label for="<?php echo $this->get_field_id( 'caption_words' ); ?>"><strong><?php _e( 'Number of words in caption:', 'tiktok-feed' ); ?></strong>
                    <input class="small-text" type="number" min="0" max="200"
                           id="<?php echo $this->get_field_id( 'caption_words' ); ?>"
                           name="<?php echo $this->get_field_name( 'caption_words' ); ?>"
                           value="<?php echo $instance['caption_words']; ?>"/>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><strong><?php _e( 'Order by', 'tiktok-feed' ); ?></strong>
                    <select class="widefat" name="<?php echo $this->get_field_name( 'orderby' ); ?>"
                            id="<?php echo $this->get_field_id( 'orderby' ); ?>">
                        <option value="date-ASC" <?php selected( $instance['orderby'], 'date-ASC', true ); ?>><?php _e( 'Date - Ascending', 'tiktok-feed' ); ?></option>
                        <option value="date-DESC" <?php selected( $instance['orderby'], 'date-DESC', true ); ?>><?php _e( 'Date - Descending', 'tiktok-feed' ); ?></option>
                        <option value="popular-ASC" <?php selected( $instance['orderby'], 'popular-ASC', true ); ?>><?php _e( 'Popularity - Ascending', 'tiktok-feed' ); ?></option>
                        <option value="popular-DESC" <?php selected( $instance['orderby'], 'popular-DESC', true ); ?>><?php _e( 'Popularity - Descending', 'tiktok-feed' ); ?></option>
                        <option value="rand" <?php selected( $instance['orderby'], 'rand', true ); ?>><?php _e( 'Random', 'tiktok-feed' ); ?></option>
                    </select>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'images_link' ); ?>"><strong><?php _e( 'Link to', 'tiktok-feed' ); ?></strong>
                    <select class="widefat" name="<?php echo $this->get_field_name( 'images_link' ); ?>"
                            id="<?php echo $this->get_field_id( 'images_link' ); ?>">
						<?php
						if ( count( $options_linkto ) ) {
							foreach ( $options_linkto as $key => $option ) {
								$selected = selected( $instance['images_link'], $key, false );
								echo "<option value='{$key}' {$selected}>{$option}</option>\n";
							}
						}
						if ( ! $this->plugin->is_premium() ) {
							?>
                            <optgroup label="Available in PRO">
                                <option value='1' disabled="disabled">Pop Up</option>
                            </optgroup>
							<?php
						}
						?>
                    </select>
                </label>
            </p>
            <p class="<?php if ( 'custom_url' != $instance['images_link'] ) {
				echo 'hidden';
			} ?>">
                <label for="<?php echo $this->get_field_id( 'custom_url' ); ?>"><?php _e( 'Custom link:', 'tiktok-feed' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'custom_url' ); ?>"
                       name="<?php echo $this->get_field_name( 'custom_url' ); ?>"
                       value="<?php echo $instance['custom_url']; ?>"/>
                <span><?php _e( '* use this field only if the above option is set to <strong>Custom Link</strong>', 'tiktok-feed' ); ?></span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'blocked_users' ); ?>"><?php _e( 'Block Users', 'tiktok-feed' ); ?>
                    :</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'blocked_users' ); ?>"
                       name="<?php echo $this->get_field_name( 'blocked_users' ); ?>"
                       value="<?php echo $instance['blocked_users']; ?>"/>
                <span class="wtik-description"><?php _e( 'Enter words separated by commas whose images you don\'t want to show', 'tiktok-feed' ); ?></span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'blocked_words' ); ?>"><?php _e( 'Block words', 'tiktok-feed' ); ?>
                    :</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'blocked_words' ); ?>"
                       name="<?php echo $this->get_field_name( 'blocked_words' ); ?>"
                       value="<?php echo $instance['blocked_words']; ?>"/>
                <span class="wtik-description"><?php _e( 'Enter comma-separated words. If one of them occurs in the image description, the image will not be displayed', 'tiktok-feed' ); ?></span>
            </p>
			<?php $widget_id = preg_replace( '/[^0-9]/', '', $this->id );
			if ( $widget_id != '' ) : ?>
                <p>
                    <label for="jr_insta_shortcode"><?php _e( 'Shortcode of this Widget:', 'tiktok-feed' ); ?></label>
                    <input id="jr_insta_shortcode" onclick="this.setSelectionRange(0, this.value.length)" type="text"
                           class="widefat" value="[cm_tiktok_feed id=&quot;<?php echo $widget_id ?>&quot;]"
                           readonly="readonly" style="border:none; color:black; font-family:monospace;">
                    <span class="wtik-description"><?php _e( 'Use this shortcode in any page or post to display images with this widget configuration!', 'tiktok-feed' ) ?></span>
                </p>
			<?php endif; ?>
        </div>
		<?php
	}

	/**
	 * Update the widget settings
	 *
	 * @param array $new_instance New instance values
	 * @param array $instance Old instance values
	 *
	 * @return array
	 */
	public function update( $new_instance, $instance ) {
		foreach ( $new_instance as $key => $item ) {
			$new_instance[ $key ] = isset( $new_instance[ $key ] ) ? $new_instance[ $key ] : $this->defaults[ $key ];
			if ( $key == 'title' ) {
				$new_instance[ $key ] = strip_tags( $new_instance[ $key ] );
			}
			$new_instance['widget_id'] = preg_replace( '/[^0-9]/', '', $this->id );
		}

		return $new_instance;
	}

	/**
	 * Update the widget settings
	 *
	 * @param array $instance instance values
	 *
	 * @return array
	 */
	public function defaults( $instance ) {
		$new_instance = [];
		foreach ( $instance as $key => $item ) {
			switch ( $key ) {
				case 'search':
				case 'blocked_users':
				case 'blocked_words':
					$new_instance[ $key ] = ! empty( $instance[ $key ] ) ? $instance[ $key ] : $this->defaults[ $key ];
					break;
				case 'images_number':
				case 'columns':
				case 'refresh_hour':
					$new_instance[ $key ] = absint( $instance[ $key ] );
					break;
				default:
					$new_instance[ $key ] = $instance[ $key ];
					break;
			}
		}

		$new_instance = wp_parse_args( (array) $new_instance, $this->defaults );

		return $new_instance;
	}

	/**
	 * Selected array function echoes selected if in array
	 *
	 * @param array $haystack The array to search in
	 * @param string $current The string value to search in array;
	 *
	 */
	public function selected( $haystack, $current ) {

		if ( is_array( $haystack ) && in_array( $current, $haystack ) ) {
			selected( 1, 1, true );
		}
	}


	/**
	 * Add shortcode function
	 *
	 * @param array $atts shortcode attributes
	 *
	 * @return mixed
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => '' ), $atts, 'cm_tiktok_feed' );
		$args = get_option( 'widget_wtiktok_feed' );
		if ( isset( $args[ $atts['id'] ] ) ) {
			$args[ $atts['id'] ]['widget_id'] = $atts['id'];

			return $this->display( $args[ $atts['id'] ] );
		}

		return "";
	}

	/**
	 * Echoes the Display Instagram Images method
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public function display_posts( $args ) {
		echo $this->display( $args );
	}

	/**
	 * Runs the query for images and returns the html
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	private function display( $args ) {

		$args = $this->defaults( $args );

		if ( ! empty( $args['description'] ) && ! is_array( $args['description'] ) ) {
			$args['description'] = explode( ',', $args['description'] );
		}

		if ( $args['refresh_hour'] == 0 ) {
			$args['refresh_hour'] = 5;
		}

		if ( $args['template'] == 'slick_slider' || $args['template'] == 'masonry' || $args['template'] == 'highlight' ) {
			//return apply_filters( 'wis/pro/display_images', "", $args, $this );
		} else {
			if ( $args['search'] ) {
				$account_data    = WTIK_Plugin::app()->get_feeds( $args['search'] );
				$args['account'] = $account_data;
				$template_args   = $args;

				$images_data = $this->get_data( $args );

				$user_url = $account_data['type'] == 'account' ? $this->api->get_tiktok_url( "@{$account_data['username']}" ) : $this->api->get_tiktok_url( "/tag/{$account_data['username']}" );
				if ( is_array( $images_data ) && ! empty( $images_data ) ) {
					if ( isset( $images_data['error'] ) ) {
						return $images_data['error'];
					}

					if ( $args['orderby'] != 'rand' ) {
						$args['orderby'] = explode( '-', $args['orderby'] );
						if ( $args['orderby'][0] == 'date' ) {
							$func = 'sort_timestamp_' . $args['orderby'][1];
						} else {
							$func = 'sort_popularity_' . $args['orderby'][1];
						}
						usort( $images_data, array( $this, $func ) );
					} else {
						shuffle( $images_data );
					}

					foreach ( $images_data as $image_data ) {

						switch ( $args['images_link'] ) {
							case 'post_page':
								$temp_args['link_to'] = $image_data['link'];
								break;
							case 'user_url':
								$temp_args['link_to'] = $user_url;
								break;
							case 'video_url':
								$temp_args['link_to'] = $image_data['url'];
								break;
							case 'custom_url':
								$temp_args['link_to'] = $args['custom_url'];
								break;
						}

						$temp_args['image']     = $image_data['sizes']['default'];
						$temp_args['caption']   = $image_data['caption'];
						$temp_args['timestamp'] = $image_data['timestamp'];
						$temp_args['username']  = isset( $image_data['username'] ) ? $image_data['username'] : '';

						$template_args['posts'][] = $temp_args;
					}

					$return = "";
					if ( $args['show_feed_header'] ) {
						$return .= $this->render_layout_template( 'feed_header_template', $account_data );
					}
					$return .= $this->render_layout_template( $args['template'], $template_args );

					return $return;
				} else {
					return __( 'No videos found', 'tiktok-feed' );
				}
			}
		}


		return "&nbsp;";
	}

	/**
	 * Method renders layout template
	 *
	 * @param string $template_name Template name without ".php"
	 *
	 * @param array $args Template arguments
	 *
	 * @return false|string
	 */
	private function render_layout_template( $template_name, $args ) {
		$path = WTIK_PLUGIN_DIR . "/html_templates/$template_name.php";
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;

			return ob_get_clean();
		} else {
			return 'This template does not exist!';
		}
	}

	/**
	 * Trigger refresh for new data
	 *
	 * @param bool $instaData
	 * @param array $old_args
	 * @param array $new_args
	 *
	 * @return bool
	 */
	private function trigger_refresh_data( $instaData, $old_args, $new_args ) {

		$trigger = 0;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		if ( false === $instaData ) {
			$trigger = 1;
		}


		if ( isset( $old_args['saved_images'] ) ) {
			unset( $old_args['saved_images'] );
		}

		if ( isset( $old_args['deleted_images'] ) ) {
			unset( $old_args['deleted_images'] );
		}

		if ( is_array( $old_args ) && is_array( $new_args ) && array_diff( $old_args, $new_args ) !== array_diff( $new_args, $old_args ) ) {
			$trigger = 1;
		}

		if ( $trigger == 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * Stores the fetched data from instagram in WordPress DB using transients
	 *
	 * @param array $search Array of widget settings
	 *
	 * @return array|string data
	 * @throws \Exception
	 */
	public function get_data( $search ) {
		$cache_hours   = $search['refresh_hour'];
		$images_number = $search['images_number'];
		$search_type   = $search['account']['type'];
		$search_name   = $search['account']['username'];
		$blocked_users = isset( $search['blocked_users'] ) ? $search['blocked_users'] : '';
		$blocked_words = isset( $search['blocked_words'] ) ? $search['blocked_words'] : '';

		if ( ! isset( $search ) || empty( $search ) ) {
			return __( 'Nothing to search for', 'tiktok-feed' );
		}


		$opt_name   = "wtiktok_{$search_type}-{$search_name}";
		$resultData = get_transient( $opt_name );
		$old_opts   = get_option( $opt_name, [] );
		$new_opts   = array(
			'search'        => $search_name,
			'blocked_users' => $blocked_users,
			'blocked_words' => $blocked_words,
			'cache_hours'   => $cache_hours,
			'images_number' => $images_number,
		);

		if ( $this->trigger_refresh_data( $resultData, $old_opts, $new_opts ) || ( defined( 'WTIK_ENABLE_CACHING' ) && ! WTIK_ENABLE_CACHING ) ) {

			$resultData                = array();
			$old_opts['search']        = $search_name;
			$old_opts['blocked_users'] = $blocked_users;
			$old_opts['blocked_words'] = $blocked_words;
			$old_opts['cache_hours']   = $cache_hours;
			$old_opts['images_number'] = $images_number;

			$images_number = ! $this->plugin->is_premium() && $images_number > 20 ? 20 : $images_number;

			if ( 'account' == $search_type ) {
				$response = $this->api->get_account_media( $search_name, $images_number * 2 );
				if ( $response ) {
					if ( ! is_array( $response ) || ! count( $response ) ) {
						return [ 'error' => __( 'There are no publications in this account yet', 'tiktok-feed' ) ];
					}
					$results = $response;
				} else {
					if ( $resultData ) {
						$results = $resultData;
					}
				}
			} elseif ( 'hashtag' == $search_type ) { //hashtag
				$response = $this->api->get_hashtag_media( $search_name, $images_number * 2 );
				if ( $response ) {
					if ( ! is_array( $response ) || ! count( $response ) ) {
						return [ 'error' => __( 'There are no publications in this hashtag yet', 'tiktok-feed' ) ];
					}
					$results = $response;
				} else {
					if ( $resultData ) {
						$results = $resultData;
					}
				}
			}

			if ( empty( $results ) ) {
				return [ 'error' => __( 'No images found', 'tiktok-feed' ) ];
			}

			$i = 0;
			foreach ( $results as $current => $result ) {
				if ( ! isset( $result['caption'] ) ) {
					$result['caption'] = "";
				}

				if ( $i >= $images_number ) {
					break;
				} else {
					$i ++;
				}

				if ( 'hashtag' == $search_type ) {
					if ( in_array( $result['author']['username'], explode( ',', $blocked_users ) ) ) {
						$images_number ++;
						continue;
					}
				}

				$image_data = $this->media_model( $result );

				if ( $this->is_blocked_by_word( $blocked_words, $image_data['caption'] ) ) {
					$images_number ++;
					continue;
				}

				$resultData[] = $image_data;
			} // end -> foreach

			update_option( $opt_name, $old_opts );

			if ( is_array( $resultData ) && ! empty( $resultData ) ) {
				set_transient( $opt_name, $resultData, $cache_hours * 60 * 60 );
			}

		} // end -> false === $instaData

		return $resultData;
	}

	/**
	 * Media Model
	 *
	 * @param  [type] $medias_array [description]
	 *
	 * @return [type]               [description]
	 */
	private function media_model( $medias_array ) {

		$m = array();

		foreach ( $medias_array as $prop => $value ) {

			switch ( $prop ) {
				case 'id':
					$m['id'] = $value;
					break;
				case 'link':
					$m['link'] = $value;
					break;
				case 'author':
					$m['user_id']  = $value['id'];
					$m['username'] = $value['username'];
					break;
				case 'text':
					$m['caption'] = $this->sanitize( $value );
					break;
				case 'date':
					$m['timestamp'] = $value;
					break;
				case 'video':
					$m['height'] = $value['height'];
					$m['width']  = $value['width'];
					$m['url']    = $value['url'];
					break;
				case 'digg_count':
					$m['likes_count'] = $value;
					break;
				case 'comment_count':
					$m['comment_count'] = $value;
					break;
				case 'covers':
					$m['sizes'] = [
						'default' => $value['default'],
						'origin'  => $value['origin'],
						'dynamic' => $value['dynamic'],
					];
					break;
			}

			if ( isset( $m['comment_count'] ) && isset( $m['likes_count'] ) ) {
				$m['popularity'] = (int) ( $m['comment_count'] ) + ( $m['likes_count'] );
			}
		}

		return $m;
	}

	/**
	 * Sort Function for timestamp Ascending
	 */
	public function sort_timestamp_ASC( $a, $b ) {
		return $a['timestamp'] > $b['timestamp'];
	}

	/**
	 * Sort Function for timestamp Descending
	 */
	public function sort_timestamp_DESC( $a, $b ) {
		return $a['timestamp'] < $b['timestamp'];
	}

	/**
	 * Sort Function for popularity Ascending
	 */
	public function sort_popularity_ASC( $a, $b ) {
		return $a['popularity'] > $b['popularity'];
	}

	/**
	 * Sort Function for popularity Descending
	 */
	public function sort_popularity_DESC( $a, $b ) {
		return $a['popularity'] < $b['popularity'];
	}

	/**
	 * Sanitize 4-byte UTF8 chars; no full utf8mb4 support in drupal7+mysql stack.
	 * This solution runs in O(n) time BUT assumes that all incoming input is
	 * strictly UTF8.
	 *
	 * @param string $input The input to be sanitised
	 *
	 * @return string sanitized input
	 */
	private function sanitize( $input ) {

		if ( ! empty( $input ) ) {
			$utf8_2byte       = 0xC0 /*1100 0000*/
			;
			$utf8_2byte_bmask = 0xE0 /*1110 0000*/
			;
			$utf8_3byte       = 0xE0 /*1110 0000*/
			;
			$utf8_3byte_bmask = 0XF0 /*1111 0000*/
			;
			$utf8_4byte       = 0xF0 /*1111 0000*/
			;
			$utf8_4byte_bmask = 0xF8 /*1111 1000*/
			;

			$sanitized = "";
			$len       = strlen( $input );
			for ( $i = 0; $i < $len; ++ $i ) {

				$mb_char = $input[ $i ]; // Potentially a multibyte sequence
				$byte    = ord( $mb_char );

				if ( ( $byte & $utf8_2byte_bmask ) == $utf8_2byte ) {
					$mb_char .= $input[ ++ $i ];
				} else if ( ( $byte & $utf8_3byte_bmask ) == $utf8_3byte ) {
					$mb_char .= $input[ ++ $i ];
					$mb_char .= $input[ ++ $i ];
				} else if ( ( $byte & $utf8_4byte_bmask ) == $utf8_4byte ) {
					// Replace with ? to avoid MySQL exception
					$mb_char = '';
					$i       += 3;
				}

				$sanitized .= $mb_char;
			}

			$input = $sanitized;
		}

		return $input;
	}

	/**
	 * This post is blocked by words?
	 *
	 * @param string|array $words
	 * @param string $text
	 *
	 * @return bool
	 */
	public function is_blocked_by_word( $words, $text ) {
		if ( empty( $words ) || empty( $text ) ) {
			return false;
		}
		$words_array = ! is_array( $words ) ? explode( ',', $words ) : $words;

		foreach ( $words_array as $word ) {
			$pos = stripos( mb_strtolower( $text ), mb_strtolower( trim( $word ) ) );
			if ( $pos !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get count of accounts
	 *
	 * @return int
	 */
	public function count_accounts() {
		$account = WTIK_Plugin::app()->getOption( WTIK_ACCOUNT_OPTION_NAME, array() );

		return count( $account );
	}

} // end of class WTIK_Widget
?>
