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
class WTIK_AboutPage extends WTIK_Page {

	/**
	 * Тип страницы
	 * options - предназначена для создании страниц с набором опций и настроек.
	 * page - произвольный контент, любой html код
	 *
	 * @var string
	 */
	public $type = 'page';

	/**
	 * Menu icon (only if a page is placed as a main menu).
	 * For example: '~/assets/img/menu-icon.png'
	 * For example dashicons: '\f321'
	 * @var string
	 */
	public $menu_icon = '';

	/**
	 * @var string
	 */
	public $page_menu_dashicon;

	/**
	 * @param WTIK_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->id            = "about";
		$this->menu_target   = "widgets-" . $plugin->getPluginName();
		$this->page_title    = __( 'About TikTok Feed Widget', 'tiktok-feed' );
		$this->menu_title    = __( 'About', 'tiktok-feed' );
		$this->template_name = "about";

		parent::__construct( $plugin );

		$this->plugin = $plugin;
	}
}