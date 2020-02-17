<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="eos-dp-def-over" class="eos-dp-legend-row-inside" style="margin-bottom:32px">
	<div>
		<div id="eos-dp-priority-legend" style="pointer-events:none;margin:20px 0">
			<p>
				<span>
					<span class="eos-dp-priority-legend-wrp eos-dp-priority-active">
						<input title="<?php _e( 'Overrides unlocked Single Post Settings','eos-dp' ); ?>" class="eos-dp-priority-post-type" type="checkbox" />
					</span>
					<span><?php _e( 'Overrides unlocked Single Post Settings','eos-dp' ); ?></span>
				</span>
				<span style="display:inline-block;width:20px"></span>
				<span>
					<span class="eos-dp-priority-legend-wrp">
						<input title="<?php _e( 'Does NOT Overrides Single Post Settings','eos-dp' ); ?>" class="eos-dp-priority-post-type" type="checkbox" />
					</span>
					<span><?php _e( 'Does NOT Overrides Single Post Settings','eos-dp' ); ?></span>
				</span>
			</p>
			<p>	
				<span>
					<span class="eos-dp-default-legend-wrp eos-dp-default-active">
						<span class="eos-dp-default-active eos-dp-default-post-type-wrp">
							<span class="eos-dp-default-chk-wrp">
								<input checked title="<?php _e( 'Set as default on new posts.','eos-dp' ); ?>" class="eos-dp-default-post-type-checked eos-dp-default-post-type" type="checkbox" />
								<span></span>
							</span>
					</span>
					<span><?php _e( 'Sets Default on NEW Single Posts.','eos-dp' ); ?></span>
				</span>
				<span style="display:inline-block;width:20px"></span>
				<span>
					<span class="eos-dp-default-legend-wrp">
						<span class="eos-dp-default-active eos-dp-default-post-type-wrp">
							<span class="eos-dp-default-chk-wrp">
								<input title="<?php _e( 'Do not set as default on new posts.','eos-dp' ); ?>" class="eos-dp-default-post-type" type="checkbox" />
								<span></span>
							</span>
						</span>

					</span>
					<span><?php _e( 'Does NOT set Default on NEW Single Posts (all plugins will be active as default).','eos-dp' ); ?></span>
				</span>
			</p>
		</div>
	</div>
</div>