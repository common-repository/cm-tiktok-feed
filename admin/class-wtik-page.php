<?php
/**
 * Class of plugin page. Must be registered in file admin/class-prefix-page.php
 *
 * @author        Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 02.12.2018, Webcraftic
 * @see           Wbcr_FactoryPages430_AdminPage
 *
 * @version       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WTIK_Page extends Wbcr_FactoryPages430_AdminPage {

	/**
	 * Name of the template to get content of. It will be based on plugins /admin/views/ dir.
	 * /admin/views/tab-{$template_name}.php
	 *
	 * @var string
	 */
	public $template_name = "main";

	public function assets( $scripts, $styles ) {
		$this->scripts->request( 'jquery' );

		$this->scripts->request( [
			'control.checkbox',
			'control.dropdown'
		], 'bootstrap' );

		$this->styles->request( [
			'bootstrap.core',
			'bootstrap.form-group',
			'bootstrap.separator',
			'control.dropdown',
			'control.checkbox',
		], 'bootstrap' );

		wp_enqueue_style( 'wtiktok-admin-styles', WTIK_PLUGIN_URL . '/admin/assets/css/wtiktok-admin.css', array(), WTIK_PLUGIN_VERSION );
		wp_enqueue_script( 'wtiktok-admin-script', WTIK_PLUGIN_URL . '/admin/assets/js/wtiktok-admin.js', array( 'jquery' ), WTIK_PLUGIN_VERSION, true );
		wp_localize_script( 'wtiktok-admin-script', 'wtik', array(
			'nonce'          => wp_create_nonce( 'wtik_nonce' ),
			'remove_account' => __( 'Are you sure want to delete this account?', 'tiktok-feed' ),
		) );
		wp_localize_script( 'wtiktok-admin-script', 'add_account_nonce', array(
			'nonce' => wp_create_nonce( "addAccountByToken" ),
		) );
	}

	/**
	 * Render and return content of the template.
	 * /admin/views/tab-{$template_name}.php
	 *
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed Content of the page
	 */
	public function render( $name = '', $args = [] ) {
		if ( $name == '' ) {
			$name = $this->template_name;
		}
		ob_start();
		if ( is_callable( $name ) ) {
			echo call_user_func( $name );
		} elseif ( strpos( $name, DIRECTORY_SEPARATOR ) !== false && ( is_file( $name ) || is_file( $name . '.php' ) ) ) {
			if ( is_file( $name ) ) {
				$path = $name;
			} else {
				$path = $name . '.php';
			}
		} else {
			$path = WTIK_PLUGIN_DIR . "/admin/views/tab-{$name}.php";
		}
		if ( ! is_file( $path ) ) {
			return '';
		}
		extract( $args );
		include $path;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Show rendered template - $template_name
	 */
	public function indexAction() {
		echo $this->render();
	}

}


