<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'EOS_DP_MU_VERSION','1.7.1' );
if( 
	!defined( 'DOING_CRON' ) 
	&& ( !isset( $_REQUEST['s'] ) || ( defined( 'EOS_DP_URL_APPLY_ON_SEARCH' ) && true === EOS_DP_URL_APPLY_ON_SEARCH ) ) 
	&& '%postname%' === basename( eos_dp_get_option( 'permalink_structure' ) ) 
	|| isset( $_GET['eos_dp_preview'] ) 
){
	$eos_dp_disabled_plugins = array();
	global $eos_dp_disabled_plugins;
	$post_types_matrix = get_site_option( 'eos_post_types_plugins' );
	$post_types = is_array( $post_types_matrix ) ? array_keys( $post_types_matrix ) : array();
	if( !is_admin() && empty( $_POST ) && is_array( $post_types ) ){
		$home_page = false;
		$clean_uri = '';
		$arr = array();
		$uri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$from_url = false;
		$urlsA = eos_dp_get_option( 'eos_dp_by_url' );
		$from_url = false;
		if( !isset( $_GET['eos_dp_preview'] ) ){
			if( is_array( $urlsA ) && !empty( is_array( $urlsA ) ) ){
				foreach( $urlsA as $urlA ){
					if( isset( $urlA['url'] ) ){
						foreach( array( 'https://','http://','www.' ) as $search ){
							$urlA['url'] = str_replace( $search,'',$urlA['url'] );
						}
						$urlA2 = array_filter( explode( '*',str_replace( '**','*',$urlA['url'].'*' ) ) );
						$urlA2Count = count( $urlA2 );
						$protocol = is_ssl() ? 'https://' : 'http://';
						$uriA = array_fill( 0,$urlA2Count,$protocol.$uri );
						$urlA3 = array_filter( array_map( 'eos_dp_array_url_fragments_filter',$urlA2,$uriA ) );
						if( !empty( $urlA3 ) && count( $urlA3 ) === $urlA2Count ){
							$eos_dp_paths = explode( ',',$urlA['plugins'] );
							$from_url = true;
							break;
						}
					}
				}
			}
		}
		if( !$from_url ){
			$uriArr = explode( '?',$uri );
			$uri = $clean_uri = $uriArr[0];
			if( !isset( $_GET['page_id'] ) && !isset( $_GET['p'] ) ){
				$home_uri = str_replace( 'https://','',str_replace( 'http://','',home_url( '/' ) ) );		
				if( $uri !== $home_uri ){
					$arr = array_filter( explode( '/',$uri ) );
					$after_home_uri = str_replace( $home_uri,'',implode( '/',$arr ) );
					$after_home_uriArr = explode( '?',$after_home_uri );
					$after_home_uri = $after_home_uriArr[0];
					$after_home_uriArr = explode( '#',$after_home_uri );
					$after_home_uri = untrailingslashit( $after_home_uriArr[0] );
					$p = false;
					$p = $after_home_uri !== '' ? get_page_by_path( $after_home_uri,'OBJECT',$post_types ) : false;
					$p = $after_home_uri !== '' && !is_object( $p ) ? get_page_by_path( basename( $after_home_uri ),'OBJECT',$post_types ) : $p;
					$eos_page_id = is_object( $p ) && ( false === strpos( $after_home_uri,'/' ) || ( $p->post_parent || 'page' !== $p->post_type ) ) ? $p->ID : false;
				}
				else{
					$eos_page_id = eos_dp_get_option( 'page_on_front' );
					$p = get_page( $eos_page_id );
					$home_page = true;
				}
			}
			else{
				$eos_page_id = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : absint( $_GET['p'] );
				$p = get_page( $eos_page_id );
				global $eos_page_id;
			}
			$eos_page_id = absint( $eos_page_id ) !== 0 ? $eos_page_id : false;
			if( eos_dp_is_mobile() ){
				$mobile_page_id = absint( get_post_meta( $eos_page_id,'eos_scfm_mobile_post_id',true ) );
				if( $mobile_page_id > 0 ){
					$eos_page_id = $mobile_page_id;
				}
			}		
			$eos_dp_paths = '';
			if( $eos_page_id || ( isset( $_GET['fdp_post_id'] ) && isset( $_GET['eos_dp_preview'] ) ) ){
				if( isset( $_GET['fdp_post_id'] ) && isset( $_GET['eos_dp_preview'] ) ){
					$eos_page_id = absint( $_GET['fdp_post_id'] );
					$eos_dp_paths = explode( ';pn:',esc_attr( get_transient( 'fdp_test_'.sanitize_key( $_GET['fdp_post_id'] ) ) ) );
				}
				else{
					if( $post_types_matrix ){
						$post_types_matrix_pt = isset( $post_types_matrix[$p->post_type] ) ? $post_types_matrix[$p->post_type] : 0;
					}
					$post_meta = get_post_meta( $eos_page_id,'_eos_deactive_plugins_key',true );
					if( isset( $post_types_matrix_pt ) && '0' == $post_types_matrix_pt[0] ){
						$eos_dp_paths = explode( ',',$post_meta );
					}
					else{
						if( is_object( $p ) && $post_types_matrix && isset( $post_types_matrix[$p->post_type] ) ){
							if( isset( $post_types_matrix_pt[3] ) ){
								$ids = $post_types_matrix_pt[3];
								if( in_array( $eos_page_id,$ids ) ){
									$eos_dp_paths = explode( ',',$post_meta );
								}
								else{
									$eos_dp_paths = explode( ',',$post_types_matrix_pt[1] );
								}							
							}
							else{
								$eos_dp_paths = explode( ',',$post_types_matrix_pt[1] );
							}
						}
					}
				}	
				global $eos_page_id;
			}
			else{
				//It's an archive page
				$archives = eos_dp_get_option( 'eos_dp_archives' );
				$clean_uri = str_replace( '/','__',rtrim( $clean_uri,'/' ) );
				$key = sanitize_key( $clean_uri );
				if( isset( $_GET['fdp_post_type'] ) && isset( $_GET['eos_dp_preview'] ) ){
					$eos_dp_paths = explode( ';pn:',esc_attr( get_transient( 'fdp_test_'.sanitize_key( $_GET['fdp_post_type'] ) ) ) );
				}
				elseif( isset( $_GET['fdp_tax'] ) && isset( $_GET['eos_dp_preview'] ) ){
					$eos_dp_paths = explode( ';pn:',esc_attr( get_transient( 'fdp_test_'.sanitize_key( $_GET['fdp_tax'] ) ) ) );
				}		
				elseif( isset( $archives[$key] ) ){
					$eos_dp_paths = explode( ',',$archives[$key] );
				}
			}
		}	
		global $eos_dp_paths;
		if( !defined( 'EOS_DEACTIVE_PLUGINS' ) ) define( 'EOS_DEACTIVE_PLUGINS',true );
		add_filter( 'option_active_plugins', 'eos_option_active_plugins',0,1 );
	}
}


