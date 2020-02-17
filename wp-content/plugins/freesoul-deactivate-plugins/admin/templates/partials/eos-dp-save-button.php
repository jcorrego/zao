<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//It displays the save button and related messages
function eos_dp_save_button(){
	$extra_class = '';
	$warning = '';
	$permalink_structure = get_option( 'permalink_structure' );
	$permalinks_label = __( 'the actual permalinks structure is not supported');
	if( '' === $permalink_structure ){
		$permalinks_label = __( 'the permalinks are set as plain','eos-dp' );
	}
	elseif( '/archives/%post_id%' === $permalink_structure ){
		$permalinks_label = __( 'the permalinks are set as numeric','eos-dp' );
	}
	if( '%postname%' !== basename( $permalink_structure ) ){
			$extra_class = ' eos-no-events';
			$warning = '<div style="background:#fff;color:#000;padding:10px;margin-bottom:10px;border-left:4px solid  #dc3232">'.sprintf( __( "You can't save because %s",'eos-dp' ),$permalinks_label );
			$warning .= '<p><a class="button" target="_blank" href="'.admin_url( 'options-permalink.php' ).'">'.__( 'Change Permalinks Structure','eos-dp' ).'</a></p>';
			$warning .= '</div>';
	}
	$dir = is_rtl() ? 'left' : 'right';
	$antiDir = is_rtl() ? 'right' : 'left';
	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
	?>
	<div class="eos-dp-btn-wrp" style="margin-top:40px">
		<?php echo $warning; ?>
		<input type="submit" name="submit" class="eos-dp-save-<?php echo esc_attr( $page );echo $extra_class; ?> button button-primary submit-dp-opts" data-backup="false" value="<?php _e( 'Save all changes','eos-dp' ); ?>"  />
		<?php eos_dp_ajax_loader_img(); ?>
		<div style="margin-<?php echo $dir; ?>:30px">
			<div class="eos-hidden eos-dp-opts-msg notice notice-success eos-dp-opts-msg_success msg_response" style="padding:10px;margin:10px;">
				<span><?php echo __( 'Options saved.','eos-dp' ); ?></span>
			</div>
			<div class="eos-dp-opts-msg_failed eos-dp-opts-msg notice notice-error eos-hidden msg_response" style="padding:10px;margin:10px;">
				<span><?php echo __( 'Something went wrong, maybe you need to refresh the page and try again, but you will lose all your changes','eos-dp' ); ?></span>
			</div>
			<div class="eos-dp-opts-msg_warning eos-dp-opts-msg notice notice-warning eos-hidden msg_response" style="padding:10px;margin:10px;">
				<span></span>
			</div>
		</div>
	</div>
	<div id="eos-dp-social" class="eos-dp-margin-top-48">
		<a title="The Facebook page where you can find news, tips and warnings about Freesoul Deactivate Plugins" href="https://www.facebook.com/Freesoul-Deactivate-Plugins-114102060129848/" target="_blanK" rel="noopener">
			<span class="dashicons dashicons-facebook-alt"></span>
		</a>
		<a title="The Freesoul Deactivate Plugins website" href="https://freesoul-deactivate-plugins.com/" target="_blanK" rel="noopener">
			<span class="dashicons dashicons-admin-site-alt"></span>
		</a>
		<a title="Support forum where you can ask for hekp" href="https://wordpress.org/support/plugin/freesoul-deactivate-plugins/" target="_blanK" rel="noopener">
			<span class="dashicons dashicons-editor-help"></span>
		</a>
		<a title="Documentation" href="https://freesoul-deactivate-plugins.com/documentation/" target="_blanK" rel="noopener">
			<span class="dashicons dashicons-book"></span>
		</a>
		<a title="We are preparing the PRO version, tell us which premium feature you would like to have." href="https://freesoul-deactivate-plugins.com/ideas-for-freesoul-deactivate-plugins-pro/" target="_blanK" rel="noopener">
			<span class="dashicons dashicons-format-chat"></span>
		</a>
	</div>
	<?php
	if( function_exists( 'get_user_locale' ) ){
		$locale = get_user_locale();
		if( $locale ){
			$locA = explode( '_',$locale );
			if( isset( $locA[0] ) && !in_array( $locA[0],array( 'en','it' ) ) ){
			?>
			<div id="eos-dp-translate" class="eos-dp-margin-top-48">
				<p><?php printf( __( 'Click %shere%s if you want to translate Freesoul Deactivate Plugins in your language.','eos-dp' ),'<a href="https://translate.wordpress.org/projects/wp-plugins/freesoul-deactivate-plugins/stable/'.esc_attr( $locA[0] ).'/default/" rel="noopener" target="_blank">','</a>' ); ?></p>
			</div>
			<?php
			}
		}
	}
}