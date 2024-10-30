<?php
/*
Plugin Name: Feed Widget for TikTok
Plugin URI: https://cm-wp.com/tiktok-feed
Version: 1.0.1
Description: Widget for TikTok Feed is a responsive slider widget that shows 12 latest images from a public Instagram user and up to 18 images from a hashtag.
Author: creativemotion
Author URI: https://cm-wp.com/
Text Domain: tiktok-feed
Domain Path: /languages
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Подключаем класс проверки совместимости
require_once( dirname( __FILE__ ) . '/libs/factory/core/includes/class-factory-requirements.php' );

$plugin_info = array(
	'prefix'               => 'wtik_',
	// Префикс для базы данных и полей формы. Строка должна соответствовать условию [A-z0-9_].
	'plugin_name'          => 'wtiktok',
	// Кодовое название плагина, используется как уникальный идентификатор. Строка должна соответствовать условию [A-z0-9_].
	'plugin_title'         => __( 'Feed Widget for TikTok', 'tiktok-feed' ),
	// Название плагина. То же что и Plugin Name. Используется в интерфейсе и сообщениях.
	'plugin_text_domain'   => 'tiktok-feed',
	// Идентификатор перевода, указывается в load_plugin_textdomain()

	// Служба поддержки
	// Указываем ссылки и имена страниц сайта плагина, чтобы иметь к ним доступ внутри плагина.
	'support_details'      => array(
		'url'       => 'https://cm-wp.com/tiktok-feed',// Ссылка на сайт плагина
		'pages_map' => array(
			'features' => 'premium-features', // {site}/premium-features "страница возможности"
			'pricing'  => 'pricing', // {site}/prices страница "цены"
			'support'  => 'support', // {site}/support страница "служба поддержки"
			'docs'     => 'docs' // {site}/docs страница "документация"
		)
	),

	// Настройка обновлений плагина
	// Имеется ввиду настройка обновлений из удаленного репозитория. Это может быть wordpress.org, freemius.com, codecanyon.com
	'has_updates'          => true,
	// Нужно ли проверять обновления для этого плагина
	'updates_settings'     => array(
		'repository'        => 'wordpress',
		// Тип репозитория из которого получаем обновления. Может быть wordpress, freemius
		'slug'              => 'tiktok-feed',
		// Слаг плагина в удаленном репозитории
		'maybe_rollback'    => true,
		// Можно ли делать откат к предыдущей версии плагина?
		'rollback_settings' => array(
			'prev_stable_version' => '0.0.0'
			// Нужно указать предыдущую стабильную версию, к которой нужно сделать откат.
		)
	),

	// Настройка премиум плагина
	// Сюда входят настройки лицензирования и премиум обновлений плагина и его надстройки
	'has_premium'          => true,
	// Есть ли у текущего плагина премиум? Если false, премиум модуль загружен не будет
	'license_settings'     => array(
		'has_updates'      => true,
		'provider'         => 'freemius',
		// Тип лицензионного поставщика, может быть freemius, codecanyon, templatemonster
		'slug'             => 'tiktok-feed-premium',
		// Слаг плагина в выбранном поставщике лицензий и обновлений
		'plugin_id'        => '4272',
		// ID плагина в freemius.com
		'public_key'       => 'pk_5152229a4aba03187267a8bc88874',
		// Публичный ключ плагина в freemius.com
		'price'            => 39,
		// Минимальная цена плагина, выводится в рекламных блоках
		// Настройка обновлений премиум плагина
		'updates_settings' => array(
			'maybe_rollback'    => true, // Можно ли делать откат к предыдущей версии плагина?
			'rollback_settings' => array(
				'prev_stable_version' => '0.0.0'
				// Нужно указать предыдущую стабильную версию, к которой нужно сделать откат.
			)
		)
	),

	// Настройки рекламы от CreativeMotion
	'render_adverts'       => true,
	// Показывать рекламу CreativeMotion в админке Wordpress?
	'adverts_settings'     => array(
		'dashboard_widget' => true,
		// если true, показывать виджет новостей на страницу Dashboard
		'right_sidebar'    => true,
		// если true, показывать виджет в правом сайбаре интерфейса плагина
		'notice'           => true,
		// если true, показывать сквозное уведомление на всех страницах админ панели Wordpress
	),

	// Подключаемые модуль фреймворка
	// Необходимые для ускоренной разработки продуктов Webcrfatic
	'load_factory_modules' => array(
		array( 'libs/factory/bootstrap', 'factory_bootstrap_431', 'admin' ),
		// Модуль позволяет использовать различные js виджеты и стили оформление форм.
		array( 'libs/factory/forms', 'factory_forms_428', 'admin' ),
		// Модуль позволяет быстро создавать формы и готовые поля настроек
		array( 'libs/factory/pages', 'factory_pages_430', 'admin' ),
		// Модуль позволяет создавать страницы плагина, в том числе шаблонизированные страницы
		array( 'libs/factory/freemius', 'factory_freemius_118', 'all' ),
		// Модуль для работы с freemius.com, содержит api библиотеку и провайдеры для премиум менеджера
		array( 'libs/factory/adverts', 'factory_adverts_110', 'admin' )
		// Модуль для показа рекламы в админпанели Wordpress, вся реклама вытягивается через API Creative Motion
	)
);

$wtik_compatibility = new Wbcr_Factory430_Requirements( __FILE__, array_merge( $plugin_info, array(
	'plugin_already_activate'          => defined( 'WTIK_PLUGIN_ACTIVE' ),
	'required_php_version'             => '5.4',
	'required_wp_version'              => '4.2.0',
	'required_clearfy_check_component' => false
) ) );

/**
 * If the plugin is compatible, then it will continue its work, otherwise it will be stopped,
 * and the user will throw a warning.
 */
