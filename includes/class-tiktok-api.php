<?php
/**
 * Class of TikTok feed
 *
 * @author        Artem Prihodko <webtemyk@yandex.ru>, Github: https://github.com/temyk
 * @copyright (c) 2020, Webcraftic
 *
 * @version       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WTIK_PLUGIN_DIR . '/includes/class-social.php';

class WTIK_Api extends WTIK_Social {

	const SELF_URL = 'https://www.tiktok.com';
	const API_URL = 'https://www.tiktok.com/node';


	/**
	 * Name of the Social
	 *
	 * @var string
	 */
	public $social_name = "tiktok";

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_wis_add_facebook_page_by_token', array( $this, 'add_account' ) );
	}

	/**
	 * Get TikTok URL
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function get_tiktok_url( $url = '' ) {
		return self::SELF_URL . $url;
	}

	/**
	 * Get API URL
	 *
	 * @param $url
	 *
	 * @return string
	 */
	private function get_api_url( $url = '' ) {
		return self::API_URL . $url;
	}

	private function request( $url = null, $args = array() ) {
		$args     = wp_parse_args( $args, array( 'timeout' => 30 ) );
		$response = $this->validateResponse( wp_remote_get( $url, $args ) );

		return (array) $response;
	}

	private function validateResponse( $json = null ) {
		if ( ! ( $response = json_decode( wp_remote_retrieve_body( $json ), true ) ) || 200 !== wp_remote_retrieve_response_code( $json ) ) {
			if ( is_wp_error( $json ) ) {
				$response = array(
					'error'   => 1,
					'message' => $json->get_error_message()
				);
			} else {
				$response = array(
					'error'   => 1,
					'message' => esc_html__( 'Unknow error occurred, please try again', 'tiktok-feed' )
				);
			}
		}

		return $response;
	}

	/**
	 * @return string
	 */
	public function getSocialName() {
		return $this->social_name;
	}

	/**
	 * Get Account data by NAME from option in wp_options
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function getAccountByName( $name ) {
		$token = WTIK_Plugin::app()->getOption( WTIK_ACCOUNT_OPTION_NAME );

		return $token[ $name ];
	}

	/**
	 * Get account info
	 *
	 * @param $name
	 *
	 * @return array|bool
	 */
	public function get_account( $username ) {
		if ( ! $username ) {
			return false;
		}

		$url = $this->get_api_url( "/share/user/@{$username}" );

		$response = $this->request( $url );

		if ( ! isset( $response['body']['userData']['userId'] ) ) {
			return false;
		}

		return array(
			'type'               => 'account',
			'id'                 => $response['body']['userData']['userId'],
			'full_name'          => $response['body']['userData']['nickName'],
			'username'           => $response['body']['userData']['uniqueId'],
			'following_count'    => $response['body']['userData']['following'],
			'fans_count'         => $response['body']['userData']['fans'],
			'heart_count'        => $response['body']['userData']['heart'],
			'video_count'        => $response['body']['userData']['video'],
			'verified'           => @$response['body']['userData']['verified'],
			'tagline'            => @$response['body']['userData']['signature'],
			'profile_pic_url'    => @$response['body']['userData']['covers'][0],
			'profile_pic_url_hd' => @$response['body']['userData']['coversMedium'][0],
			'link'               => $this->get_tiktok_url( "/@{$username}" )
		);
	}

	/**
	 * @param null $username
	 * @param null $after
	 *
	 * @return bool|array
	 */
	public function get_account_media( $username = null, $limit = 20 ) {

		$profile = $this->get_account( $username );

		if ( ! isset( $profile['id'] ) ) {
			return false;
		}

		$url = add_query_arg( array(
			'id'        => $profile['id'],
			'minCursor' => 0,
			'maxCursor' => 0,
			'count'     => $limit,
			'type'      => 1
		), $this->get_api_url( "/video/feed" ) );

		$response = $this->request( $url );

		if ( ! isset( $response['body'] ) ) {
			return false;
		}

		return $this->parse_media( $response['body'] );
	}

	/**
	 * Get hashtag info
	 *
	 * @param $hashtag
	 *
	 * @return array|bool
	 */
	public function get_hashtag( $hashtag ) {

		if ( ! $hashtag ) {
			return false;
		}

		$url = $this->get_api_url( "/share/tag/{$hashtag}" );

		$response = $this->request( $url );

		if ( ! isset( $response['body']['challengeData']['challengeId'] ) ) {
			return false;
		}

		return array(
			'type'               => 'hashtag',
			'id'                 => $response['body']['challengeData']['challengeId'],
			'full_name'          => $response['body']['challengeData']['challengeName'],
			'username'           => $hashtag,
			'video_count'        => $response['body']['challengeData']['posts'],
			'views_count'        => $response['body']['challengeData']['views'],
			'tagline'            => @$response['body']['challengeData']['text'],
			'profile_pic_url'    => @$response['body']['challengeData']['covers'][0],
			'profile_pic_url_hd' => @$response['body']['challengeData']['coversMedium'][0],
			'link'               => $this->get_tiktok_url( "/tag/{$hashtag}" )
		);
	}

	/**
	 * @param null $hashtag
	 * @param null $after
	 *
	 * @return bool|array
	 */
	public function get_hashtag_media( $hashtag = null, $limit = 20 ) {

		$profile = $this->get_hashtag( $hashtag );

		if ( ! isset( $profile['id'] ) ) {
			return false;
		}

		$url = add_query_arg( array(
			'id'        => $profile['id'],
			'minCursor' => 0,
			'maxCursor' => 0,
			'count'     => $limit,
			'type'      => 3
		), $this->get_api_url( "/video/feed" ) );

		$response = $this->request( $url );

		if ( ! isset( $response['body'] ) ) {
			return false;
		}

		return $this->parse_media( $response['body'] );
	}

	/**
	 * @param $data
	 * @param null $last_id
	 *
	 * @return array
	 */
	public function parse_media( $data, $last_id = null ) {

		static $load = false;
		static $i = 1;

		if ( ! $last_id ) {
			$load = true;
		}

		$tiktok_items = array();

		if ( isset( $data['itemListData'] ) && is_array( $data['itemListData'] ) && ! empty( $data['itemListData'] ) ) {

			foreach ( $data['itemListData'] as $item ) {

				if ( $load ) {

					preg_match_all( "/#(\\w+)/", $item['itemInfos']['text'], $hashtags );

					$tiktok_items[] = array(
						//'i'             => $i,
						'id'            => $item['itemInfos']['id'],
						'covers'        => array(
							'default' => $item['itemInfos']['covers'][0],
							'origin'  => $item['itemInfos']['coversOrigin'][0],
							'dynamic' => $item['itemInfos']['coversDynamic'][0],
						),
						'video'         => array(
							'url'    => $item['itemInfos']['video']['urls'][0],
							'width'  => $item['itemInfos']['video']['videoMeta']['width'],
							'height' => $item['itemInfos']['video']['videoMeta']['height'],
						),
						'share_count'   => $item['itemInfos']['shareCount'],
						'comment_count' => $item['itemInfos']['commentCount'],
						'digg_count'    => $item['itemInfos']['diggCount'],
						'play_count'    => $item['itemInfos']['playCount'],
						'text'          => preg_replace( '/(?<!\S)#([0-9a-zA-Z]+)/', "<a target=\"_blank\" href=\"{$this->get_tiktok_url()}/tag/$1\">#$1</a>", htmlspecialchars( $item['itemInfos']['text'] ) ),
						'hashtags'      => @$hashtags[1],
						'link'          => $this->get_tiktok_url( "/@{$item['authorInfos']['uniqueId']}/video/{$item['itemInfos']['id']}" ),
						'date'          => $item['itemInfos']['createTime'],
						'author'        => array(
							'id'        => $item['authorInfos']['userId'],
							'username'  => $item['authorInfos']['uniqueId'],
							'full_name' => $item['authorInfos']['nickName'],
							'tagline'   => $item['authorInfos']['signature'],
							'verified'  => $item['authorInfos']['verified'],
							'image'     => array(
								'small'  => $item['authorInfos']['covers'][0],
								'medium' => $item['authorInfos']['coversMedium'][0],
								'larger' => $item['authorInfos']['coversLarger'][0],
							),
							'link'      => $this->get_tiktok_url( "/@{$item['authorInfos']['uniqueId']}" ),
						)
					);
				}
				if ( $last_id && ( $last_id == $i ) ) {
					$i    = $last_id;
					$load = true;
				}
				$i ++;
			}
		}

		return $tiktok_items;
	}

}