if( is_admin() 
	&& ( !isset( $_GET['page'] ) || 'eos_dp_admin' !== $_GET['page'] )
	&& isset( $_SERVER['HTTP_HOST'] )
	&& isset( $_SERVER['REQUEST_URI'] )
	&& empty( $_POST )
){
	add_action( 'wp_loaded',function(){
		$GLOBALS['eos_dp_wp_loaded'] = round( microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],2 );
	} );	
	add_filter( 'option_active_plugins','eos_dp_admin_option_active_plugins',0,1 );
	$adminTheme = eos_dp_get_option( 'eos_dp_admin_theme' );
	$adminThemeUrl = eos_dp_get_option( 'eos_dp_admin_url_theme' );
	$base_url = basename( $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
	if( isset( $adminTheme[$base_url] ) && !$adminTheme[$base_url] ){
		add_action( 'plugins_loaded','eos_dp_replace_theme',99 );
	}
}
function eos_dp_admin_option_active_plugins( $plugins ){
	$all_plugins = $plugins;
	foreach( $plugins as $p => $const ){
		$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
		if( !defined( 'EOS_ADMIN_'.$const.'_ACTIVE' ) ) define( 'EOS_ADMIN_'.$const.'_ACTIVE','true' );
	}
	$base_url = basename( $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] );
	$admin_page = false !== strpos( $base_url,'.php' ) ?  admin_url( $base_url ) : admin_url( 'admin.php'.$base_url );
	$from_admin_url = false;
	$urlsA = eos_dp_get_option( 'eos_dp_by_admin_url' );	
	$admin_plugins = array();
	if( is_array( $urlsA ) && !empty( is_array( $urlsA ) ) ){
		foreach( $urlsA as $urlA ){
			if( isset( $urlA['url'] ) ){
				foreach( array( 'https://','http://','www.' ) as $search ){
					$urlA['url'] = str_replace( $search,'',$urlA['url'] );
				}				
				$urlA2 = array_filter( explode( '*',str_replace( '**','*',$urlA['url'].'*' ) ) );
				$urlA2Count = count( $urlA2 );
				$uriA = array_fill( 0,$urlA2Count,$admin_page );
				$urlA3 = array_filter( array_map( 'eos_dp_array_url_fragments_filter',$urlA2,$uriA ) );
				if( !empty( $urlA3 ) && count( $urlA3 ) === $urlA2Count ){
					$admin_plugins[$admin_page] = $urlA['plugins'];
					$from_admin_url = true;
					break;
				}
			}
		}
	}
	if( !$from_admin_url ){
		$admin_plugins = eos_dp_get_option( 'eos_dp_admin_setts' );
	}
	if( isset( $admin_plugins[$admin_page] ) || isset( $admin_plugins[$base_url] ) ){
		$key = isset( $admin_plugins[$admin_page] ) ? $admin_plugins[$admin_page] : $admin_plugins[$base_url];
		$disabled_plugins = explode( ',',$key );
		foreach( $disabled_plugins as $path ){
			$k = array_search( $path, $plugins );
			if( false !== $k ){
				$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
				if( !defined( 'EOS_'.$const.'_ACTIVE' ) ) define( 'EOS_'.$const.'_ACTIVE','true' );
				unset( $plugins[$k] );
			}
		}	
	}
	$GLOBALS['eos_dp_paths'] = array_diff( $all_plugins,$plugins );
	add_action( 'admin_footer','eos_dp_print_disabled_plugins',9999 );
	register_shutdown_function( 'eos_dp_console_usage' );
	if( isset( $_GET['backend_usage'] ) && 'true' === $_GET['backend_usage'] ){
		register_shutdown_function( 'eos_dp_display_usage' );
	}
	return $plugins;
}



