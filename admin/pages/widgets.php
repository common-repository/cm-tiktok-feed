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
class WTIK_WidgetsPage extends WTIK_Page {

	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type;

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
	public $menu_position = 58;

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
	 * @param WTIK_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->id             = "widgets";
		$this->page_title     = __( 'TikTok Feed Widgets', 'tiktok-feed' );
		$this->menu_title     = __( 'TikTok Feed Widgets', 'tiktok-feed' );
		$this->menu_sub_title = __( 'Widgets', 'tiktok-feed' );
		$this->menu_icon      = '~/admin/assets/img/tiktok.png';
		$this->capabilitiy    = "manage_options";
		$this->template_name  = "widgets";

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}

	public function assets( $scripts, $styles ) {
		//Widgets scripts
		$this->scripts->request( 'admin-widgets' );

		if ( wp_is_mobile() ) {
			$this->scripts->request( 'jquery-touch-punch' );
		}

	}

	/**
	 * @inheritDoc
	 */
	public function indexAction() {
		$sidebars_widgets = get_option( 'sidebars_widgets', [] );
		$tiktok_widgets    = get_option( 'widget_wtiktok_feed', [] );

		/*************************/
		ob_start();
		require_once ABSPATH . "wp-admin/includes/widgets.php";
		$sidebars_widgets = wp_get_sidebars_widgets();
		global $wp_registered_widgets, $wp_registered_sidebars;
		$isset_widgets = false;
		wp_nonce_field( 'save-sidebar-widgets', '_wpnonce_widgets' );
		if ( ! empty( $sidebars_widgets ) ) {
			foreach ( $sidebars_widgets as $key => $sidebar ) {
				foreach ( $sidebar as $widget ) {
					if ( strstr( $widget, 'wtiktok_feed' ) ) {
						wp_list_widget_controls( $key, $wp_registered_sidebars[ $key ]['name'] );
						$isset_widgets = true;
						break;
					}
				}
			}
		}
		if ( ! $isset_widgets ) {
			echo "<h2>" . sprintf( __( "You don't have any TikTok Feed widgets. Go to the Wordpress <a href='%1s'>Widgets</a> page and add it.", 'tiktok-feed' ), admin_url( 'widgets.php' ) ) . "</h2>";
		}
		$widgets = ob_get_contents();
		ob_end_clean();

		$data = [
			'content'       => $widgets,
			'insta_widgets' => $tiktok_widgets,
		];
		echo $this->render( '', $data );
	}
}