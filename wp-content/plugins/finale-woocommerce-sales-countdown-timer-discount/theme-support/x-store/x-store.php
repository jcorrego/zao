<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp', 'wcct_theme_helper_x_store', 100 );

function wcct_theme_helper_x_store() {
	$wcct_appearance_instance = WCCT_Core()->appearance;
	remove_action( 'woocommerce_single_product_summary', array( $wcct_appearance_instance, 'wcct_position_below_price' ), 17.3 );
	remove_action( 'woocommerce_single_product_summary', array( $wcct_appearance_instance, 'wcct_position_above_title' ), 2.3 );
	remove_action( 'woocommerce_single_product_summary', array( $wcct_appearance_instance, 'wcct_position_below_title' ), 9.3 );

	add_action( 'woocommerce_single_product_summary', array( $wcct_appearance_instance, 'wcct_position_below_price' ), 25.1 );

	add_filter( 'woocommerce_show_page_title', function ( $bool ) {
		$wcct_appearance_instance = WCCT_Core()->appearance;
		$wcct_appearance_instance->wcct_position_above_title();
		?>
        <h1 class="title">
			<?php if ( ! etheme_get_option( 'product_name_signle' ) && is_single() && ! is_attachment() ) : ?>
				<?php echo WCCT_Common::get_the_title(); ?>
			<?php elseif ( ! is_single() ) : ?>
				<?php woocommerce_page_title(); ?>
			<?php endif; ?>
        </h1>
		<?php
		$wcct_appearance_instance->wcct_position_below_title();
	}, 999 );

	// shop loop
	remove_action( 'woocommerce_after_shop_loop_item', array( $wcct_appearance_instance, 'wcct_bar_timer_show_on_grid' ), 9 );

	// hooking after shop loop function
	add_action( 'woocommerce_after_shop_loop_item_title', array( $wcct_appearance_instance, 'wcct_bar_timer_show_on_grid' ), 20 );
}