//Return active plugins in according with the options
function eos_option_active_plugins( $plugins ){	
	if( is_admin() || defined( 'DOING_AJAX' ) || class_exists( 'FS_Plugin_Updater' ) ) return $plugins;
	if( isset( $_GET['eos_dp_preview'] ) ){
		if( isset( $_REQUEST['eos_dp_debug'] ) ){
			if( 'no_errors' === $_REQUEST['eos_dp_debug'] ){
				ini_set( 'display_errors','Off' );
			}
			elseif( 'display_errors' === $_REQUEST['eos_dp_debug'] ){
				ini_set( 'display_errors','On' ); 	
			}
		}		
		add_action( 'plugins_loaded','eos_check_dp_preview_nonce' );
		add_action( 'wp_loaded',function(){
			$GLOBALS['eos_dp_wp_loaded'] = round( microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],2 );
		} );
		register_shutdown_function( 'eos_dp_display_usage' );
		if( isset( $_GET['theme'] ) && 'false' !== $_GET['theme'] ){
			add_filter( 'stylesheet','eos_dp_get_theme' );
			add_filter( 'template','eos_dp_get_parent_theme' );			
		}
		if( isset( $_GET['js'] ) && 'off' === $_GET['js'] ){
			add_action( 'wp_head','eos_dp_disable_javascript',10 );		
		}
	}
	else{
		if( defined( 'EOS_DP_DEBUG' ) && true === EOS_DP_DEBUG || ( isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_GET['show_disabled_plugins'] ) && $_GET['show_disabled_plugins'] === md5( $_SERVER['REMOTE_ADDR'].( absint( time()/1000 ) ) ) ) ){			
			$GLOBALS['eos_dp_user_can_preview'] = true;
			add_action( 'wp_footer','eos_dp_print_disabled_plugins',9999 );
		}
		
	}
	global $eos_dp_paths;
	global $eos_dp_disabled_plugins;
	foreach( $plugins as $p => $const ){
		$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
		if( !defined( 'EOS_'.$const.'_ACTIVE' ) ) define( 'EOS_'.$const.'_ACTIVE',true );
	}
	if( $eos_dp_paths === '' ) return $plugins;
	$eos_dp_paths = $eos_dp_paths ? $eos_dp_paths : array();
	$e = 0;
	foreach( $eos_dp_paths as $path ){
		$k = array_search( $path, $plugins );
		if( false !== $k ){
			unset( $plugins[$k] );
			if( in_array( $path,$plugins ) ){
				$eos_dp_disabled_plugins[] = $path;
			}
		}
		else{
			unset( $eos_dp_paths[$e] );
		}
		++$e;
	}
	register_shutdown_function( 'eos_dp_comment' );
	return $plugins;
}

