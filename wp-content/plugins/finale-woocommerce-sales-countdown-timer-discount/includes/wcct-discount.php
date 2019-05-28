<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class WCCT_discount
 * @package Finale-Lite
 * @author XlPlugins
 */
class WCCT_discount {

	public static $_instance = null;
	public $excluded = array();
	public $is_wc_calculating = false;

	public function __construct() {

		global $woocommerce;

		if ( version_compare( $woocommerce->version, 3.0, '>=' ) ) {
			//simple
			add_filter( 'woocommerce_product_get_price', array( $this, 'wcct_trigger_get_price' ), 999, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( $this, 'wcct_trigger_get_sale_price' ), 999, 2 );

			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'wcct_trigger_get_price' ), 999, 2 );
			add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'wcct_trigger_get_sale_price' ), 999, 2 );

			add_filter( 'woocommerce_product_get_date_on_sale_from', array( $this, 'wcct_date_on_sale_from' ), 999, 2 );
			add_filter( 'woocommerce_product_get_date_on_sale_to', array( $this, 'wcct_date_on_sale_to' ), 999, 2 );
		} else {
			add_filter( 'woocommerce_get_price', array( $this, 'wcct_trigger_get_price' ), 10, 2 );
			add_filter( 'woocommerce_get_sale_price', array( $this, 'wcct_trigger_get_sale_price' ), 999, 2 );
		}

		/**
		 * Additional filter applied to check if we need to display price or not.
		 */
		add_filter( 'woocommerce_get_variation_price', array( $this, 'wcct_handle_single_variation_price' ), 990, 4 );

		/**
		 * For variation products we need to handle case where we mark variable product as it is on sale
		 */
		add_filter( 'woocommerce_product_is_on_sale', array( $this, 'wcct_maybe_mark_product_having_sale' ), 999, 2 );

		/**
		 * modify price ranges for variable products
		 */
		add_filter( 'woocommerce_variation_prices', array( $this, 'wcct_change_price_ranges' ), 900, 3 );

		add_action( 'woocommerce_before_calculate_totals', array( $this, 'maybe_flag_running_calculations' ) );
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'maybe_unflag_running_calculations' ) );

		/**
		 * Need to modify the variation hash in order to let the woocommerce display the correct and modified variation
		 * Commented in 2.6.1 as causing transients creation every time on page load, so DB flood.
		 */
		add_filter( 'woocommerce_get_variation_prices_hash', array( $this, 'maybe_modify_variation_price_hash' ), 999, 3 );
	}

	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * @param $get_price
	 * @param $product_global
	 *
	 * @return bool|float|int|mixed|void
	 */
	public function wcct_trigger_get_price( $get_price, $product_global ) {
		if ( ! $product_global instanceof WC_Product ) {
			return $get_price;
		}

		$is_skip = apply_filters( 'wcct_skip_discounts', false, $get_price, $product_global );

		wcct_force_log( "Product id {$product_global->get_id()} : " . __FUNCTION__ . ' Before Price: ' . $get_price );

		if ( $get_price == '' ) {
			return $get_price;
		}

		if ( $is_skip === true ) {
			return $get_price;
		}

		if ( in_array( $product_global->get_type(), WCCT_Common::get_sale_compatible_league_product_types() ) ) {

			$get_price = $this->wcct_trigger_create_price( $get_price, $product_global );
		}
		wcct_force_log( "Product id {$product_global->get_id()} : " . __FUNCTION__ . ' After Price: ' . $get_price );

		return $get_price;
	}

	public function wcct_trigger_create_price( $sale_price, $product_global, $mode = 'basic', $regular_price = false, $parent_product = 0 ) {
		/**
		 * Here we are handling the case of all the hooks works while variable price range is getting generated
		 * We always pass $regular_price in that case
		 * so for the regular price we have, we do not need to generate any product variation object
		 * In that way we can easily by pass creating an object & the queries associated with that.
		 */

		if ( false === $regular_price ) {
			$type              = $product_global->get_type();
			$parentId          = WCCT_Core()->public->wcct_get_product_parent_id( $product_global );
			$product_global_id = $product_global->get_id();
		} else {
			$type              = 'variation';
			$product_global_id = $product_global;
			$parentId          = $parent_product;
		}

		if ( WCCT_Core()->public->wcct_restrict_for_booking_oth( $parentId, $type ) ) {
			return $sale_price;
		}

		$tempId = $parentId;
		$data   = WCCT_Core()->public->get_single_campaign_pro_data( $tempId );

		if ( empty( $data ) ) {
			wcct_force_log( ' terminating ' . __FUNCTION__ . ' For Product' . $tempId );

			return $sale_price;
		}

		if ( ! isset( $data['deals'] ) || ! is_array( $data['deals'] ) ) {
			return $sale_price;
		}

		WCCT_Core()->public->wcct_get_product_obj( $tempId );
		$deals          = $data['deals'];
		$deals_override = $deals;

		if ( $regular_price === false ) {
			do_action( 'wcct_before_get_regular_price', $product_global );

			$regular_price = $product_global->get_regular_price();

			do_action( 'wcct_after_get_regular_price', $product_global );
		}

		if ( empty( $regular_price ) ) {
			return $sale_price;
		}

		if ( ! is_array( $deals_override ) ) {
			return $sale_price;
		}

		if ( $deals_override['override'] == 1 ) {

			$check_sale = get_post_meta( $product_global_id, '_sale_price', true ); // we are fetching sale price from db using get_post_meta otherwise will stick in loop
			$check_sale = apply_filters( 'wcct_discount_check_sale_price', $check_sale, $product_global );

			if ( $check_sale >= '0' ) {
				$sale_price = (float) $sale_price;

				return $sale_price;
			}
		}

		$deal        = $deals_override;
		$deal_amount = (int) isset( $deal['deal_amount'] ) ? $deal['deal_amount'] : 0;
		$deal_amount = $deal_amount;

		if ( $deal_amount >= 0 ) {
			switch ( $deal['type'] ) {
				case 'percentage':
					$deal_amount = apply_filters( "wcct_deal_amount_percentage_{$type}", $deal_amount, $product_global, $data );

					if ( $mode == 'sale' && $deal_amount == '0' ) {
						return '';
					}
					$set_sale_price = $regular_price - ( $regular_price * ( $deal_amount / 100 ) );
					if ( $set_sale_price >= 0 ) {
						$sale_price = $set_sale_price;
					} else {
						$sale_price = 0;
					}
					break;
				case 'percentage_sale':
					$deal_amount = apply_filters( "wcct_deal_amount_percentage_{$type}", $deal_amount, $product_global, $data );
					if ( $mode == 'sale' && $deal_amount == '0' ) {
						return '';
					}
					if ( empty( $sale_price ) ) {
						$sale_price = $regular_price;
					}
					$set_sale_price = $sale_price - ( $sale_price * ( $deal_amount / 100 ) );
					if ( $set_sale_price >= 0 ) {
						$sale_price = $set_sale_price;
					} else {
						$sale_price = 0;
					}
					break;
				case 'fixed_sale':
					$deal_amount = apply_filters( "wcct_deal_amount_fixed_amount_{$type}", $deal_amount, $product_global, $data );
					if ( $mode == 'sale' && $deal_amount == '0' ) {
						return '';
					}
					if ( empty( $sale_price ) ) {
						$sale_price = $regular_price;
					}
					$set_sale_price = $sale_price - $deal_amount;
					if ( $set_sale_price >= 0 ) {
						$sale_price = $set_sale_price;
					} else {
						$sale_price = 0;
					}
					break;
				case 'fixed_price':
					$deal_amount = apply_filters( "wcct_deal_amount_fixed_amount_{$type}", $deal_amount, $product_global, $data );
					if ( $mode == 'sale' && $deal_amount == '0' ) {
						return '';
					}
					$set_sale_price = $regular_price - $deal_amount;
					if ( $set_sale_price >= 0 ) {
						$sale_price = $set_sale_price;
					} else {
						$sale_price = 0;
					}
					break;
			}
		} else {
			return $sale_price;
		}

		return $sale_price;
	}

	public function wcct_trigger_get_sale_price( $sale_price, $product_global ) {
		if ( ! $product_global instanceof WC_Product ) {
			return $sale_price;
		}

		$is_skip = apply_filters( 'wcct_skip_discounts', false, $sale_price, $product_global );

		if ( $is_skip === true ) {
			return $sale_price;
		}
		wcct_force_log( "Product id {$product_global->get_id()} : " . __FUNCTION__ . ' Before Price: ' . $sale_price );

		if ( in_array( $product_global->get_type(), WCCT_Common::get_sale_compatible_league_product_types() ) ) {
			$sale_price = $this->wcct_trigger_create_price( $sale_price, $product_global, 'sale' );
		}

		wcct_force_log( "Product id {$product_global->get_id()} : " . __FUNCTION__ . ' After Price: ' . $sale_price );

		return $sale_price;
	}

	public function wcct_trigger_create_sale_variation( $sale_price, $variation, $product_global ) {
		return $this->wcct_trigger_create_price( $sale_price, $variation, 'sale' );
	}


	public function wcct_date_on_sale_from( $sale_from_date, $product_global ) {

		$parentId = WCCT_Core()->public->wcct_get_product_parent_id( $product_global );

		if ( WCCT_Core()->public->wcct_restrict_for_booking_oth( $parentId ) ) {
			return $sale_from_date;
		}
		$tempId = $parentId;
		$data   = WCCT_Core()->public->get_single_campaign_pro_data( $parentId );

		if ( isset( $data['deals'] ) && is_array( $data['deals'] ) && count( $data['deals'] ) > 0 ) {
			$deals = $data['deals'];
			if ( isset( $deals['override'] ) && $deals['override'] == '1' && is_object( $sale_from_date ) ) {
				return $sale_from_date;
			}

			$sale_start_date = (int) $data['deals']['start_time'];
			//sale end condition custom check
			//            $sale_start_date = $sale_start_date - 60 * 60 * 24;
			$timezone = new DateTimeZone( WCCT_Common::wc_timezone_string() );
			if ( $sale_from_date instanceof WC_DateTime ) {
				$sale_from_date->setTimezone( $timezone );
				$sale_from_date->setTimestamp( $sale_start_date );
			} else {
				$sale_from_date = new WC_DateTime();
				$sale_from_date->setTimezone( $timezone );
				$sale_from_date->setTimestamp( $sale_start_date );
			}
		}

		return $sale_from_date;
	}

	//Events

	public function wcct_date_on_sale_to( $sale_from_to, $product_global ) {
		global $post;

		$parentId = WCCT_Core()->public->wcct_get_product_parent_id( $product_global );
		if ( WCCT_Core()->public->wcct_restrict_for_booking_oth( $parentId ) ) {
			return $sale_from_to;
		}
		$data = WCCT_Core()->public->get_single_campaign_pro_data( $parentId );

		if ( isset( $data['deals'] ) && is_array( $data['deals'] ) && count( $data['deals'] ) > 0 ) {
			$deals = $data['deals'];
			if ( isset( $deals['override'] ) && $deals['override'] == '1' && is_object( $sale_from_to ) ) {
				return $sale_from_to;
			}

			$sale_end_date = (int) $data['deals']['end_time'];
			$timezone      = new DateTimeZone( WCCT_Common::wc_timezone_string() );
			if ( $sale_from_to instanceof WC_DateTime ) {
				$sale_from_to->setTimezone( $timezone );
				$sale_from_to->setTimestamp( $sale_end_date );
			} else {
				$sale_from_to = new WC_DateTime();
				$sale_from_to->setTimezone( $timezone );
				$sale_from_to->setTimestamp( $sale_end_date );

			}
		}

		return $sale_from_to;
	}

	/**
	 * @hooked over `woocommerce_get_variation_prices_hash`
	 * Added current time as unique key so that the variation prices comes to display with the discounts added by finale but not by the object caching (by WordPress)
	 *
	 * @param array $price_hash
	 * @param WC_Product $productThis
	 * @param boolean $display
	 *
	 * @return array
	 */
	public function maybe_modify_variation_price_hash( $price_hash, $productThis, $display ) {

		if ( false === $display ) {
			return $price_hash;
		}

		if ( ! $productThis instanceof WC_Product ) {
			return $price_hash;
		}

		$campaign = WCCT_Core()->public->get_single_campaign_pro_data( $productThis->get_id() );
		if ( empty( $campaign ) || ! isset( $campaign['deals'] ) || empty( $campaign['deals'] ) ) {
			return $price_hash;
		}

		$deals = $campaign['deals'];
		unset( $campaign['deals']['start_time'] );
		unset( $campaign['deals']['end_time'] );
		$hash = md5( serialize( $campaign['deals'] ) );

		if ( is_array( $price_hash ) ) {
			$price_hash[] = $hash;
		} elseif ( empty( $price_hash ) ) {
			$price_hash = array( $hash );
		} else {
			$price_hash = array( $price_hash, $hash );
		}

		return $price_hash;
	}

	public function wcct_change_price_ranges( $priceRanges, $productThis, $display ) {

		if ( $display === false ) {
			return $priceRanges;
		}

		$prices         = array();
		$regular_prices = array();
		$sale_prices    = array();
		if ( is_array( $priceRanges ) && count( $priceRanges ) > 0 ) {

			foreach ( $priceRanges['regular_price'] as $key => $val ) {

				$regular_prices[ $key ] = $val;

			}

			$prices = $this->wcct_set_variation_price( $priceRanges['price'], 'basic', $regular_prices, $productThis->get_id() );

			$sale_prices = $this->wcct_set_variation_price( $priceRanges['price'], 'basic', $regular_prices, $productThis->get_id() );

		}
		asort( $prices );
		asort( $regular_prices );
		asort( $sale_prices );

		//
		$priceRanges = array(
			'price'         => $prices,
			'regular_price' => $regular_prices,
			'sale_price'    => $sale_prices,
		);

		return $priceRanges;
	}

	public function wcct_set_variation_price( $input, $type = 'basic', $regular_price = false, $parent_Product = 0 ) {
		if ( is_array( $input ) ) {

			foreach ( $input as $k => $price ) {

				$is_skip = apply_filters( 'wcct_skip_discounts', false, $price, $k );

				if ( $is_skip === true ) {
					$input[ $k ] = $price;
					continue;
				}
				//formatting the prices as per WC is doing so that further comparison can take place between reg price and sale price to detect is on sale

				$input[ $k ] = $this->wcct_trigger_create_price( $price, $k, $type, ( $regular_price !== false ) ? $regular_price[ $k ] : false, $parent_Product );
			}
		}

		return $input;
	}


	public function wcct_maybe_mark_product_having_sale( $bool, $product ) {
		if ( ! $product instanceof WC_Product ) {
			return $bool;
		}
		if ( in_array( $product->get_type(), WCCT_Common::get_variable_league_product_types() ) ) {

			$priceRanges    = $product->get_variation_prices();
			$prices         = array();
			$regular_prices = array();
			$sale_prices    = array();
			if ( is_array( $priceRanges ) && count( $priceRanges ) > 0 ) {

				foreach ( $priceRanges['regular_price'] as $key => $val ) {

					$regular_prices[ $key ] = $val;

				}

				$prices = $this->wcct_set_variation_price( $priceRanges['price'], 'basic', $regular_prices, $product->get_id() );

				$sale_prices = $this->wcct_set_variation_price( $priceRanges['price'], 'basic', $regular_prices, $product->get_id() );
			}
			asort( $prices );
			asort( $regular_prices );
			asort( $sale_prices );

			$priceRanges = array(
				'price'         => $prices,
				'regular_price' => $regular_prices,
				'sale_price'    => $sale_prices,
			);

			if ( is_array( $priceRanges['regular_price'] ) && ! empty( $priceRanges['regular_price'] ) ) {
				$bool = false;
				foreach ( $priceRanges['regular_price'] as $id => $price ) {
					if ( $priceRanges['sale_price'][ $id ] != $price && $priceRanges['sale_price'][ $id ] == $priceRanges['price'][ $id ] ) {
						$bool = true;
					}
				}
			}

			return $bool;
		}

		return $bool;
	}


	public function wcct_handle_single_variation_price( $price, $product, $min_or_max, $include_taxes ) {

		if ( ! $product instanceof WC_Product ) {
			return $price;
		}
		$priceRanges    = $product->get_variation_prices();
		$prices         = array();
		$regular_prices = array();
		$sale_prices    = array();
		if ( is_array( $priceRanges ) && count( $priceRanges ) > 0 ) {

			foreach ( $priceRanges['regular_price'] as $key => $val ) {

				$regular_prices[ $key ] = $val;
			}

			$prices = $this->wcct_set_variation_price( $priceRanges['price'], 'basic', $regular_prices, $product->get_id() );

			$sale_prices = $this->wcct_set_variation_price( $priceRanges['price'], 'basic', $regular_prices, $product->get_id() );
		}
		asort( $prices );
		asort( $regular_prices );
		asort( $sale_prices );

		$priceRanges = array(
			'price'         => $prices,
			'regular_price' => $regular_prices,
			'sale_price'    => $sale_prices,
		);

		$price = 'min' === $min_or_max ? current( $priceRanges['sale_price'] ) : end( $priceRanges['sale_price'] );

		return $price;

	}

	public function maybe_flag_running_calculations() {

		$this->is_wc_calculating = true;
	}

	public function maybe_unflag_running_calculations() {
		$this->is_wc_calculating = false;
	}


}

if ( class_exists( 'WCCT_discount' ) ) {
	WCCT_Core::register( 'discount', 'WCCT_discount' );
}
