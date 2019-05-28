<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'VillaTheme_Instagram' ) ) {
	/**
	 * Create Import Image from Instagram
	 * Class VillaTheme_Instagram
	 */
	class VillaTheme_Instagram {
		protected $ins_url = 'https://www.instagram.com/';
		protected $data = array();
		protected $setting;


		public function __construct() {
			$this->setting = new WOO_F_LOOKBOOK_Data();
			//		$this->import( false );
			//		$this->check_duplicate( 'Be6V4soHL9g' );
			//		die;
		}

		/**
		 * Import Lookbook
		 * @return bool
		 */
		public function import( $cache = true ) {

			//		$this->data = get_transient( 'wlb_instagram_data' );
			if ( ! $this->data || ! $cache ) {
				$this->get();

				if ( is_array( $this->data ) && count( $this->data ) ) {
					set_transient( 'wlb_instagram_data', $this->data, 86400 );
				} else {
					return false;
				}
			}

			$post_status = 'pending';


			foreach ( $this->data as $image ) {
				$shortcode = str_replace( '/', '', str_replace( 'https://www.instagram.com/p/', '', $image['link'] ) );
				$post_id   = $this->check_duplicate( $shortcode );
				if ( ! $post_id ) {
					$thumb_id = $this->upload_image( $image['images']['standard_resolution']['url'], $shortcode );
					//					print_r($thumb_id);

					if ( ! $thumb_id ) {
						return false;
					}
					$post_arg = array( // Set up the basic post data to insert for our lookbook
						'post_status' => $post_status,
						'post_title'  => $image['caption']['text'],
						'post_type'   => 'woocommerce-lookbook',
						'post_date'   => date( "Y-m-d H:i:s", $image['created_time'] )
					);

					$post_id = wp_insert_post( $post_arg ); // Insert the post returning the new post id

					if ( ! $post_id ) {
						return false;
					}

					$metabox = array(
						'image'     => $thumb_id,
						'instagram' => "1",
						'code'      => $shortcode,
						'date'      => $image['created_time'],
						'comments'  => $image['comments']['count'] ? $image['comments']['count'] : 0,
						'likes'     => $image['likes']['count'] ? $image['likes']['count'] : 0,
					);
					update_post_meta( $post_id, 'wlb_params', $metabox );
				}
			}


		}

		/**
		 * Check post duplicate
		 *
		 * @param $code
		 *
		 * @return bool
		 */
		protected function check_duplicate( $code ) {
			$args      = array(
				'post_type'   => 'woocommerce-lookbook',
				'post_status' => array(
					'any',
					'auto-draft',
					'trash', // - post is in trashbin (available with Version 2.9).
				),
				'meta_query'  => array(
					array(
						'key'     => 'wlb_params',
						'value'   => $code,
						'compare' => 'LIKE',
					),
				)
			);
			$the_query = new WP_Query( $args );

			//		print_r( $the_query );
			//		die;
			// The Loop
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					return get_the_ID();
				}
				wp_reset_postdata();

			} else {
				return false;
			}

		}

		/**
		 * Get image instagram link
		 * @return bool
		 */
		public function get() {
			if ( $this->setting->get_access_token() ) {
				$get_count    = 12;
				$access_token = $this->setting->get_access_token();
				$url          = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=' . $access_token . '&count=' . $get_count;

				$data = $this->remote( $url, true );

				if ( ! $data ) {
					return false;
				}
				$this->data = $data['data'];
			} else {
				return false;
			}
		}

		public function compare() {
		}

		/**
		 * Get data
		 *
		 * @param $url
		 *
		 * @return array|bool|mixed|null|object|WP_Error
		 */
		protected function remote( $url, $api = false ) {

			$request = wp_remote_get(
				$url, array(
					'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.167 Safari/537.36',
					'timeout'    => 20,
				)
			);
			//			print_r($request);die;
			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				if ( $api ) {
					$html = $request['body'];

					return json_decode( $html, true );
				} else {
					$html = $request['body'];
					$html = str_replace( "\n", ' ', $html );
					$html = str_replace( "\t", ' ', $html );
					$html = str_replace( "\r", ' ', $html );
					$html = str_replace( "\0", ' ', $html );
					preg_match_all( '/(_sharedData\s=)+(.+?);<\/script>/i', $html, $result );
					if ( isset( $result[2][0] ) ) {
						$request = trim( $result[2][0] );
					} else {
						return false;
					}
					if ( $request ) {
						$request = json_decode( $request, true );
					} else {
						return false;
					}

					return $request;
				}
			} else {
				return false;
			}

		}

		/**
		 * Upload image
		 *
		 * @param $url
		 *
		 * @return int|object
		 */
		protected function upload_image( $url, $desc = '' ) {
			//add product image:
			//require_once 'inc/add_pic.php';
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$thumb_url = $url;
			//							print_r($thumb_url);

			// Download file to temp location
			$tmp = download_url( $thumb_url );
			//		print_r($tmp);die;
			// Set variables for storage
			// fix file name for query strings
			preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches );
			$file_array['name']     = basename( $matches[0] );
			$file_array['tmp_name'] = $tmp;
			//		print_r( $file_array );
			//		die;
			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				@unlink( $file_array['tmp_name'] );
			} else {

			}

			//use media_handle_sideload to upload img:
			$thumbid = media_handle_sideload( $file_array, '', $desc );
			//			print_r($thumbid);
			// If error storing permanently, unlink
			if ( is_wp_error( $thumbid ) ) {
				@unlink( $file_array['tmp_name'] );
			} else {

			}

			return $thumbid;
		}
	}
}
//new  VillaTheme_Instagram();
//die;
