<?php
/**
 * Class of Social feed
 *
 * @author        Artem Prihodko <webtemyk@yandex.ru>, Github: https://github.com/temyk
 * @copyright (c) 28.12.2019, Webcraftic
 *
 * @version       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WTIK_Social {

	/**
	 * @see self::app()
	 */
	private static $app;

	/**
	 * @var WTIK_Plugin
	 */
	public $plugin;

	/**
	 * Name of the Social
	 *
	 * @var string
	 */
	public $social_name = "";

	/**
	 * Статический метод для быстрого доступа к интерфейсу плагина.
	 *
	 * @return WTIK_Social
	 */
	public static function app() {
		return self::$app;
	}

	/**
	 * WTIK_Social constructor.
	 */
	public function __construct() {
		self::$app = $this;
		$this->plugin = WTIK_Plugin::app();
	}

	/**
	 * @return string
	 */
	public function getSocialName() {
		return $this->social_name;
	}

	/**
	 * Обработка данных на вкладке соцсети
	 */
	public function tabAction() {
		echo $this->social_name;
	}

	/**
	 * Add account
	 *
	 */
	public function addAccount() {

	}

}
