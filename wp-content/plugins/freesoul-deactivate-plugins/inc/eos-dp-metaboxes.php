<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Adds a box to the main column on the Posts, Pages and Portfolios edit screens.
 */
function eos_dp_add_meta_box() {
	$post_types = get_post_types( array( 'publicly_queryable' => true,'public' => true ) );
	if( isset( $post_types['attachment'] ) ){
		unset( $post_types['attachment'] );
	}
	$screens = array_merge( array( 'page' ),$post_types );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'eos_dp_sectionid',
			__( 'Freesoul Deactivate Plugins', 'eos-dp' ),
			'eos_dp_meta_box_callback',
			$screen,
			'normal',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'eos_dp_add_meta_box' );
//Add metabox to deactivate external plugins on specific pages
function eos_dp_meta_box_callback( $post ){
	if( !current_user_can( 'activate_plugins' ) ) return;
	wp_nonce_field( 'eos_dp_meta_boxes', 'eos_dp_meta_boxes_nonce' );
	$post_types_plugins = eos_dp_get_option( 'eos_post_types_plugins' );
	$post_types_plugins = is_array( $post_types_plugins ) && !empty( $post_types_plugins ) ? $post_types_plugins : eos_dp_post_types_empty();	
	$active_plugins = eos_dp_active_plugins();
	$values_string = get_post_meta( $post->ID, '_eos_deactive_plugins_key',true );
	$extra_class = '';
	if( isset( $post->post_type ) ){
		if( isset( $post_types_plugins[$post->post_type] ) ){
			$ptp = $post_types_plugins[$post->post_type];
			$locked_ids = isset( $ptp[3] ) ? $ptp[3] : array();
			if( isset( $ptp[0] ) && $ptp[0] == '1' && !in_array( $post->ID,$locked_ids ) ){
				$extra_class = ' eos-hidden';
				$single_settings_url = admin_url( 'admin.php?page=eos_dp_menu' );
				$post_types_url = admin_url( 'admin.php?page=eos_dp_by_post_type' );
				?>
				<div class="eos-dp-warning">
					<h2><?php printf( __( '%s are overriding these Single Post Settings.','eos-dp' ),'<a style="text-decoration:none;color:#D3C4B8" href="'.$post_types_url.'">'.__( 'Post Type Settings','eos-dp' ).'</a>' ); ?></h2>
					<h2><?php printf( __( 'If you want to disable specific plugins for this specific page you have to lock the %sSingle Post Settings%s for this post.','eos-dp' ),'<a style="text-decoration:none;color:#D3C4B8" href="'.$single_settings_url.'">','</a>' ); ?></h2>
				</div>
				<?php
			}
			if( !$values_string && isset( $ptp[2] ) && $ptp[2] == '1' ){
				$values_string = $ptp[1];
			}
		}
	}
	$values = explode( ',',$values_string );
	?>
	<div id="eos-dp-plugins-wrp" class="eos-dp-plugins-wrp<?php echo $extra_class; ?>" style="line-height:2">
		<div class="eos-dp-separator-little"></div>
		<h2 style="display:inline-block;padding: 10px 0"><?php _e( 'Uncheck the plugins that you want to deactivate on this page.','eos-dp' ); ?></h2>
		<span style="display:inline-block;width:10px"></span>
		<span class="eos-dp-active-wrp"><input type="checkbox" /></span><span class="eos-dp-legend-txt"><?php _e( 'Plugin active','eos-dp' ); ?> </span>
		<span class="eos-dp-not-active-wrp"><input type="checkbox" checked/></span><span class="eos-dp-legend-txt"><?php _e( 'Plugin not active','eos-dp' ); ?></span>
		<input type="hidden" name="eos_dp_admin_meta[_eos_deactive_plugins_key]" id="eos_deactive_plugins" class="checkbox-result" value="<?php echo esc_attr( $values_string ); ?>"/>
		<div class="eos-dp-separator-little"></div>
		<?php
		foreach( $active_plugins as $p ){
			$plugin_name = strtoupper( str_replace( '-',' ',dirname( $p ) ) );
			?>
			<div class="eos-theme-checkbox-div" style="margin-bottom:4px">
				<span class="<?php echo in_array( $p,$values) ? 'eos-dp-not-active-wrp' : 'eos-dp-active-wrp'; ?>"><input class="eos-theme-checkbox" type="checkbox" value="<?php echo $p; ?>"<?php echo in_array( $p,$values) ? ' checked' : ''; ?> onclick="javascript:eos_dp_update_chk_wrp(jQuery(this),jQuery(this).is(':checked'));eos_dp_update_included_checks(this);"/></span>
				<span><?php echo esc_html( $plugin_name ); ?></span>
			</div>
			<?php	
		}
		?>
		<div class="eos-dp-separator"></div>
		<a class="button" href="<?php echo admin_url( 'admin.php?page=eos_dp_menu' ); ?>" target="_blank"><?php _e( 'Go to Freesoul Deactivate Plugins settings page','eos-dp' ); ?></a>
		<div class="eos-dp-separator-little"></div>
	</div>
	<script>
	function eos_dp_update_chk_wrp(chk,checked){
		if(true === checked){
			chk.parent().removeClass('eos-dp-active-wrp').addClass('eos-dp-not-active-wrp');
		}
		else{
			chk.parent().addClass('eos-dp-active-wrp').removeClass('eos-dp-not-active-wrp');
		}
	}
	function eos_dp_update_included_checks(el){
		var wrp = jQuery(el).closest('#eos-dp-plugins-wrp');
		var checks_imploder = wrp.find('.checkbox-result');
		var included_chk = [];
		var n = 0;
		wrp.find('.eos-theme-checkbox-div').each(function () {
			if (jQuery(this).find('input').prop('checked') === true) {
				included_chk[n] = jQuery(this).find('input').val();
				n = n + 1;
			}
		});
		checks_imploder.val(included_chk.sort().toString());
	}
	</script>
	<?php
}
/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved and object $post the post object.
 */