//Replace theme for preview
function eos_dp_get_theme( $stylesheet ){
	$theme = $_REQUEST['theme'];
	return $theme ? esc_attr( $theme ) : $stylesheet;
}
//Return parent theme
function eos_dp_get_parent_theme( $template ){
	$themes = wp_get_themes();
	$child_theme = sanitize_key( $_REQUEST['theme'] );
	if( !isset( $themes[$child_theme] ) ) return $template;
	$theme = $themes[$child_theme];
	if( isset( $theme->template ) ){
		return $theme->template;
	}
	return $template;
}

//It replaces the theme with an almost empty theme provided by FDP
function eos_dp_replace_theme(){
	add_filter( 'stylesheet_directory','eos_dp_stylesheet_directory',20,3 );
	add_filter( 'theme_root','eos_dp_theme_root',20 );
	add_filter( 'stylesheet','eos_dp_template',20 );
	add_filter( 'template','eos_dp_template',20 );
}
//Replace theme with ultralight theme given by FDP
function eos_dp_stylesheet_directory( $stylesheet_dir,$stylesheet,$theme_root ){
	return EOS_DP_PLUGIN_DIR.'/fdp-theme';
}
function eos_dp_theme_root( $theme_root ){
	return EOS_DP_PLUGIN_DIR;
}
function eos_dp_template( $template ){
	return 'fdp-theme';
}

//Check preview nonce
function eos_check_dp_preview_nonce(){
	if( defined( 'EOS_DP_PRO_TESTING_UNIQUE_ID' ) ){
		if( isset( $_REQUEST['eos_dp_pro_id' ] ) && md5( EOS_DP_PRO_TESTING_UNIQUE_ID ) === $_REQUEST['eos_dp_pro_id'] ){
			$GLOBALS['eos_dp_user_can_preview'] = true;
			return true;
		}
	}
	if( !wp_verify_nonce( $_REQUEST['eos_dp_preview'],'eos_dp_preview' ) ){
		$nonce = get_transient( 'fdp_psi_nonce_'.sanitize_key( $_REQUEST['fdp_post_id'] ) );
		if( $nonce ){
			if( 1000*absint( time()/1000 ) === absint( $nonce ) ){
				$GLOBALS['eos_dp_user_can_preview'] = true;
				return true;
			}
		}
		echo '<p>It looks you are not allowed to see this preview.</p>';
		echo '<p>Check if you have the rights to activate and deactivate plugins.</p>';
		echo '<p>If you are sure you have the rights, try to log out, log in, and try again.</p>';
		echo '<p>If you still have problems, ask for help on the <a href="https://wordpress.org/support/plugin/freesoul-deactivate-plugins/">Freesoul Deactivate Plugins support forum</a>.</p>';
		exit;
	}
	$GLOBALS['eos_dp_user_can_preview'] = true;
}
//Display memory usage
function eos_dp_display_usage(){
    static $foo_called = false;
    if( $foo_called ) return;
    $foo_called = true;
	global $wpdb;
	$precision = 0;
	$memory_usage = memory_get_peak_usage() / 1048576;
	if( $memory_usage < 10 ){
		$precision = 2;
	}
	else if( $memory_usage < 100 ) {
		$precision = 1;
	}
	$usage = array(
		'queries' => sprintf( __( 'Queries %s','eos-dp' ),$wpdb->num_queries ),
		'wp_loaded' => sprintf( __( 'Initialization Time: %s %s','eos-dp' ),$GLOBALS['eos_dp_wp_loaded'],'s' ),
		'loading_time' => sprintf( __( 'Page Generation Time: %s %s','eos-dp' ),strval( round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],2 ) ),'s' ),
		'memory' => sprintf( __( 'Memory Usage: %s %s (%s)','eos-dp' ),round( $memory_usage, $precision ),'M',round( 100*$memory_usage/absint( ini_get( 'memory_limit' ) ),1 ).'%' )
	);
	echo '<div id="eos-dp-usage" style="z-index:9999999999;text-align:center;position:fixed;bottom:0;left:0;right:0">';
	echo '<div style="display:inline-block;padding:10px;background-color:#e5dada;background-color:rgba(229,218,218,0.8)">';
	$n = 0;
	$separators = array( ' | ',' | ',' | ','' );
	foreach( $usage as $key => $value ){
		echo '<span style="color:#000;font-size:20px;font-family:Arial" class="eos-dp-'.$key.'">'.esc_html( $value ).'</span>';
		echo '<span style="color:#000;font-size:20px;font-family:Arial" class="eos-dp-separator">'.$separators[$n].'</span>';
		++$n;
	}
	$left = is_rtl() ? 'right' : 'left';
	$right = is_rtl() ? 'left' : 'right';
	echo '<span title="Close" style="position:relative;margin-'.$right.':-8px;margin-'.$left.':20px;display:inline-block;top:-8px;padding:4px 8px 8px 8px;cursor:pointer;color:#000;font-size:20px;font-family:Arial" class="eos-dp-close" onclick="javascript:this.parentNode.parentNode.style.display = \'none\'">X</span>';
	echo '</div>';
	echo '</div>';
	eos_dp_print_disabled_plugins();
}

