<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://agilelogix.com
 * @since      1.0.0
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/public
 * @author     Your Name <email@agilelogix.com>
 */
class AgileStoreLocator_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $AgileStoreLocator    The ID of this plugin.
	 */
	private $AgileStoreLocator;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $AgileStoreLocator       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $AgileStoreLocator, $version ) {

		$this->AgileStoreLocator = $AgileStoreLocator;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in AgileStoreLocator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The AgileStoreLocator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->AgileStoreLocator.'-all-css',  AGILESTORELOCATOR_URL_PATH.'public/css/all-css.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->AgileStoreLocator.'-asl-responsive',  AGILESTORELOCATOR_URL_PATH.'public/css/asl_responsive.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->AgileStoreLocator.'-asl',  AGILESTORELOCATOR_URL_PATH.'public/css/asl.css', array(), $this->version, 'all' );
	}


	/*Frontend of Plugin*/
	public function frontendStoreLocator($atts)
	{
		
		//[myshortcode foo="bar" bar="bing"]
	  //AGILESTORELOCATOR_PLUGIN_PATH.

		wp_enqueue_script( $this->AgileStoreLocator.'-script', AGILESTORELOCATOR_URL_PATH . 'public/js/site_script.js', array('jquery'), $this->version, true );
		
	    
		if(!$atts) {
			$atts = array();
		}
		
		
		global $wpdb;

		$query   = "SELECT * FROM ".AGILESTORELOCATOR_PREFIX."configs";
		$configs = $wpdb->get_results($query);

		$all_configs = array('target_blank' => '1');
		
		foreach($configs as $_config)
			$all_configs[$_config->key] = $_config->value;


		$all_configs = shortcode_atts( $all_configs, $atts );
		
		$all_configs['URL'] = AGILESTORELOCATOR_URL_PATH;
		

		//Get the categories
		$all_categories = array();
		$results = $wpdb->get_results("SELECT id,category_name as name,icon FROM ".AGILESTORELOCATOR_PREFIX."categories WHERE is_active = 1");

		foreach($results as $_result)
		{
			$all_categories[$_result->id] = $_result;
		}


		//Get the Markers
		$all_markers = array();
		$results = $wpdb->get_results("SELECT id,marker_name as name,icon FROM ".AGILESTORELOCATOR_PREFIX."markers WHERE is_active = 1");

		foreach($results as $_result)
		{
			$all_markers[$_result->id] = $_result;
		}


		$all_configs['map_layout'] = '[]';

		
			
		//For Translation	
		$words = array(
			'direction' => __('Directions','asl_locator'),
			'zoom' => __('Zoom Here','asl_locator'),
			'detail' => __('Website','asl_locator'),
			'select_option' => __('Select Option','asl_locator'),
			'none' => __('None','asl_locator')
		);

		$all_configs['words'] 	= $words;
		$all_configs['version'] = AGILESTORELOCATOR_CVERSION;
		
		$template_file = 'template-frontend.php';


		add_filter('script_loader_tag', array($this, 'removeGoogleMapsTag'), 9999999, 3);

		
		ob_start();

		//Customization of Template
		if($template_file) {

			if ( $theme_file = locate_template( array ( $template_file ) ) ) {
	            $template_path = $theme_file;
	        }
	        else {
	            $template_path = 'partials/'.$template_file;
	        }

	        include $template_path;
		}
		

		$output = ob_get_contents();
		ob_end_clean();


		$title_nonce = wp_create_nonce( 'asl_remote_nonce' );
		
		wp_localize_script( $this->AgileStoreLocator.'-script', 'ASL_REMOTE', array(
		    'ajax_url' => admin_url( 'admin-ajax.php' ),
		    'nonce'    => $title_nonce // It is common practice to comma after
		) );

		wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_configuration',$all_configs);
		wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_categories',$all_categories);
		wp_localize_script( $this->AgileStoreLocator.'-script', 'asl_markers',array());


		return $output;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in AgileStoreLocator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The AgileStoreLocator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$title_nonce = wp_create_nonce( 'asl_remote_nonce' );
		
		
		global $wp_scripts,$wpdb;

		$sql = "SELECT `key`,`value` FROM ".AGILESTORELOCATOR_PREFIX."configs WHERE `key` = 'api_key'";
		$all_result = $wpdb->get_results($sql);
		

		$map_url = '//maps.googleapis.com/maps/api/js?libraries=places,drawing';

		if($all_result[0] && $all_result[0]->value) {
			$api_key = $all_result[0]->value;

			$map_url .= '&key='.$api_key;
		}

		//map language and region
		$sql = "SELECT `key`,`value` FROM ".AGILESTORELOCATOR_PREFIX."configs WHERE `key` = 'map_language' OR `key` = 'map_region' ORDER BY id ASC;";
		$all_result = $wpdb->get_results($sql);
		

		if(isset($all_result[0]) && $all_result[0]->value) {
			
			$map_country = $all_result[0]->value;
			$map_url .= '&language='.$map_country;
		}

		if(isset($all_result[1]) && $all_result[1]->value) {
			
			$map_region = $all_result[1]->value;
			$map_url   .= '&region='.$map_region;
		}
		

		//dd($wp_scripts->registered);
		wp_enqueue_script('asl_google_maps', $map_url,array('jquery'), null, true  );
		wp_enqueue_script( $this->AgileStoreLocator.'-lib', AGILESTORELOCATOR_URL_PATH . 'public/js/libs_new.min.js', array('jquery'), $this->version, true );

	}

	public function removeGoogleMapsTag($tag, $handle, $src)
	{

		if(preg_match('/maps\.google/i', $src))
		{
			if($handle != 'asl_google_maps')
				return '';
		}

		return $tag;
	}


	public function load_stores()
	{
		//header('Content-Type: application/json');
		global $wpdb;
				

		$nonce = isset($_GET['nonce'])?$_GET['nonce']:null;
		/*
		if ( ! wp_verify_nonce( $nonce, 'asl_remote_nonce' ))
 			die ( 'CRF check error.');
 		*/


		$AGILESTORELOCATOR_PREFIX = AGILESTORELOCATOR_PREFIX;

		$bound   = '';

		$extra_sql = '';
		$country_field = '';

		

		$query   = "SELECT s.`id`, `title`,  `description`, `street`,  `city`,  `state`, `postal_code`, {$country_field} `lat`,`lng`,`phone`,  `fax`,`email`,`website`,`logo_id`,{$AGILESTORELOCATOR_PREFIX}storelogos.`path`,`open_hours`,`ordr`,
					group_concat(category_id) as categories FROM {$AGILESTORELOCATOR_PREFIX}stores as s 
					LEFT JOIN {$AGILESTORELOCATOR_PREFIX}storelogos ON logo_id = {$AGILESTORELOCATOR_PREFIX}storelogos.id
					LEFT JOIN {$AGILESTORELOCATOR_PREFIX}stores_categories ON s.`id` = {$AGILESTORELOCATOR_PREFIX}stores_categories.store_id
					$extra_sql
					WHERE (is_disabled is NULL || is_disabled = 0) 
					GROUP BY s.`id` ";

		$query .= "LIMIT 1500";

		
		$all_results = $wpdb->get_results($query);



		//die($wpdb->last_error);
		$days_in_words = array('sun'=>__( 'Sun','asl_locator'), 'mon'=>__('Mon','asl_locator'), 'tue'=>__( 'Tues','asl_locator'), 'wed'=>__( 'Wed','asl_locator' ), 'thu'=> __( 'Thur','asl_locator'), 'fri'=>__( 'Fri','asl_locator' ), 'sat'=> __( 'Sat','asl_locator')) ;
		$days 		   = array('mon','tue','wed','thu','fri','sat','sun');


		foreach($all_results as $aRow) {

			if($aRow->open_hours) {

				$days_are 	= array();
				$open_hours = json_decode($aRow->open_hours);

				foreach($days as $day) {

					if(!empty($open_hours->$day)) {

						$days_are[] = $days_in_words[$day];
					}
				}

				$aRow->days_str = implode(', ', $days_are);
			}
	    }


		echo json_encode($all_results);die;
	}

}
