<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Основной класс плагина TikTok Feed Widget
 *
 * @author        Artem Prihodko <webtemyk@yandex.ru>
 * @copyright (c) 2019 Webraftic Ltd
 * @version       1.0
 */
class WTIK_Plugin extends Wbcr_Factory430_Plugin {

	/**
	 * @see self::app()
	 * @var Wbcr_Factory430_Plugin
	 */
	private static $app;

	/**
	 * @var array Список слайдеров
	 */
	public $sliders = array();

	/**
	 * Статический метод для быстрого доступа к интерфейсу плагина.
	 *
	 * Позволяет разработчику глобально получить доступ к экземпляру класса плагина в любом месте
	 * плагина, но при этом разработчик не может вносить изменения в основной класс плагина.
	 *
	 * Используется для получения настроек плагина, информации о плагине, для доступа к вспомогательным
	 * классам.
	 *
	 * @return Wbcr_Factory430_Plugin
	 */
	public static function app() {
		return self::$app;
	}

	/**
	 * Статический метод для быстрого доступа к классу соцсети.
	 *
	 * @param string $class
	 *
	 * @return $class
	 */
	public static function social( $class ) {
		return new $class;
	}

	/**
	 * Конструктор
	 *
	 * Применяет конструктор родительского класса и записывает экземпляр текущего класса в свойство $app.
	 * Подробнее о свойстве $app см. self::app()
	 *
	 * @param string $plugin_path
	 * @param array $data
	 *
	 * @throws Exception
	 */
	public function __construct( $plugin_path, $data ) {
		parent::__construct( $plugin_path, $data );

		self::$app = $this;

		$this->global_scripts();

		if ( is_admin() ) {
			// Регистрации класса активации/деактивации плагина
			$this->init_activation();

			// Инициализация скриптов для бэкенда
			$this->admin_scripts();

			//Подключение файла проверки лицензии
			require( WTIK_PLUGIN_DIR . '/admin/ajax/check-license.php' );
		} else {
			$this->front_scripts();
		}

		add_action( 'wp_ajax_wtik_delete_account', array( $this, 'delete_feed' ) );
	}

	protected function init_activation() {
		include_once( WTIK_PLUGIN_DIR . '/admin/class-wtik-activation.php' );
		$this->registerActivation( 'WTIK_Activation' );
	}

	/**
	 * Регистрирует классы страниц в плагине
	 */
	private function register_pages() {
		require_once WTIK_PLUGIN_DIR . '/admin/class-wtik-page.php';

		//$fb = new WTIK_Facebook();

		self::app()->registerPage( 'WTIK_WidgetsPage', WTIK_PLUGIN_DIR . '/admin/pages/widgets.php' );
		self::app()->registerPage( 'WTIK_SettingsPage', WTIK_PLUGIN_DIR . '/admin/pages/settings.php' );
		//self::app()->registerPage( 'WTIK_LicensePage', WTIK_PLUGIN_DIR . '/admin/pages/license.php' );
		//self::app()->registerPage( 'WTIK_AboutPage', WTIK_PLUGIN_DIR . '/admin/pages/about.php' );
	}

	/**
	 * Код для админки
	 */
	private function admin_scripts() {
		// Регистрация страниц
		$this->register_pages();

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_assets' ] );
		//add_action( 'admin_notices', [ $this, 'new_api_admin_notice' ] );
	}

	/**
	 * Код для админки и фронтенда
	 */
	private function global_scripts() {
		require_once WTIK_PLUGIN_DIR . '/includes/helpers.php';
		require_once WTIK_PLUGIN_DIR . '/includes/class-tiktok-api.php';
	}

	/**
	 * Код для фронтенда
	 */
	private function front_scripts() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function admin_enqueue_assets( $hook_suffix ) {
		wp_enqueue_script( 'wtik-tinymce-button', WTIK_PLUGIN_URL . '/admin/assets/js/tinymce_button.js', array( 'jquery' ), WTIK_PLUGIN_VERSION, false );
		$wtik_shortcodes = $this->get_isw_widgets();
		wp_localize_script( 'wtik-tinymce-button', 'wtik_shortcodes', $wtik_shortcodes );

	}

	public function enqueue_assets() {
		wp_enqueue_style( 'wtiktok-styles', WTIK_PLUGIN_URL . '/assets/css/wtiktok.css', array(), WTIK_PLUGIN_VERSION );
	}

	/**
	 * Метод проверяет активацию премиум плагина и наличие действующего лицензионнного ключа
	 *
	 * @return bool
	 */
	public function is_premium() {
		if ( $this->premium->is_active() && $this->premium->is_activate() //&& is_plugin_active( "{$this->premium->get_setting('slug')}/{$this->premium->get_setting('slug')}.php" )
		) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add/update account in database
	 *
	 * @param $username
	 *
	 * @return bool
	 */
	public function update_feed( $profile ) {
		if ( $profile && is_array( $profile ) ) {
			$pro = WTIK_Plugin::app()->getOption( WTIK_ACCOUNT_OPTION_NAME, [] );
			if ( ! is_array( $pro ) ) {
				$pro = [];
			}
			$pro[ $profile['id'] ] = $profile;

			return WTIK_Plugin::app()->updateOption( WTIK_ACCOUNT_OPTION_NAME, $pro );
		}

		return false;
	}

	/**
	 * Get feeds or feed from database
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function get_feeds( $id = 0 ) {
		if ( $id ) {
			$feeds = $this->getOption( WTIK_ACCOUNT_OPTION_NAME, [] );
			if ( is_array( $feeds ) && ! empty( $feeds ) ) {
				foreach ( $feeds as $feed ) {
					if ( isset( $feed['id'] ) && $feed['id'] == $id ) {
						return $feed;
					}
				}
			}
		}

		return $this->getOption( WTIK_ACCOUNT_OPTION_NAME, [] );
	}

	/**
	 * Ajax Call to delete account
	 * @return void
	 */
	public function delete_feed() {
		if ( isset( $_POST['item_id'] ) && ! empty( $_POST['item_id'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( - 1 );
			} else {
				check_ajax_referer( 'wtik_nonce', 'nonce' );

				$accounts = WTIK_Plugin::app()->getPopulateOption( WTIK_ACCOUNT_OPTION_NAME, [] );
				$item_id = esc_html( $_POST['item_id']);
				if ( isset( $accounts[ $item_id ] ) ) {
					unset( $accounts[ $item_id ] );
				}

				WTIK_Plugin::app()->updatePopulateOption( WTIK_ACCOUNT_OPTION_NAME, $accounts );

				wp_send_json_success( __( 'Feed deleted successfully', 'tiktok-feed' ) );
			}
		}
	}

	/**
	 * Получает все виджеты этого плагина
	 *
	 * @return array
	 */
	public function get_isw_widgets() {
		$settings = WTIK_Widget::app()->get_settings();
		$result   = array();
		foreach ( $settings as $key => $widget ) {
			$result[] = array(
				'title' => $widget['title'],
				'id'    => $key,
			);
		}

		return $result;
	}
}