//Print usage in the JS console
function eos_dp_console_usage(){
	if( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
    static $cu_called = false;
    if( $cu_called ) return;
    $cu_called = true;
	global $wpdb;
	$precision = 0;
	$memory_usage = memory_get_peak_usage() / 1048576;
	if( $memory_usage < 10 ){
		$precision = 2;
	}
	else if( $memory_usage < 100 ) {
		$precision = 1;
	}
	$usage = array(
		'queries' => sprintf( __( 'Queries %s','eos-dp' ),$wpdb->num_queries ),
		'wp_loaded' => sprintf( __( 'Initialization Time: %s %s','eos-dp' ),$GLOBALS['eos_dp_wp_loaded'],'s' ),
		'loading_time' => sprintf( __( 'Page Generation Time: %s %s','eos-dp' ),strval( round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],2 ) ),'s' ),
		'memory' => sprintf( __( 'Memory Usage: %s %s (%s)','eos-dp' ),round( $memory_usage, $precision ),'M',round( 100*$memory_usage/absint( ini_get( 'memory_limit' ) ),1 ).'%' )
	);
	$n = 0;
	$output = PHP_EOL.'*************************************'.PHP_EOL;
	$output .= 'Usage measured by Freesoul Deactivate Plugins'.PHP_EOL.PHP_EOL;
	foreach( $usage as $key => $value ){
		$output .= esc_html( $value ).PHP_EOL;
		++$n;
	}
	$output .= '************************************'.PHP_EOL;
	echo '<script>if("undefined" === typeof(window.eos_dp_printed))console.log("'.esc_js( $output ).'");window.eos_dp_printed = true;</script>';
}

//Display comment before PHP shutdown
function eos_dp_comment(){
	global $eos_dp_paths;
	if( is_array( $eos_dp_paths ) ){
		$comment = sprintf( 'Freesoul Deactivate Plugins disabled %s plugins on this page',count( $eos_dp_paths ) );
		?>
		<!-- <?php echo esc_html( $comment ); ?> -->
		<?php
	}
}
//Get options in case of single or multisite installation.
function eos_dp_get_option( $option ){
	if( !is_multisite() ){
		return get_option( $option );
	}
	else{
		return get_blog_option( get_current_blog_id(),$option );
	}
}

