<?php
/**
 * Ajax plugin check licensing
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 2017 Webraftic Ltd
 * @version 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Обработчик ajax запросов для проверки, активации, деактивации лицензионного ключа
 *
 * @since 1.4.0
 */
function wtik_check_license() {
	check_admin_referer( 'license' );

	$action      = WTIK_Plugin::app()->request->post( 'license_action', false, true );
	$license_key = WTIK_Plugin::app()->request->post( 'licensekey', null );

	if ( empty( $action ) || ! in_array( $action, array( 'activate', 'deactivate', 'sync', 'unsubscribe' ) ) ) {
		wp_send_json_error( array( 'error_message' => __( 'Licensing action not passed or this action is prohibited!', 'tiktok-feed' ) ) );
		die();
	}

	$licensing = WTIK_Plugin::app()->premium;

	$result          = null;
	$success_message = '';

	switch ( $action ) {
		case 'activate':
			if ( empty( $license_key ) || strlen( $license_key ) > 32 ) {
				wp_send_json_error( array( 'error_message' => __( 'License key is empty or license key too long (license key is 32 characters long)', 'tiktok-feed' ) ) );
			} else {
				$result          = $licensing->activate( $license_key );
				$success_message = __( 'Your license has been successfully activated', 'tiktok-feed' );
			}
			break;
		case 'deactivate':
			$result          = $licensing->deactivate();
			$success_message = __( 'The license is deactivated', 'tiktok-feed' );
			break;
		case 'sync':
			$result          = $licensing->sync();
			$success_message = __( 'The license has been updated', 'tiktok-feed' );
			break;
		case 'unsubscribe':
			$result          = $licensing->cancel_paid_subscription();
			$success_message = __( 'Subscription success cancelled', 'tiktok-feed' );
			break;
	}

	if ( is_wp_error( $result ) ) {

		/**
		 * Экшен выполняет, когда проверка лицензии вернула ошибку
		 *
		 * @param string $action
		 * @param string $license_key
		 *
		 * @since 1.4.0
		 */
		add_action( 'wbcr/isw/check_license_error', $action, $license_key );

		wp_send_json_error( array( 'error_message' => $result->get_error_message() ) );
		die();
	}

	/**
	 * Экшен выполняет, когда проверка лицензии успешно завершена
	 *
	 * @param string $action
	 * @param string $license_key
	 *
	 * @since 1.4.0
	 */
	add_action( 'wbcr/isw/check_license_success', $action, $license_key );

	wp_send_json_success( array( 'message' => $success_message ) );

	die();
}

add_action( 'wp_ajax_wtik_check_license', 'wtik_check_license' );

