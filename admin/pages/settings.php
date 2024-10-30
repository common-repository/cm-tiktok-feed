<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The page Settings.
 *
 * @since 1.0.0
 */
class WTIK_SettingsPage extends WTIK_Page {

	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type = 'options';

	/**
	 * The id of the page in the admin menu.
	 *
	 * Mainly used to navigate between pages.
	 *
	 * @since 1.0.0
	 * @see   FactoryPages430_AdminPage
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Menu icon (only if a page is placed as a main menu).
	 * For example: '~/assets/img/menu-icon.png'
	 * For example dashicons: '\f321'
	 * @var string
	 */
	public $menu_icon;

	/**
	 * @var string
	 */
	public $page_menu_dashicon = 'dashicons-performance';

	/**
	 * Menu position (only if a page is placed as a main menu).
	 * @link http://codex.wordpress.org/Function_Reference/add_menu_page
	 * @var string
	 */
	public $menu_position = 59;

	/**
	 * Menu type. Set it to add the page to the specified type menu.
	 * For example: 'post'
	 * @var string
	 */
	public $menu_post_type = null;

	/**
	 * Visible page title.
	 * For example: 'License Manager'
	 * @var string
	 */
	public $page_title;

	/**
	 * Visible title in menu.
	 * For example: 'License Manager'
	 * @var string
	 */
	public $menu_title;

	/**
	 * If set, an extra sub menu will be created with another title.
	 * @var string
	 */
	public $menu_sub_title;

	/**
	 *
	 * @var
	 */
	public $page_menu_short_description;

	/**
	 * Заголовок страницы, также использует в меню, как название закладки
	 *
	 * @var bool
	 */
	public $show_page_title = true;

	/**
	 * @var int
	 */
	public $page_menu_position = 20;

	/**
	 * @var WTIK_Api
	 */
	public $api;


	/**
	 * @param WTIK_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->id            = "feeds";
		$this->page_title    = __( 'TikTok Feeds', 'tiktok-feed' );
		$this->menu_title    = __( 'Feeds', 'tiktok-feed' );
		$this->menu_target   = "widgets-" . $plugin->getPluginName();
		$this->menu_icon     = '~/admin/assets/img/wis.png';
		$this->capabilitiy   = "manage_options";
		$this->template_name = "settings";

		parent::__construct( $plugin );

		$this->plugin = $plugin;
		$this->api    = new WTIK_Api();
	}

	public function assets( $scripts, $styles ) {
		parent::assets( $scripts, $styles );
	}

	public function indexAction() {
		wp_enqueue_style( 'wtik-tabs-style', WTIK_PLUGIN_URL . '/admin/assets/css/component.css', array(), WTIK_PLUGIN_VERSION );
		if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
			switch ( $_GET['tab'] ) {
				case "tiktok":
					$this->tiktok();
					break;
			}
		} else {
			$this->tiktok();
		}

		parent::indexAction();
	}

	/**
	 * Логика на вкладке TikTok
	 */
	public function tiktok() {
		if ( isset( $_POST['wtik_feed_type'] ) ) {
			switch ( $_POST['wtik_feed_type'] ) {
				case "hashtag":
					if ( isset( $_POST['wtik_feed_hashtag'] ) ) {
						$profile = $this->api->get_hashtag( esc_html( $_POST['wtik_feed_hashtag']) );
					}
					break;
				case "account":
				default:
					if ( isset( $_POST['wtik_feed_account'] ) ) {
						$profile = $this->api->get_account( esc_html( $_POST['wtik_feed_account']) );
					}
					break;
			}

			$this->plugin->update_feed( $profile );
		}
	}

}