//It checks if it's a mobile device
function eos_dp_is_mobile() {
	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) return false;
	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
		|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'a1-32ab0' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'a210' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'a211' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'b6000-h' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'b8000-h' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'bnrv200' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'bntv400' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'darwin' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'gt-n8005' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'gt-p3105' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'gt-p6810' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'gt-p7510' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'hmj37' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'hp-tablet' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'hp\sslate' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'hp\sslatebook' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ht7s3' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ideatab_a1107' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ideataba2109a' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ideos\ss7' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'imm76d' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ipad' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'k00f' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'kfjwi' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'kfot' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'kftt' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'kindle' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'l-06c' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lg-f200k' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lg-f200l' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lg-f200s' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'm470bsa' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'm470bse' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'maxwell' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'me173x' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mediapad' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'midc497' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'msi\senjoy\s10\splus' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mz601' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mz616' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nexus' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nookcolor' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pg09410' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pg41200' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pmp5570c' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pmp5588c' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pocketbook' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'qmv7a' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sgp311' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sgpt12' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'shv-e230k' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'shw-m305w' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'shw-m380w' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sm-p605' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'smarttab' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sonysgp321' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sph-p500' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'surfpad' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tab07-200' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tab10-201' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tab465euk' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tab474' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tablet' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tegranote' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tf700t' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'thinkpad' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'viewpad' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'voltaire' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), '(android|bb\d+|meego).+mobile' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), '2.0\ mmp' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), '240x320' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), '\bppc\b' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'acer\ s100' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'alcatel' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'amoi' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'archos5' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'asus' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'au-mic' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'audiovox' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'avantgo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'bada' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'benq' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'bird' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'blackberry' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'blazer' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'cdm' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'cellphone' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'cupcake' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'danger' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ddipocket' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'docomo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'docomo\ ht-03a' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'dopod' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'dream' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'elaine/3.0' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ericsson' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'eudoraweb' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'fly' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'froyo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'googlebot-mobile' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'haier' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'hiptop' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'hp.ipaq' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'htc' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'htc\ hero' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'htc\ magic' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'htc_dream' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'htc_magic' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'huawei' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'i-mobile' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'iemobile' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'iemobile/7' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'iemobile/7.0' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'iemobile/9' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'incognito' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'iphone' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ipod' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'j-phone' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'kddi' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'konka' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'kwc' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'kyocera/wx310k' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lenovo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lg' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lg-gw620' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lg/u990' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'lge\ vx' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'liquid\ build' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'maemo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'midp' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'midp-2.0' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mmef20' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mmp' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mobilephone' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mot-mb200' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mot-mb300' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mot-v' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'motorola' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'msie\ 10.0' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'netfront' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'newgen' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'newt' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nexus\ 7' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nexus\ one' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nintendo\ ds' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nintendo\ wii' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nitro' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'nokia' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'novarra' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'openweb' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'opera.mobi' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'opera\ mini' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'opera\ mobi' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'p160u' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'palm' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'panasonic' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pantech' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pdxgw' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'pg' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'philips' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'phone' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'playbook' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'playstation\ portable' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'portalmmm' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'proxinet' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'psp' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'qtek' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 's8000' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sagem' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'samsung' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'samsung-s8000' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sanyo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sch' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sch-i800' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sec' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sendo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'series60.*webkit' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'series60/5.0' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sgh' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sharp' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sharp-tq-gx10' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'small' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'smartphone' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'softbank' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sonyericsson' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sonyericssone10' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sonyericssonu20' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sonyericssonx10' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'sph' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'symbian' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'symbian\ os' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'symbianos' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 't-mobile\ mytouch\ 3g' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 't-mobile\ opal' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tattoo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'toshiba' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'touch' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'treo' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ts21i-10' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'up.browser' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'up.link' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'uts' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'vertu' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'vodafone' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'wap' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'webmate' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'webos' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'willcome' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'windows.ce' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'windows\ ce' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'winwap' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'xda' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'xoom' ) !== false
		|| strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'zte' ) !== false ) {
		return true;
	}
	return false;
}


//It returns the disabled plugins according to the mobile options
function eos_dp_disabled_plugins_on_mobile() {
	$mobile_options = eos_dp_get_option( 'eos_dp_mobile' );
	if( $mobile_options ){
		return array_values( eos_dp_get_option( 'eos_dp_mobile' ) );
	}
	return false;
}

//It returns the disabled plugins according to the search options
function eos_dp_disabled_plugins_on_search() {
	$search_options = eos_dp_get_option( 'eos_dp_search' );
	if( $search_options ){
		return array_values( eos_dp_get_option( 'eos_dp_search' ) );
	}
	return false;
}


