<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//It displays the help navigation
function eos_dp_help_navigation(){
	?>
	<div id="eos-dp-help-nav-wrp" class="eos-dp-margin-top-48">
		<ul id="eos-dp-help-nav">
			<li data-section="eos-dp-flowchart" class="hover eos-dp-help-menu-item"><a class="button<?php echo $_GET['tab'] === 'flowchart' ? ' eos-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=eos_dp_help&tab=flowchart' ); ?>"><?php _e( 'Flowchart','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-docu" class="hover eos-dp-help-menu-item"><a class="button" target="_blank" rel="noopener" href="https://freesoul-deactivate-plugins.com/documentation/"><?php _e( 'Documentation','eos-dp' ); ?></a></li>
			<li data-section="eos-dp-forum" class="hover eos-dp-help-menu-item"><a class="button" target="_blank" rel="noopener" href="https://wordpress.org/support/plugin/freesoul-deactivate-plugins/"><?php _e( 'Support Forum','eos-dp' ); ?></a></li>
			<?php do_action( 'eos_dp_helps_tabs' ); ?>
			<li style="position:relative;float:<?php echo is_rtl() ? 'left' : 'right'; ?>">
				<input style="padding:0 10px;min-height:0;line-height:25px;border-top-style:none;border-left-style:none;border-right-style:none" type="text" id="eos-dp-search-on-support-input" placeholder="<?php _e( 'Search on forum','eos-dp' ); ?>" />
				<a style="position:absolute;<?php echo is_rtl() ? 'left' : 'right'; ?>:6px;text-decoration:none;top:0" id="eos-dp-search-on-support-button" href="https://wordpress.org/search/freesoul+deactivate+plugins" target="_blank" rel="noopener"><span class="dashicons dashicons-search"></span></a>
			</li>			
		</ul>
	</div>
	<?php do_action( 'eos_dp_after_help_nav' );
}