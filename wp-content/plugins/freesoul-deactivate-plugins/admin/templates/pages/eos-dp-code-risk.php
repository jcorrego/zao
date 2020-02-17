<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Callback for deactivate by archive settings page
function eos_dp_code_risk_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}
	eos_dp_alert_plain_permalink();
	eos_dp_navigation();
	$active_plugins = eos_dp_active_plugins();
	$plugins = eos_dp_get_plugins();
	$plugins_data = array();
	?>
	<div id="eos-dp-plugin-info">
		<h1><?php _e( 'Smoke Tests and Code Risk results for the last plugin versions','eos-dp' ); ?></h1>
		<div class="eos-dp-tests-actions-wrp eos-dp-margin-top-48">
			<span id="eos-dp-collapse-all" class="button"><?php _e( 'Collapse all','eos-dp' ); ?><span class="dashicons dashicons-editor-contract"></span></span>
			<span id="eos-dp-expand-all" class="button"><?php _e( 'Expand all','eos-dp' ); ?><span class="dashicons dashicons-editor-expand"></span></span>
			<span id="eos-dp-show-comparison" class="button"><?php _e( 'Go to comparison','eos-dp' ); ?><span class="dashicons dashicons-arrow-down-alt"></span></span>
		</div>
		<?php
		foreach( $active_plugins as $plugin_path ){
			if( isset( $plugins[$plugin_path] ) ){
				$summaryHTML = '';
				$plugin_slug = sanitize_key( dirname( $plugin_path ) );
				$plugin_name = strtoupper( str_replace( '-',' ',$plugin_slug ) );
				if( !$summaryHTML || '' === $summaryHTML ){
					$tests_url = 'https://plugintests.com/plugins/'.$plugin_slug.'/latest';
					$response = wp_remote_get( esc_url( $tests_url ) );
					if( is_wp_error( $response ) || !isset( $response['headers'] ) ) continue;
					$body = wp_remote_retrieve_body( $response );
					if( !isset( $response['response'] ) ) continue;
					$response = $response['response'];
					if( !isset( $response['code'] ) ) continue;
					if( 200 === $response['code'] ){
						if( class_exists( 'DOMDocument' ) && class_exists( 'DomXPath' ) ){
							libxml_use_internal_errors( true );
							$dom = new DOMDocument();
							$dom->loadHTML( $body );
							if( null === $dom ) continue;
							$finder = new DomXPath( $dom );
							foreach( array( 
								'breadcrumb',
								'col-md-3',
								'col-md-4',
								'modal-body',
								'navbar-header',
								'modal-header',
								'modal-footer',
								'navbar-collapse',
								'well'
							) as $classname ){
								$nodes = $finder->query("//*[contains(@class, '$classname')]");				
								foreach( $nodes as $node ){
									$node->parentNode->removeChild( $node );
								}
							}
							$classname = 'container';
							$panels = $finder->query("//*[contains(@class, '$classname')]");				
							foreach( $panels as $panel ){
								$summaryHTML .= eos_dp_get_inner_html( $panel );
							}
							$rows = $finder->query("//*[@id='benchmark-details']//tr");
							$rowsN = $rows->length;
							$n = 1;
							$data = array();
							foreach( $rows as $row ){
								if( $n === $rowsN - 1 ){
									$cols = $row->getElementsByTagName( 'td' );
									$k = 0;
									foreach( $cols as $col ){
										if( $k > 0 ){
											$data[] = $col->nodeValue;
										}
										++$k;
									}
									$plugins_data[$plugin_slug] = $data;
								}
								++$n;
							}						
						}	
					}
				}
			?>
		<div class="postbox-container">
			<div class="eos-dp-plugin-info-section eos-dp-margin-top-48 postbox close">
				<span class="eos-dp-open-div eos-dp-toggle-div dashicons dashicons-arrow-down"></span>
				<span class="eos-dp-open-close eos-dp-toggle-div dashicons dashicons-arrow-up"></span>				
				<h2><?php echo esc_html( $plugin_name ); ?></h2>
				<div>
					<?php 
					if( 200 === $response['code'] ){ ?>
						<a target="_blank" rel="noopener" href="https://coderisk.com/wp/plugin/<?php echo esc_attr( $plugin_slug ); ?>"><img src="https://coderisk.com/wp/plugin/<?php echo esc_attr( $plugin_slug ); ?>/badge"></a>
						<a target="_blank" rel="noopener" href="https://plugintests.com/plugins/<?php echo esc_attr( $plugin_slug ); ?>/latest"><img src="https://plugintests.com/plugins/<?php echo esc_attr( $plugin_slug ); ?>/wp-badge.svg" /></a>
					<?php 
					}
					else{
						?>
						<span><?php _e( 'Not found','eos-dp' ); ?></span>
						<?php
					}
					?>
				</div>
				<div class="eos-dp-plugin-test-summary eos-dp-margin-top-48">
				<?php
				if( $summaryHTML && '' !== $summaryHTML ){
					?>
					<?php echo eos_dp_plugininfo_kses( $summaryHTML ); ?>
					<span class="eos-dp-open-close eos-dp-toggle-div dashicons dashicons-arrow-up" style="margin-top:-10px"></span>
				<?php 
				}else{
					?>
					<div>
						<p><?php printf( __( 'Tests results of %s not found.','eos-dp' ),esc_html( $plugin_name ) ); ?></p>
						<p><?php printf( __( 'Maybe %s is a premium plugin, these tests are not available for premium plugins.','eos-dp' ),esc_html( $plugin_name ) ); ?></p>
					</div>
					<?php
				}
				?>
				</div>
			</div>
		</div>
			<?php 
			}
		}
		if( !empty( $plugins_data ) ){
		?>
		<div id="eos-dp-plugins-comparison" style="padding-top:48px;clear:both">
			<h1><?php _e( 'Comparison','eos-dp' ); ?></h1>
			<h2><?php _e( 'Loading Time, Memory Usage, Code Risk, Errors','eos-dp' ); ?></h2>
			<table class="table table-striped" id="benchmark-details-comparison">
				<thead>
					<tr>
						<th rowspan="2"><?php _e( 'Plugin Name','eos-dp' ); ?></th>
						<th colspan="3"><?php _e( 'Load time','eos-dp' ); ?></th>
						<th colspan="3"><?php _e( 'Memory usage','eos-dp' ); ?></th>
						<th rowspan="2"><?php _e( 'Code Risk','eos-dp' ); ?></th>
						<th rowspan="2"><?php _e( 'Errors','eos-dp' ); ?></th>
					</tr>
					<tr>
						<th><?php _e( 'Inactive','eos-dp' ); ?></th>
						<th><?php _e( 'Active','eos-dp' ); ?></th>
						<th><?php _e( 'Change','eos-dp' ); ?></th>
						<th><?php _e( 'Inactive','eos-dp' ); ?></th>
						<th><?php _e( 'Active','eos-dp' ); ?></th>
						<th><?php _e( 'Change','eos-dp' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach( $plugins_data as $plugin_slug => $arr ){
					?>
					<tr>
						<td><?php echo esc_html( strtoupper( str_replace( '-',' ',$plugin_slug ) ) ); ?></td>
						<?php
						$k = 0;
						foreach( $arr as $value ){
							?>
							<td><?php echo esc_html( $value ); ?></td>
							<?php							
							++$k;
						}
						?>
						<td>
							<div>
								<a target="_blank" rel="noopener" href="https://coderisk.com/wp/plugin/<?php echo esc_attr( $plugin_slug ); ?>"><img src="https://coderisk.com/wp/plugin/<?php echo esc_attr( $plugin_slug ); ?>/badge"></a>
							</div>
						</td>
						<td>
							<div>
								<a target="_blank" rel="noopener" href="https://plugintests.com/plugins/<?php echo esc_attr( $plugin_slug ); ?>/latest"><img src="https://plugintests.com/plugins/<?php echo esc_attr( $plugin_slug ); ?>/wp-badge.svg"></a>
								<a target="_blank" rel="noopener" href="https://plugintests.com/plugins/<?php echo esc_attr( $plugin_slug ); ?>/latest"><img src="https://plugintests.com/plugins/<?php echo esc_attr( $plugin_slug ); ?>/php-badge.svg"></a>
							</div>
						</td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
		}
		?>
		<p><?php printf( __(  'Tests performed by %s%s%s','eos-dp' ),'<a href="https://plugintests.com/" target="_blank" rel="noopener" >','https://plugintests.com/','</a>' ); ?></p>
		<p><?php printf( __(  'Code risk check performed by %s%s%s','eos-dp' ),'<a href="https://coderisk.com/" target="_blank" rel="noopener" >','https://coderisk.com/','</a>' ); ?></p>
		<div id="eos-dp-go-to-top" class="hover right" style="margin:48px 6px 0 6px;z-index:999"><span title="<?php _e( 'Go to top','eos-dp' ); ?>" style="background:#fff;padding:10px" class="dashicons dashicons-arrow-up-alt"></span></div>
	</div>
	<?php
}

//Return node inner HTML
function eos_dp_get_inner_html( $node ) {
    $innerHTML= '';
    $children = $node->childNodes;
    foreach( $children as $child ){
        $innerHTML .= $child->ownerDocument->saveXML( $child );
    }
    return $innerHTML;
} 

//Return allowed HTML tags for plugin info
function eos_dp_plugininfo_kses( $html ){
	return wp_kses( str_replace( 'Get badge code','',$html ),array(
		'span' => array( 'class' => array( 'glyphicon','glyphicon-ok','language-json','hljs' ) ),
		'strong' => array(),
		'div' => array( 'class' => array( 'panel-heading' ) ),
		'code' => array(),
		'pre' => array(),
		'p' => array(),
		'small' => array(),
		'ul' => array(),
		'li' => array(),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'table' => array(),
		'thead' => array(),
		'td' => array(),
		'tr' => array(),
		'th' => array( 'colspan' => array(),'rowspan' => array() ),
		'img' => array( 'src' => array() ),
	) );
}