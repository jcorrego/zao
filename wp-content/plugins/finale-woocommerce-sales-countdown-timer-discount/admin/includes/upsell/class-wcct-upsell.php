<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCCT_Upsell {

	protected static $instance = null;
	protected $notice_time = array();
	protected $notice_displayed = false;
	protected $plugin_path = WCCT_PLUGIN_FILE;

	/**
	 * construct
	 */
	public function __construct() {
		$this->hooks();
		$this->set_notice_timings();
	}

	/**
	 * Getting class instance
	 * @return null|instance
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initiate hooks
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'xl_notice_variable' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'notice_enqueue_scripts' ) );
		add_action( 'wp_ajax_finale_upsells_dismiss', array( $this, 'xl_dismiss_notice' ) );

		add_action( 'admin_notices', array( $this, 'xl_christmas_sale_notice' ), 10 );
		add_action( 'admin_notices', array( $this, 'xl_bfcm_sale_notice' ), 10 );
		add_action( 'admin_notices', array( $this, 'xl_pre_black_friday_sale_notice' ), 10 );
		add_action( 'admin_notices', array( $this, 'xl_halloween_sale_notice' ), 10 );

		add_action( 'admin_notices', array( $this, 'xl_upsells_notice_html_finale' ), 10 );
		add_action( 'admin_notices', array( $this, 'xl_upsells_notice_html_nextmove' ), 10 );
		//      add_action( 'admin_notices', array( $this, 'xl_upsells_notice_html_sales_trigger' ), 10 );

		add_action( 'admin_notices', array( $this, 'xl_upsells_notice_js' ), 20 );
	}

	/**
	 * Assigning plugin notice timings
	 * Always use 2 time period as 'no'
	 */
	public function set_notice_timings() {
		$sales_trigger_notice_time = array(
			'0' => 8 * DAY_IN_SECONDS, // +8 days
			'1' => 4 * DAY_IN_SECONDS, // +4 days
		);
		//      $this->notice_time['sales_trigger'] = $sales_trigger_notice_time;

		$finale_notice_time          = array(
			'0' => 3 * DAY_IN_SECONDS, // +3 days
			'1' => 3 * DAY_IN_SECONDS, // +3 days
		);
		$this->notice_time['finale'] = $finale_notice_time;

		$nextmove_notice_time          = array(
			'0' => 15 * DAY_IN_SECONDS, // +15 days
			'1' => 3 * DAY_IN_SECONDS, // +3 days
		);
		$this->notice_time['nextmove'] = $nextmove_notice_time;

		$halloween_sale_notice_time             = array(
			'0' => 0.05 * DAY_IN_SECONDS, // +1.2 hrs
			'1' => 1 * DAY_IN_SECONDS, // +1 day
		);
		$this->notice_time['halloween_sale_18'] = $halloween_sale_notice_time;

		$pre_bf_sale_notice_time                  = array(
			'0' => 0.05 * DAY_IN_SECONDS, // +1.2 hrs
			'1' => 1 * DAY_IN_SECONDS, // +1 day
		);
		$this->notice_time['pre_black_friday_18'] = $pre_bf_sale_notice_time;

		$bfcm_sale_notice_time        = array(
			'0' => 0.05 * DAY_IN_SECONDS, // +1.2 hrs
			'1' => 1 * DAY_IN_SECONDS, // +1 day
		);
		$this->notice_time['bfcm_18'] = $bfcm_sale_notice_time;

		$christmas_sale_notice_time        = array(
			'0' => 0.05 * DAY_IN_SECONDS, // +1.2 hrs
			'1' => 1 * DAY_IN_SECONDS, // +1 day
		);
		$this->notice_time['christmas_18'] = $christmas_sale_notice_time;
	}

	/**
	 * Assign notice variable to false if not set
	 * @global boolean $xl_upsells_notice_active
	 */
	public function xl_notice_variable() {
		global $xl_upsells_notice_active;
		if ( '' == $xl_upsells_notice_active ) {
			$xl_upsells_notice_active = false;
		}
	}

	/**
	 * Enqueue assets
	 */
	public function notice_enqueue_scripts() {
		wp_enqueue_style( 'xl-notices-css', plugin_dir_url( $this->plugin_path ) . 'admin/assets/css/wcct-xl-notice.css', array(), WCCT_VERSION );
		wp_enqueue_script( 'wp-util' );
	}

	/**
	 * Upsell notice html - NextMove
	 * @global boolean $xl_upsells_notice_active
	 * @return type
	 */
	public function xl_upsells_notice_html_nextmove() {
		global $xl_upsells_notice_active;
		if ( true === $xl_upsells_notice_active ) {
			return;
		}
		if ( true === $this->plugin_already_installed( 'nextmove' ) ) {
			return;
		}
		if ( true === $this->hide_notice() ) {
			return;
		}
		if ( ! isset( $this->notice_time['nextmove'] ) ) {
			return;
		}
		$this->main_plugin_activation( 'nextmove' );
		if ( true === $this->notice_dismissed( 'nextmove' ) ) {
			return;
		}
		$this->notice_displayed = true;
		echo $this->nextmove_notice_html();
		$xl_upsells_notice_active = true;
	}

	/**
	 * Upsell notice html - Sales Trigger
	 * @global boolean $xl_upsells_notice_active
	 * @return type
	 */
	public function xl_upsells_notice_html_sales_trigger() {
		global $xl_upsells_notice_active;
		if ( true === $xl_upsells_notice_active ) {
			return;
		}
		if ( true === $this->plugin_already_installed( 'sales_trigger' ) ) {
			return;
		}
		if ( true === $this->hide_notice() ) {
			return;
		}
		if ( ! isset( $this->notice_time['sales_trigger'] ) ) {
			return;
		}
		$this->main_plugin_activation( 'sales_trigger' );
		if ( true === $this->notice_dismissed( 'sales_trigger' ) ) {
			return;
		}
		$this->notice_displayed = true;
		echo $this->sales_trigger_notice_html();
		$xl_upsells_notice_active = true;
	}

	/**
	 * Upsell notice html - Finale
	 * @global boolean $xl_upsells_notice_active
	 * @return type
	 */
	public function xl_upsells_notice_html_finale() {
		global $xl_upsells_notice_active;
		if ( true === $xl_upsells_notice_active ) {
			return;
		}
		if ( true === $this->plugin_already_installed( 'finale' ) ) {
			//return;
			/** As this is a lite plugin */
		}
		if ( true === $this->hide_notice() ) {
			return;
		}
		if ( ! isset( $this->notice_time['finale'] ) ) {
			return;
		}
		$this->main_plugin_activation( 'finale' );
		if ( true === $this->notice_dismissed( 'finale' ) ) {
			return;
		}
		$this->notice_displayed = true;
		echo $this->finale_notice_html();
		$xl_upsells_notice_active = true;
	}

	/**
	 * Halloween Sale notice html
	 * @global boolean $xl_upsells_notice_active
	 * @return type
	 */
	public function xl_halloween_sale_notice() {
		global $xl_upsells_notice_active;
		if ( true === $xl_upsells_notice_active ) {
			return;
		}
		if ( true === $this->hide_notice() ) {
			return;
		}
		if ( true === $this->valid_time_duration( 'halloween_sale_18' ) ) {
			return;
		}
		$this->main_plugin_activation( 'halloween_sale_18' );
		if ( true === $this->notice_dismissed( 'halloween_sale_18' ) ) {
			return;
		}
		$this->notice_displayed = true;
		echo $this->halloween_sale_notice_html();
		$xl_upsells_notice_active = true;
	}

	/**
	 * Pre Black Friday Sale notice html
	 * @global boolean $xl_upsells_notice_active
	 * @return type
	 */
	public function xl_pre_black_friday_sale_notice() {
		global $xl_upsells_notice_active;
		if ( true === $xl_upsells_notice_active ) {
			return;
		}
		if ( true === $this->hide_notice() ) {
			return;
		}
		if ( true === $this->valid_time_duration( 'pre_black_friday_18' ) ) {
			return;
		}
		$this->main_plugin_activation( 'pre_black_friday_18' );
		if ( true === $this->notice_dismissed( 'pre_black_friday_18' ) ) {
			return;
		}
		$this->notice_displayed = true;
		echo $this->pre_black_friday_notice_html();
		$xl_upsells_notice_active = true;
	}

	/**
	 * Black Friday Cyber Monday Sale notice html
	 * @global boolean $xl_upsells_notice_active
	 * @return type
	 */
	public function xl_bfcm_sale_notice() {
		global $xl_upsells_notice_active;
		if ( true === $xl_upsells_notice_active ) {
			return;
		}
		if ( true === $this->hide_notice() ) {
			return;
		}
		if ( true === $this->valid_time_duration( 'bfcm_18' ) ) {
			return;
		}
		$this->main_plugin_activation( 'bfcm_18' );
		if ( true === $this->notice_dismissed( 'bfcm_18' ) ) {
			return;
		}
		$this->notice_displayed = true;
		echo $this->bfcm_notice_html();
		$xl_upsells_notice_active = true;
	}

	/**
	 * Christmas New Year Sale notice html
	 * @global boolean $xl_upsells_notice_active
	 * @return type
	 */
	public function xl_christmas_sale_notice() {
		global $xl_upsells_notice_active;
		if ( true === $xl_upsells_notice_active ) {
			return;
		}
		if ( true === $this->hide_notice() ) {
			return;
		}
		if ( true === $this->valid_time_duration( 'christmas_18' ) ) {
			return;
		}
		$this->main_plugin_activation( 'christmas_18' );
		if ( true === $this->notice_dismissed( 'christmas_18' ) ) {
			return;
		}
		$this->notice_displayed = true;
		echo $this->christmas_notice_html();
		$xl_upsells_notice_active = true;
	}

	/**
	 * Checking if plugin already installed
	 * @return boolean
	 */
	public function plugin_already_installed( $plugin_short_name ) {
		if ( 'nextmove' == $plugin_short_name ) {
			if ( class_exists( 'XLWCTY_Core' ) ) {
				return true;
			}
		}
		if ( 'finale' == $plugin_short_name ) {
			if ( class_exists( 'WCCT_Core' ) ) {
				return true;
			}
		}
		if ( 'sales_trigger' == $plugin_short_name ) {
			if ( defined( 'WCST_SLUG' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Hide upsell notice on defined pages.
	 * @return boolean
	 */
	public function hide_notice() {
		$screen     = get_current_screen();
		$base_array = array( 'plugin-install', 'update-core', 'post', 'export', 'import', 'upload', 'media', 'edit' );
		$post_type  = 'wcct_countdown';
		if ( is_object( $screen ) && in_array( $screen->base, $base_array ) ) {
			if ( 'post' == $screen->base && $post_type == $screen->post_type ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * First time assigning display timings for plugin upsell
	 *
	 * @param type $plugin_short_name
	 */
	public function main_plugin_activation( $plugin_short_name ) {
		$notice_displayed_count = get_option( $plugin_short_name . '_upsell_displayed', '0' );
		if ( '0' == $notice_displayed_count ) {
			if ( isset( $this->notice_time[ $plugin_short_name ][ $notice_displayed_count ] ) && '' != $this->notice_time[ $plugin_short_name ][ $notice_displayed_count ] ) {
				$this->plugin_upsell_set_values( (int) $this->notice_time[ $plugin_short_name ][ $notice_displayed_count ], $plugin_short_name, ( (int) $notice_displayed_count + 1 ) );
			} else {
				// set expiration for an year
				$this->plugin_upsell_set_values( YEAR_IN_SECONDS, $plugin_short_name );
			}
		}
	}

	/**
	 * Setting values in transient or option for upsell plugin
	 *
	 * @param type $expire_time
	 * @param type $plugin_short_name
	 * @param type $notice_displayed_count
	 */
	public function plugin_upsell_set_values( $expire_time, $plugin_short_name, $notice_displayed_count = 100 ) {
		$this->set_xl_transient( $plugin_short_name . '_upsell_hold_time', 'yes', $expire_time );
		update_option( $plugin_short_name . '_upsell_displayed', $notice_displayed_count, false );
	}

	/**
	 * Check if the notice is dismissed
	 *
	 * @param type $plugin_short_name
	 *
	 * @return boolean
	 */
	public function notice_dismissed( $plugin_short_name ) {
		$upsell_dismissed_forever = get_option( $plugin_short_name . '_upsell_displayed', false );
		if ( '100' == $upsell_dismissed_forever ) {
			return true;
		}
		$notice_display = $this->get_xl_transient( $plugin_short_name . '_upsell_hold_time' );
		if ( false === $notice_display ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the notice display duration is correct
	 *
	 * @param $plugin_short_name
	 *
	 * @return boolean
	 */
	public function valid_time_duration( $plugin_short_name ) {
		$current_date_obj = new DateTime( 'now', new DateTimeZone( 'America/New_York' ) );
		if ( 'halloween_sale_18' == $plugin_short_name ) {
			if ( $current_date_obj->getTimestamp() > 1541131200 ) {
				return true;
			}
			// 1541131200 nov 2 midnight
		} elseif ( 'pre_black_friday_18' == $plugin_short_name ) {
			if ( $current_date_obj->getTimestamp() < 1542600000 || $current_date_obj->getTimestamp() > 1542945600 ) {
				return true;
			}
			// 1542600000 nov 19 midnight
			// 1542945600 nov 23 midnight
		} elseif ( 'bfcm_18' == $plugin_short_name ) {
			if ( $current_date_obj->getTimestamp() < 1542945600 || $current_date_obj->getTimestamp() > 1543550400 ) {
				return true;
			}
			// 1542945600 nov 23 midnight
			// 1543550400 nov 30 midnight
		} elseif ( 'christmas_18' == $plugin_short_name ) {
			if ( $current_date_obj->getTimestamp() < 1545278400 || $current_date_obj->getTimestamp() > 1546401600 ) {
				return true;
			}
			// 1545278400 dec 20 midnight
			// 1546401600 jan 2 midnight
		}

		return false;
	}

	/**
	 * Dismiss the notice via Ajax
	 * @return void
	 */
	public function xl_dismiss_notice() {
		if ( isset( $_POST['notice_displayed_count'] ) && ( '' != $_POST['notice_displayed_count'] ) ) {
			$notice_displayed_count = $_POST['notice_displayed_count'];
		} else {
			$notice_displayed_count = '100';
		}
		$this->dismiss_notice( $_POST['plugin'], $notice_displayed_count );
		wp_send_json_success();
	}

	/**
	 * Dismiss notice
	 *
	 * @param type $plugin_short_name
	 * @param type $notice_displayed_count
	 *
	 * @return void
	 */
	public function dismiss_notice( $plugin_short_name, $notice_displayed_count = '' ) {
		if ( empty( $notice_displayed_count ) ) {
			$notice_displayed_count = get_option( $plugin_short_name . '_upsell_displayed', '0' );
		}
		if ( '+1' == $notice_displayed_count ) {
			$notice_time = $this->notice_time[ $plugin_short_name ];
			end( $notice_time );
			$key = key( $notice_time );
			if ( isset( $notice_time[ $key ] ) && ( '' != $notice_time[ $key ] ) ) {
				$this->plugin_upsell_set_values( (int) $notice_time[ $key ], $plugin_short_name, ( (int) $key ) );

				return;
			}
		}
		if ( isset( $this->notice_time[ $plugin_short_name ][ $notice_displayed_count ] ) && ( '' != $this->notice_time[ $plugin_short_name ][ $notice_displayed_count ] ) ) {
			$this->plugin_upsell_set_values( (int) $this->notice_time[ $plugin_short_name ][ $notice_displayed_count ], $plugin_short_name, ( (int) $notice_displayed_count + 1 ) );
		} else {
			// set expiration for an year
			$this->plugin_upsell_set_values( YEAR_IN_SECONDS, $plugin_short_name );
		}
	}

	/**
	 * Upsell notice js
	 * common per plugin
	 */
	public function xl_upsells_notice_js() {
		if ( true === $this->notice_displayed ) {
			ob_start();
			?>
            <script type="text/javascript">
                (function ($) {
                    var noticeWrap = $('#xl_notice_type_2');
                    var pluginShortSlug = noticeWrap.attr("data-plugin");
                    var pluginSlug = noticeWrap.attr("data-plugin-slug");
                    noticeWrap.on('click', '.xl-notice-dismiss', function (e) {
                        e.preventDefault();
                        var $this = $(this);
                        var xlDisplayedMode = $this.attr("data-mode");
                        if (xlDisplayedMode == 'dismiss') {
                            xlDisplayedCount = '100';
                        } else if (xlDisplayedMode == 'later') {
                            xlDisplayedCount = '+1';
                        }
                        wp.ajax.send('finale_upsells_dismiss', {
                            data: {
                                plugin: pluginShortSlug,
                                notice_displayed_count: xlDisplayedCount,
                            },
                        });
                        $this.closest('.updated').slideUp('fast', function () {
                            $this.remove();
                        });
                    });
                    $(document).on('wp-plugin-install-success', function (e, args) {
                        if (args.slug == pluginSlug) {
                            wp.ajax.send('finale_upsells_dismiss', {
                                data: {
                                    plugin: pluginShortSlug,
                                    notice_displayed_count: '100',
                                },
                            });
                        }
                    });
                })(jQuery);
            </script>
			<?php
			echo ob_get_clean();
		}
	}

	/**
	 * NextMove upsell notice html
	 * @return type
	 */
	protected function nextmove_notice_html() {
		$plugin_slug = 'woo-thank-you-page-nextmove-lite';
		$plugin_url  = wp_nonce_url( add_query_arg( array(
			'action' => 'install-plugin',
			'plugin' => $plugin_slug,
			'from'   => 'import',
		), self_admin_url( 'update.php' ) ), 'install-plugin_' . $plugin_slug );
		ob_start();
		?>
        <div class="updated" id="xl_notice_type_2" data-plugin="nextmove" data-plugin-slug="<?php echo $plugin_slug; ?>">
            <div class="xl_upsell_area">
                <div class="xl_skew_arow"></div>
                <div class="upsell_left_abs">
                    <div class="xl_plugin_logo">
                        <img src="<?php echo plugin_dir_url( $this->plugin_path ) . 'admin/assets/img/nextmove.png'; ?>" alt="NextMove Logo">
                    </div>
                    <div class="xl_h4">NextMove</div>
                    <div class="xl_cen_icon"><i class="xl-icon"></i></div>
                </div>
                <div class="upsell_right_abs">
                    <div id="plugin-filter" class="upsell_xl_plugin_btn plugin-card plugin-card-<?php echo $plugin_slug; ?>">
                        <a class="install-now button" data-slug="<?php echo $plugin_slug; ?>" href="<?php echo $plugin_url; ?>" aria-label="Install WooCommerce Thank You Page – NextMove Lite"
                           data-name="Install WooCommerce Thank You Page – NextMove Lite">Try
                            for Free</a>
                        <p><a class="xl-notice-dismiss" data-mode="later" href="javascript:void(0)">May be
                                later</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="xl-notice-dismiss" data-mode="dismiss" title="Dismiss forever" href="javascript:void(0)">No, thanks</a></p>
                    </div>
                </div>
                <div class="upsell_main_abs">
                    <h3>Say good bye to templated & lousy Thank You pages. Hack your growth with NextMove.</h3>
                    <p>Use NextMove to create profit-pulling Thank You pages with plug & play components and watch your repeats orders explode.</p>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Sales Trigger upsell notice html
	 * @return type
	 */
	protected function sales_trigger_notice_html() {
		$sales_trigger_link = add_query_arg( array(
			'utm_source'   => 'finale-lite',
			'utm_medium'   => 'notice',
			'utm_campaign' => 'sales-trigger',
			'utm_term'     => 'more-info',
		), 'https://xlplugins.com/woocommerce-sales-triggers/' );
		ob_start();
		?>
        <div class="updated" id="xl_notice_type_2" data-plugin="sales_trigger">
            <div class="xl_upsell_area">
                <div class="xl_skew_arow"></div>
                <div class="upsell_left_abs">
                    <div class="xl_plugin_logo">
                        <img src="<?php echo plugin_dir_url( $this->plugin_path ) . 'admin/assets/img/sales-trigger.png'; ?>" alt="Sales Trigger Logo">
                    </div>
                    <div class="xl_h4">Sales Triggers</div>
                    <div class="xl_cen_icon"><i class="xl-icon"></i></div>
                </div>
                <div class="upsell_right_abs">
                    <div id="plugin-filter" class="upsell_xl_plugin_btn">
                        <a class="button" href="<?php echo $sales_trigger_link; ?>" data-name="XL WooCommerce Sales Triggers" target="_blank">Get More Info</a>
                        <p><a class="xl-notice-dismiss" data-mode="later" href="javascript:void(0)">May be
                                later</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="xl-notice-dismiss" data-mode="dismiss" title="Dismiss forever" href="javascript:void(0)">No, thanks</a></p>
                    </div>
                </div>
                <div class="upsell_main_abs">
                    <h3>Transform Weak WooCommerce Product Pages into Conversion Powerhouses.</h3>
                    <p>Deploy 7 Psychological Triggers On WooCommerce Products Page And Watch Your Sales Grow.</p>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Finale upsell notice html
	 * @return type
	 */
	protected function finale_notice_html() {
		$finale_link = add_query_arg( array(
			'utm_source'   => 'finale-lite',
			'utm_medium'   => 'notice',
			'utm_campaign' => 'finale',
			'utm_term'     => 'more-info',
		), 'https://xlplugins.com/finale-woocommerce-sales-countdown-timer-discount-plugin/' );
		ob_start();
		?>
        <div class="updated" id="xl_notice_type_2" data-plugin="finale">
            <div class="xl_upsell_area">
                <div class="xl_skew_arow"></div>
                <div class="upsell_left_abs">
                    <div class="xl_plugin_logo">
                        <img src="<?php echo plugin_dir_url( $this->plugin_path ) . 'admin/assets/img/finale.png'; ?>" alt="Finale Logo">
                    </div>
                    <div class="xl_h4">Finale</div>
                    <div class="xl_cen_icon"><i class="xl-icon"></i></div>
                </div>
                <div class="upsell_right_abs">
                    <div id="plugin-filter" class="upsell_xl_plugin_btn">
                        <a class="button" href="<?php echo $finale_link; ?>" data-name="Finale: WooCommerce Countdown Timer" target="_blank">Get More Info</a>
                        <p><a class="xl-notice-dismiss" data-mode="later" href="javascript:void(0)">May be
                                later</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="xl-notice-dismiss" data-mode="dismiss" title="Dismiss forever" href="javascript:void(0)">No, thanks</a></p>
                    </div>
                </div>
                <div class="upsell_main_abs">
                    <h3>Set up profit-pulling promotions in your WooCommerce Store this season.</h3>
                    <p>Finale helps store owners run seasonal offers, flash sales, deals of the day & festive offers to boost conversions.</p>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Haloween Sale notice html
	 * @return type
	 */
	protected function halloween_sale_notice_html() {
		$halloween_finale_link = add_query_arg( array(
			'utm_source'   => 'finale-lite',
			'utm_medium'   => 'notice',
			'utm_campaign' => 'halloween',
			'utm_term'     => 'sale',
		), 'https://xlplugins.com/finale-woocommerce-sales-countdown-timer-discount-plugin/' );
		ob_start();
		?>
        <div class="updated" id="xl_notice_type_2" data-plugin="halloween_sale_18">
            <div class="xl_upsell_area">
                <div class="xl_skew_arow"></div>
                <div class="upsell_left_abs">
                    <div class="xl_plugin_logo">
                        <img width="70" src="<?php echo plugin_dir_url( $this->plugin_path ) . 'admin/assets/img/halloween.jpg'; ?>" alt="Halloween Sale"/>
                    </div>
                    <div class="xl_h4">Halloween Sale</div>
                    <div class="xl_cen_icon"><i class="xl-icon"></i></div>
                </div>
                <div class="upsell_right_abs">
                    <div id="plugin-filter" class="upsell_xl_plugin_btn">
                        <a class="button" href="<?php echo $halloween_finale_link; ?>" data-name="XL WooCommerce Sales Triggers" target="_blank">Grab The Offer</a>
                        <p><a class="xl-notice-dismiss" data-mode="later" href="javascript:void(0)">May be
                                later</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="xl-notice-dismiss" data-mode="dismiss" title="Dismiss forever" href="javascript:void(0)">No, thanks</a></p>
                    </div>
                </div>
                <div class="upsell_main_abs">
                    <h3>Upgrade to FINALE PRO and Save 20%! Use coupon 'XLHALLOWEENSALE18'</h3>
                    <p>This <em>one-time</em> spooky deal ends on <em>November 2nd</em> 12 AM EST. 'Act Fast!'</p>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Pre Black Friday Sale notice html
	 * @return type
	 */
	protected function pre_black_friday_notice_html() {
		$pre_black_friday_finale_link = add_query_arg( array(
			'utm_source'   => 'finale-lite',
			'utm_medium'   => 'notice',
			'utm_campaign' => 'pre-black-friday-18',
			'utm_term'     => 'sale',
		), 'https://xlplugins.com/finale-woocommerce-sales-countdown-timer-discount-plugin/' );
		ob_start();
		?>
        <div class="updated" id="xl_notice_type_2" data-plugin="pre_black_friday_18">
            <div class="xl_upsell_area">
                <div class="xl_skew_arow"></div>
                <div class="upsell_left_abs">
                    <div class="xl_plugin_logo">
                        <img width="70" src="<?php echo plugin_dir_url( $this->plugin_path ) . 'admin/assets/img/black-friday.jpg'; ?>" alt="Pre Black Friday Sale"/>
                    </div>
                    <div class="xl_h4">Pre Black Friday Sale</div>
                    <div class="xl_cen_icon"><i class="xl-icon"></i></div>
                </div>
                <div class="upsell_right_abs">
                    <div id="plugin-filter" class="upsell_xl_plugin_btn">
                        <a class="button" href="<?php echo $pre_black_friday_finale_link; ?>" data-name="Finale: WooCommerce Countdown Timer" target="_blank">Grab The Offer</a>
                        <p><a class="xl-notice-dismiss" data-mode="later" href="javascript:void(0)">May be
                                later</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="xl-notice-dismiss" data-mode="dismiss" title="Dismiss forever" href="javascript:void(0)">No, thanks</a></p>
                    </div>
                </div>
                <div class="upsell_main_abs">
                    <h3>Prepare your store for Black Friday Sale. Upgrade to FINALE PRO and Save 20%!</h3>
                    <p>Use coupon 'XLPREBF18'. Deal expires on <em>November 23th</em> 12 AM EST. 'Act Fast!'</p>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Black Friday Cyber Monday Sale notice html
	 * @return type
	 */
	protected function bfcm_notice_html() {
		$bfcm_finale_link = add_query_arg( array(
			'utm_source'   => 'finale-lite',
			'utm_medium'   => 'notice',
			'utm_campaign' => 'bfcm-18',
			'utm_term'     => 'sale',
		), 'https://xlplugins.com/finale-woocommerce-sales-countdown-timer-discount-plugin/' );
		ob_start();
		?>
        <div class="updated" id="xl_notice_type_2" data-plugin="bfcm_18">
            <div class="xl_upsell_area">
                <div class="xl_skew_arow"></div>
                <div class="upsell_left_abs">
                    <div class="xl_plugin_logo">
                        <img width="70" src="<?php echo plugin_dir_url( $this->plugin_path ) . 'admin/assets/img/black-friday.jpg'; ?>" alt="Black Friday Sale"/>
                    </div>
                    <div class="xl_h4">Black Friday Sale</div>
                    <div class="xl_cen_icon"><i class="xl-icon"></i></div>
                </div>
                <div class="upsell_right_abs">
                    <div id="plugin-filter" class="upsell_xl_plugin_btn">
                        <a class="button" href="<?php echo $bfcm_finale_link; ?>" data-name="Finale: WooCommerce Countdown Timer" target="_blank">Grab The Offer</a>
                        <p><a class="xl-notice-dismiss" data-mode="later" href="javascript:void(0)">May be
                                later</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="xl-notice-dismiss" data-mode="dismiss" title="Dismiss forever" href="javascript:void(0)">No, thanks</a></p>
                    </div>
                </div>
                <div class="upsell_main_abs">
                    <h3>Upgrade to FINALE PRO and Save 30%! Use coupon 'XLBFCM18'</h3>
                    <p>Get a super high 30% off on our plugins. Act fast! Deal expires on <em>November 30th</em> 12 AM EST.</p>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Christmas New Year Sale notice html
	 * @return type
	 */
	protected function christmas_notice_html() {
		$christmas_finale_link = add_query_arg( array(
			'utm_source'   => 'finale-lite',
			'utm_medium'   => 'notice',
			'utm_campaign' => 'christmas-18',
			'utm_term'     => 'sale',
		), 'https://xlplugins.com/finale-woocommerce-sales-countdown-timer-discount-plugin/' );
		ob_start();
		?>
        <div class="updated" id="xl_notice_type_2" data-plugin="christmas_18">
            <div class="xl_upsell_area">
                <div class="xl_skew_arow"></div>
                <div class="upsell_left_abs">
                    <div class="xl_plugin_logo">
                        <img width="70" src="<?php echo plugin_dir_url( $this->plugin_path ) . 'admin/assets/img/christmas.jpg'; ?>" alt="Christmas Sale"/>
                    </div>
                    <div class="xl_h4">Christmas Sale</div>
                    <div class="xl_cen_icon"><i class="xl-icon"></i></div>
                </div>
                <div class="upsell_right_abs">
                    <div id="plugin-filter" class="upsell_xl_plugin_btn">
                        <a class="button" href="<?php echo $christmas_finale_link; ?>" data-name="Finale: WooCommerce Countdown Timer" target="_blank">Grab The Offer</a>
                        <p><a class="xl-notice-dismiss" data-mode="later" href="javascript:void(0)">May be
                                later</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="xl-notice-dismiss" data-mode="dismiss" title="Dismiss forever" href="javascript:void(0)">No, thanks</a></p>
                    </div>
                </div>
                <div class="upsell_main_abs">
                    <h3>Upgrade to FINALE PRO and Save 25%! Use coupon 'XLCHRISTMAS18'</h3>
                    <p>Get a super high 25% off on our plugins. Act fast! Deal expires on <em>January 2nd</em> 12 AM EST.</p>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}


	/**
	 * Set custom transient as native transient sometimes don't save when cache plugins active
	 *
	 * @param type $key
	 * @param type $value
	 * @param type $expirtaion
	 */
	public function set_xl_transient( $key, $value, $expirtaion ) {
		$array = array(
			'time'  => time() + (int) $expirtaion,
			'value' => $value,
		);
		update_option( '_xl_transient_' . $key, $array, false );
	}

	/**
	 * get custom transient value
	 *
	 * @param type $key
	 *
	 * @return boolean
	 */
	public function get_xl_transient( $key ) {
		$data = get_option( '_xl_transient_' . $key, false );
		if ( false === $data ) {
			return false;
		}
		$current_time = time();
		if ( is_array( $data ) && isset( $data['time'] ) ) {
			if ( $current_time > (int) $data['time'] ) {
				delete_option( '_xl_transient_' . $key );

				return false;
			} else {
				return $data['value'];
			}
		}

		return false;
	}
}

WCCT_Upsell::get_instance();