add_filter( 'option_active_plugins', 'eos_dp_disabled_plugins_on_mobile_filter',10,1 );
//It filters the disabled plugins on mobile
function eos_dp_disabled_plugins_on_mobile_filter( $plugins ) {
	if ( !eos_dp_is_mobile() || is_admin() || class_exists( 'FS_Plugin_Updater' ) ) {
		return $plugins; // for desktops we do nothing
	}
	$disabled_on_mobile = eos_dp_disabled_plugins_on_mobile();
	if( $disabled_on_mobile ){
		foreach( $disabled_on_mobile as $p => $const ){
			$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
			if( !defined( 'EOS_'.$const.'_ACTIVE' ) ) define( 'EOS_'.$const.'_ACTIVE',true );
		}
		if( isset( $GLOBALS['eos_dp_paths'] ) && is_array( $GLOBALS['eos_dp_paths'] ) && is_array( $disabled_on_mobile ) ){
			$GLOBALS['eos_dp_paths'] = array_unique( array_merge( $GLOBALS['eos_dp_paths'],$disabled_on_mobile ) );
		}
		return array_values( array_diff( $plugins,$disabled_on_mobile ) );
	}
	return $plugins;
}

add_filter( 'option_active_plugins', 'eos_dp_disabled_plugins_on_search_filter',20,1 );
//It filters the disabled plugins on search
function eos_dp_disabled_plugins_on_search_filter( $plugins ) {
	if ( !isset( $_REQUEST['s'] ) || is_admin() || class_exists( 'FS_Plugin_Updater' ) ) {
		return $plugins;
	}
	$disabled_on_search = eos_dp_disabled_plugins_on_search();
	if( $disabled_on_search ){
		foreach( $disabled_on_search as $p => $const ){
			$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
			if( !defined( 'EOS_'.$const.'_ACTIVE' ) ) define( 'EOS_'.$const.'_ACTIVE',true );
		}		
		if( isset( $GLOBALS['eos_dp_paths'] ) && is_array( $GLOBALS['eos_dp_paths'] ) && is_array( $disabled_on_search ) ){
			$GLOBALS['eos_dp_paths'] = array_unique( array_merge( $GLOBALS['eos_dp_paths'],$disabled_on_search ) );
		}
		else{
			$GLOBALS['eos_dp_paths'] = $disabled_on_search;
		}
		if( defined( 'EOS_DP_DEBUG' ) && true === EOS_DP_DEBUG || ( isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_GET['show_disabled_plugins'] ) && $_GET['show_disabled_plugins'] === md5( $_SERVER['REMOTE_ADDR'] ) ) ){			
			$GLOBALS['eos_dp_user_can_preview'] = true;
			add_action( 'wp_footer','eos_dp_print_disabled_plugins',9999 );
		}		
		return array_values( array_diff( $plugins,$disabled_on_search ) );
	}
	return $plugins;
}

add_action( 'plugins_loaded','eos_dp_remove_filters',9999 );
//It removes the active plugins filters to avoid any issue with plugins that save the active_plugins option in the database
function eos_dp_remove_filters(){
	remove_filter( 'option_active_plugins','eos_option_active_plugins',0 );
	remove_filter( 'option_active_plugins','eos_dp_admin_option_active_plugins',0,1 );
	remove_filter( 'option_active_plugins','eos_dp_disabled_plugins_on_mobile_filter',10 );
	remove_filter( 'option_active_plugins','eos_dp_disabled_plugins_on_search_filter',20 );
}

//It prints the disabled plugins in the JavaScript console in case of preview and debug.
function eos_dp_print_disabled_plugins(){
	if( isset( $GLOBALS['eos_dp_paths'] ) && is_array( $GLOBALS['eos_dp_paths'] ) ){
		echo '<script>';
		echo 'if("undefined" === typeof(window.fdp_printed)){';
		echo 'console.log("*** PLUGINS DISABLED BY FREESOUL DEACTIVATE PLUGINS ***\n\r");';
		$n = 1;
		foreach( $GLOBALS['eos_dp_paths'] as $path ){
			echo '' !== $path ? 'console.log("'.esc_attr( esc_js( $n.') '.$path ) ).'");' : '';
			++$n;
		}
		echo 'console.log("\n\r*************************************\n\r");';
		echo 'window.fdp_printed = true;}';
		echo '</script>';
	}
}	

//Callback to filter array of customr URLs settings
function eos_dp_array_url_fragments_filter( $urlFragment,$uriA ){
	if( '' === $urlFragment ) return false;
	return false !== strpos( str_replace( '?','&',$uriA ),str_replace( '?','&',$urlFragment ) );
}

//Send JavaScript on modern browsers with the Content Security Policy
function eos_dp_disable_javascript(){
	?>
	<meta http-equiv="Content-Security-Policy" content="script-src 'none'">
	<?php
}