function eos_dp_save_meta_box_data( $post_id,$post ) {
	if ( ! isset( $_POST['eos_dp_admin_meta'] ) ) return;
	//* Merge user submitted options with fallback defaults
	$data = wp_parse_args( $_POST['eos_dp_admin_meta'], array( '_eos_deactive_plugins_key'  => '' ) );
	//* Sanitize
	foreach ( (array) $data as $key => $value ) {
		$data[$key] = sanitize_text_field( $value );
	}
	if( isset( $data['_eos_deactive_plugins_key'] ) ){
		$data['_eos_deactive_plugins_key'] .= ','.EOS_DP_PLUGIN_BASE_NAME;
	}
	eos_dp_save_metaboxes( $data, 'eos_dp_meta_boxes', 'eos_dp_meta_boxes_nonce', $post, 'activate_plugins' );
}
add_action( 'save_post', 'eos_dp_save_meta_box_data',10,2 );
/**
 *  @brief Save metaboxes
 *
 *  @param [in] $data Array containing the metaboxes field names and values
 *  @param [in] $nonce_action Nonce action
 *  @param [in] $nonce_name Nonce name
 *  @param [in] $post Post where we want to save the metaboxes values
 *  @param [in] $capability Required capability of the user that will save the meta data
 */
function eos_dp_save_metaboxes( array $data, $nonce_action, $nonce_name, $post, $capability = 'activate_plugins' ){
	//* Verify the nonce
	if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( $_POST[ $nonce_name ], $nonce_action ) )
		return;
	//* Don't try to save the data under autosave, ajax, or future post.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) return;
	$post = get_post( $post );
	$post_type = $post->post_type;
	//* Don't save if WP is creating a revision (same as DOING_AUTOSAVE?)
	if ( 'revision' === $post_type ) return;
	//* Check that the user is allowed to edit the post
	if ( ! current_user_can( $capability, $post->ID ) ) return;
	//* Cycle through $data, insert value or delete field
	foreach ( (array) $data as $field => $value ) {
		//* Save $value, or delete if the $value is empty
		if ( false !== $value ) update_post_meta( $post->ID, $field, $value );
	}
}