if ( ! $wtik_compatibility->check() ) {
	return;
}
/********************************************/
// Устанавливает статус плагина, как активный
define( 'WTIK_PLUGIN_ACTIVE', true );
// Версия плагина
define( 'WTIK_PLUGIN_VERSION', $wtik_compatibility->get_plugin_version() );
define( 'WTIK_PLUGIN_FILE', __FILE__ );
define( 'WTIK_ABSPATH', dirname( __FILE__ ) );
define( 'WTIK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WTIK_PLUGIN_SLUG', dirname( plugin_basename( __FILE__ ) ) );
// Ссылка к директории плагина
define( 'WTIK_PLUGIN_URL', plugins_url( null, __FILE__ ) );
// Директория плагина
define( 'WTIK_PLUGIN_DIR', dirname( __FILE__ ) );
/********************************************/




/**
 * -----------------------------------------------------------------------------
 * PLUGIN INIT
 * -----------------------------------------------------------------------------
 */
require_once( WTIK_PLUGIN_DIR . '/libs/factory/core/boot.php' );
require_once( WTIK_PLUGIN_DIR . '/includes/class-plugin.php' );

try {
	new WTIK_Plugin( __FILE__, array_merge( $plugin_info, array(
		'plugin_version' => WTIK_PLUGIN_VERSION
	) ) );
} catch ( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define( 'WTIK_PLUGIN_THROW_ERROR', true );

	$wtik_plugin_error_func = function () use ( $e )
	{
		$error = sprintf( "The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'TikTok Feed Widget', $e->getMessage(), $e->getCode() );
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action( 'admin_notices', $wtik_plugin_error_func );
	add_action( 'network_admin_notices', $wtik_plugin_error_func );
}

define( 'WTIK_INSTAGRAM_CLIENT_ID', '2555361627845349' );
define( 'WTIK_FACEBOOK_CLIENT_ID', '776212986124330' );

/*
 * Константа определяет какое имя опции для хранения данных.
 * Нужно для отладки и последующего бесшовного перехода
 */
define( 'WTIK_ACCOUNT_OPTION_NAME', 'account' );

/*******************************************************************************/
/**
 * On widgets Init register Widget
 */
require_once WTIK_PLUGIN_DIR . '/includes/class-social.php';

require_once WTIK_PLUGIN_DIR . "/includes/class-tiktok-widget.php";
add_action( 'widgets_init', array( 'WTIK_Widget', 'register_widget' ) );

?>