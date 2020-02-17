<?php

namespace SVIApp;

/**
 * Admin Pages Handler
 */
class Admin
{
    public function __construct()
    {
        $plugin_public = new Frontend();
        $this->options = get_option( 'woosvi_options', [] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'woocommerce_product_write_panel_tabs', [ $this, 'sviproimages_section' ] );
        $panels = 'woocommerce_product_data_panels';
        if ( $this->version_check() ) {
            $panels = 'woocommerce_product_write_panels';
        }
        add_action( $panels, [ $this, 'sviproimages_settings' ] );
        add_action( 'wp_ajax_woosvi_esc_html', [ $this, 'woosvi_esc_html' ] );
        add_action( 'wp_ajax_woosvi_reloadselect', [ $this, 'reloadSelect_json' ] );
        add_action(
            'woocommerce_product_options_advanced',
            [ $this, 'action_woocommerce_product_options_advanced' ],
            10,
            0
        );
        add_action( 'woocommerce_process_product_meta', [ $this, 'woo_add_custom_general_fields_save' ] );
        add_filter(
            'woocommerce_product_export_meta_value',
            [ $this, 'woo_handle_export' ],
            10,
            4
        );
        $this->activated = ( array_key_exists( 'default', $this->options ) ? $this->options['default'] : false );
        if ( !$this->activated ) {
            //Check if SVI is enabled
            add_action( 'admin_notices', array( $this, 'displayAdminNoticeV4' ) );
        }
        add_action( 'admin_init', array( 'PAnD', 'init' ) );
        add_action(
            'woocommerce_variation_options',
            [ $this, 'mega_teste' ],
            10,
            3
        );
    }
    
    public function mega_teste()
    {
        echo  '<div class="svi-variation-gallery"><h3>Smart Variation image gallery</h3><a href="#" class="button button-primary svi-add-additional-images">' . __( 'Add additional images', 'svi' ) . '</a></div>' ;
    }
    
    public function displayAdminNoticeV4()
    {
        if ( !\PAnD::is_admin_notice_active( 'disablesvi-done-notice-forever' ) ) {
            return;
        }
        $settings_link = '<a href="admin.php?page=woocommerce_svi">' . __( "WooCommerce > SVI", "wc_svi" ) . '</a>';
        ?>
<div data-dismissible="disablesvi-done-notice-forever" class="notice notice-warning is-dismissible">
    <p style="line-height: 1.4em;">
        <img src="<?php 
        echo  SVI_URL ;
        ?>/assets/img/svi-notice.png"
            style="float: left; height: auto; margin-right: 1em;">
        <strong>SVI is activated but not yet available to the public!</strong>
        <br>
        This allows you to work on your products until you are ready to display it to the World!<br>
        You may turn it on going to <?php 
        echo  $settings_link ;
        ?>.<br>
        Dont understand how to setup SVI, watch this youtube <a target="_blank"
            href="https://www.youtube.com/watch?v=QMV8XBeub_o">video</a>.
    </p>
</div>
<?php 
    }
    
    /**
     * Check WooCommerce version
     *
     * @since     1.0.0
     * @return    boolean
     */
    public static function version_check( $version = '3.0' )
    {
        
        if ( class_exists( 'WooCommerce' ) ) {
            global  $woocommerce ;
            if ( version_compare( $woocommerce->version, $version, "<=" ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        $screen = get_current_screen();
        
        if ( $screen->post_type == 'product' || $screen->base == 'woocommerce_page_woocommerce_svi' ) {
            wp_enqueue_style( 'svi-admin' );
            wp_enqueue_script( 'svi-admin' );
        }
    
    }
    
    /**
     * Add tab to WooCommerce Product
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function sviproimages_section()
    {
        $screen = get_current_screen();
        echo  '<li class="box_tab show_if_variable"><a href="#sviproimages_tab_data" id="svibulkbtn"><span>' . __( 'SVI <b>Variations Gallery</b>', 'svi' ) . '</span></a></li>' ;
    }
    
    /**
     * Clean names to prevent breaks
     *
     * @return void
     */
    public function woosvi_esc_html()
    {
        header( "Content-type: application/json" );
        echo  esc_html( $_POST['data'] ) ;
        die;
    }
    
    /**
     * Builds Html with content of TAB
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function sviproimages_settings()
    {
        echo  '<div id="sviproimages_tab_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">' ;
        echo  '<div class="wc-metabox">' ;
        $this->buildSelect();
        echo  '</div>' ;
        echo  '</div>' ;
    }
    
    /**
     * Builds the varitions display on product page load
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function buildSelect( $id = false )
    {
        global  $post ;
        $pid = $post->ID;
        $attributes = get_post_meta( $pid, '_product_attributes' );
        
        if ( count( $attributes ) < 1 ) {
            echo  '<div id="message" class="inline notice woocommerce-message">' ;
            echo  '<p>Before you can assign images to a variation you need to add some variation attributes on the <strong>Attributes</strong> tab and <b>save the product<b>.</p>' ;
            echo  '</div>' ;
            return;
        }
        
        echo  '<div class="wc-metabox-content">' ;
        echo  '<table cellspacing="0" cellpadding="0">' ;
        echo  '<tr>' ;
        echo  ' <td class="attribute_name">' ;
        echo  '<strong>Assign Images to : </strong>' ;
        echo  '</td>' ;
        echo  '<td>' ;
        echo  "<div id='sviselect_container'>" ;
        echo  "<select id='sviprobulk'>" ;
        $existing = array();
        foreach ( $attributes[0] as $att => $attribute ) {
            
            if ( $attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                $terms = wp_get_post_terms( $pid, urldecode( $att ), 'all' );
                
                if ( !empty($terms) ) {
                    $tax = get_taxonomy( $att );
                    echo  '<optgroup label="' . $tax->label . '">' ;
                    foreach ( $terms as $tr => $term ) {
                        echo  "<option value='" . esc_attr( $term->slug ) . "'>" . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . "</option>" ;
                        array_push( $existing, esc_attr( $term->slug ) );
                    }
                    echo  '</optgroup>' ;
                }
            
            } elseif ( !$attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                $terms = explode( '|', $attribute['value'] );
                echo  '<optgroup label="' . $attribute['name'] . '">' ;
                foreach ( $terms as $tr => $term ) {
                    echo  '<option value="' . sanitize_title( $term ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term ) ) . '</option>' ;
                    array_push( $existing, sanitize_title( $term ) );
                }
                echo  '</optgroup>' ;
            }
        
        }
        echo  "</select>" ;
        echo  "</div>" ;
        echo  '<button id="addsviprovariation" class="button fr plus">Add</button>' ;
        echo  '</td>' ;
        echo  '</tr>' ;
        echo  '<tr><td colspan="2">
            <b>(SVI PRO VERSION FEATURES)</b><br><small>' . __( 'Unlock all features <a href="/wp-admin/admin.php?page=woocommerce_svi-pricing" target="_blank">here</a>.', 'wc_svi' ) . '</small><br><br>
            <b><small><span class="svibadge svibadge-warning">Default Gallery</span> Create a default gallery to be displayed. All other images will be hidden until match occours.</small></b><br>
            <b><small><span class="svibadge svibadge-warning">SVI Global</span> Use this variation to assign global images to be showed in all variations.</small></b><br><br>
            <b><small><span class="svibadge svibadge-success">Multiple Attributes</span> Build galleries with more than 1 Attribute, ex: Size + Color.</small></b><br>
            <b><small><span class="svibadge svibadge-success">Sortable/draggable</span> Sort images into the correct position or drag and drop image between galleries.</small></b></td></tr>' ;
        echo  '</table>' ;
        echo  '</div>' ;
        echo  '<div class="clear"></div>' ;
        echo  '<div id="svigallery">' ;
        echo  $this->output( $pid, $existing ) ;
        echo  '</div>' ;
        echo  '<div id="svipro_clone" class="postbox svi-woocommerce-product-images hidden" data-svigal="">' ;
        echo  '<h2><span class="svititle">Product Gallery</span><a href="#/" class="svi-pullright sviprobulk_remove"><span class="dashicons dashicons-trash"></span></a></h2>' ;
        echo  '<div class="inside">' ;
        echo  '<div class="svipro-product_images_container">' ;
        echo  '<ul class="product_images ui-sortable product_galsort">' ;
        echo  '<li class="add_product_images_svipro hide-if-no-js ui-state-disabled">' ;
        echo  '<a href="#/" data-choose="Add Images to Product Gallery" data-update="Add to gallery" data-delete="Delete image" data-text="Delete"><span class="dashicons dashicons-plus"></span></a>' ;
        echo  '</li>' ;
        echo  '</ul>' ;
        echo  '<span class="sviHiddenLoop">Hide from <b>Product Loop</b>: (PRO VERSION FEATURE)</span>' ;
        echo  ' <input class="svipro-product_image_gallery" name="" value="" type="hidden">' ;
        echo  '</div>' ;
        echo  '</div>' ;
        echo  '</div>' ;
    }
    
    /**
     * Runs the fallback
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function fallback( $pid )
    {
        $return = '';
        $product_image_gallery = $this->getProductGallery( $pid );
        if ( !$product_image_gallery ) {
            return;
        }
        $order = array();
        foreach ( $product_image_gallery as $key => $value ) {
            $woosvi_slug = get_post_meta( $value, 'woosvi_slug_' . $pid, true );
            
            if ( is_array( $woosvi_slug ) ) {
                $data = array();
                foreach ( $woosvi_slug as $k => $v ) {
                    
                    if ( count( $v ) > 1 ) {
                        $data[] = implode( '_svipro_', $v );
                    } else {
                        $data[] = $v;
                    }
                
                }
                $woosvi_slug = $data;
            }
            
            if ( !$woosvi_slug ) {
                $woosvi_slug = get_post_meta( $value, 'woosvi_slug', true );
            }
            if ( !$woosvi_slug ) {
                $woosvi_slug = 'nullsvi';
            }
            
            if ( is_array( $woosvi_slug ) ) {
                foreach ( $woosvi_slug as $k => $v ) {
                    
                    if ( is_array( $v ) ) {
                        $order[$v[0]][] = $value;
                    } else {
                        $order[$v][] = $value;
                    }
                
                }
            } else {
                $order[$woosvi_slug][] = $value;
            }
        
        }
        unset( $order['nullsvi'] );
        $ordered = array();
        foreach ( $order as $k => $v ) {
            $arr = array(
                'slugs' => explode( '_svipro_', $k ),
                'imgs'  => $v,
            );
            array_push( $ordered, $arr );
        }
        update_post_meta( $pid, 'woosvi_slug', $ordered );
    }
    
    public function getAttributes( $attributes, $pid )
    {
        $data = array();
        if ( count( $attributes ) > 0 ) {
            foreach ( $attributes[0] as $att => $attribute ) {
                
                if ( $attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                    $terms = wp_get_post_terms( $pid, urldecode( $att ), 'all' );
                    
                    if ( !empty($terms) ) {
                        $tax = get_taxonomy( $att );
                        foreach ( $terms as $tr => $term ) {
                            $data[strtolower( esc_attr( $term->slug ) )] = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
                        }
                    }
                
                } elseif ( !$attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                    $terms = explode( '|', $attribute['value'] );
                    foreach ( $terms as $tr => $term ) {
                        $data[sanitize_title( $term )] = esc_html( apply_filters( 'woocommerce_variation_option_name', $term ) );
                    }
                }
            
            }
        }
        return array_filter( $data );
    }
    
    public function getImagesAssignedWithVariations( $pid, $woosvi_slug = array() )
    {
        $asigned_svi = array();
        $product_image_gallery = array();
        if ( !empty($woosvi_slug) && count( $woosvi_slug ) > 0 ) {
            foreach ( $woosvi_slug as $k => $v ) {
                $asigned_svi = array_unique( array_merge( $asigned_svi, $v['imgs'] ) );
            }
        }
        
        if ( metadata_exists( 'post', $pid, '_product_image_gallery' ) ) {
            $product_image_gallery = explode( ',', get_post_meta( $pid, '_product_image_gallery', true ) );
        } else {
            // Backwards compat
            $attachment_ids = get_posts( 'post_parent=' . $pid . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
            $attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
            if ( $attachment_ids ) {
                $product_image_gallery = explode( ',', $attachment_ids );
            }
        }
        
        return array_diff( $product_image_gallery, $asigned_svi );
    }
    
    /**
     * Returns the variations tab + images
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function output( $pid, $existing = false )
    {
        $return = '';
        $errors = [];
        $woosvi_slug = get_post_meta( $pid, 'woosvi_slug', true );
        $attributes = get_post_meta( $pid, '_product_attributes' );
        $theslugs = $this->getAttributes( $attributes, $pid );
        
        if ( empty($woosvi_slug) ) {
            $this->fallback( $pid );
            $woosvi_slug = get_post_meta( $pid, 'woosvi_slug', true );
        }
        
        $unsigned_svi = $this->getImagesAssignedWithVariations( $pid, $woosvi_slug );
        
        if ( !empty($unsigned_svi) ) {
            $title = "Images without assigned variations";
            $return .= $this->outputOrder( $title, 'unsigned_svi', $unsigned_svi );
        }
        
        if ( !empty($woosvi_slug) ) {
            foreach ( $woosvi_slug as $key => $data ) {
                
                if ( is_array( $data['slugs'] ) && count( $data['slugs'] ) > 1 ) {
                    $slugs_name = array();
                    foreach ( $data['slugs'] as $d => $s ) {
                        
                        if ( array_key_exists( strtolower( $s ), $theslugs ) ) {
                            $slugs_name[] = $theslugs[strtolower( $s )];
                        } else {
                            $bigger = 0;
                            foreach ( $theslugs as $extra => $check ) {
                                $sim = similar_text( $extra, $s, $perc );
                                
                                if ( $perc > $bigger ) {
                                    $bigger = $perc;
                                    $keep = $extra;
                                }
                            
                            }
                            //$slugs_name[] = $check;
                            //$slugs_name[] = $keep;
                            $slugs_name[] = "<span class='dashicons dashicons-hidden'></span>";
                            $errors[$s] = "The SVI saved attribute doesnt match the current WooCommerce attribute. <b><u>" . strtolower( $s ) . "</u> != <u>" . strtolower( $keep ) . "</u></b>.<br>Maybe you changed the attribute slug? Please fix this before proceeding otherwise matching may not work.<br>To fix this edit/replace the current attribute <b>slug</b> <u>" . strtolower( $keep ) . "</u> to <u>" . strtolower( $s ) . "</u> under <b>Product > Attributes</b> or remove this gallery and build a new one with the current attribute.";
                        }
                    
                    }
                    $title = implode( ' + ', $slugs_name ) . ' Gallery';
                } else {
                    
                    if ( is_array( $data['slugs'] ) ) {
                        $slugs_name = array();
                        foreach ( $data['slugs'] as $d => $s ) {
                            
                            if ( array_key_exists( $s, $theslugs ) ) {
                                $slugs_name = $theslugs[$s];
                            } else {
                                $bigger = 0;
                                foreach ( $theslugs as $extra => $check ) {
                                    $sim = similar_text( $extra, $s, $perc );
                                    
                                    if ( $perc > $bigger ) {
                                        $slugs_name = $check;
                                        $bigger = $perc;
                                        $data['slugs'][$d] = $extra;
                                    }
                                
                                }
                            }
                        
                        }
                        $title = $slugs_name . ' Gallery';
                    } else {
                        $title = $data['slugs'][0] . ' Gallery';
                    }
                
                }
                
                $return .= $this->outputOrder(
                    $title,
                    $data['slugs'],
                    $data['imgs'],
                    $key,
                    $data,
                    $errors
                );
            }
        }
        return $return;
    }
    
    /**
     * Builds the output order for the variations
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function outputOrder(
        $title,
        $slugs = 'null',
        $attachments = array(),
        $key = 'x',
        $loop_hidden = array(),
        $errors = array()
    )
    {
        $return = '';
        $slug = $slugs;
        if ( is_array( $slugs ) ) {
            $slug = implode( '_svipro_', $slugs );
        }
        $class = '';
        switch ( $title ) {
            case 'Images without assigned variations':
                $class = 'svibadge svibadge-warning';
                break;
        }
        $removegal = '<a href="#/" class="svi-pullright sviprobulk_remove"><span class="dashicons dashicons-trash"></span></a>';
        $unsigned_svi = '';
        
        if ( $slug == 'unsigned_svi' ) {
            $unsigned_svi = $slug;
            $removegal = '';
        }
        
        $title_display = '<span class="svititle ' . $class . '">' . $title . '</span>';
        if ( is_array( $slugs ) ) {
            foreach ( $slugs as $slu => $find ) {
                if ( array_key_exists( $find, $errors ) ) {
                    $return .= '<div class="notice notice-error inline"><p><span class="dashicons dashicons-warning"></span> ' . $errors[$find] . '</p></div>';
                }
            }
        }
        $return .= '<div id="svipro_' . $key . '" class="postbox svi-woocommerce-product-images" data-title="' . $title . '" data-svigal="' . esc_html( $slug ) . '" data-svikey="' . $key . '">';
        $return .= '<h2>' . $title_display . $removegal . ' </h2>';
        $return .= '<div class="inside">';
        $return .= '<div class="svipro-product_images_container">';
        $return .= '<ul class="product_images ui-sortable product_galsort ' . $unsigned_svi . '">';
        $product_image_gallery_svi = '';
        
        if ( count( $attachments ) > 0 ) {
            foreach ( $attachments as $attachment_id ) {
                $attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );
                // if attachment is empty skip
                
                if ( empty($attachment) ) {
                    $update_meta = true;
                    continue;
                }
                
                $return .= '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">' . $attachment . '<ul class="actions"><li><a href="#/" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'woocommerce' ) . '">' . __( 'Delete', 'woocommerce' ) . '</a></li></ul></li>';
            }
            $product_image_gallery_svi = implode( ',', $attachments );
        }
        
        
        if ( $slug != 'unsigned_svi' ) {
            $return .= '<li class="add_product_images_svipro hide-if-no-js ui-state-disabled">';
            $return .= '<a href="#/" data-choose="Add Images to Product Gallery" data-update="Add to gallery" data-delete="Delete image" data-text="Delete"><span class="dashicons dashicons-plus"></span></a>';
            $return .= '</li>';
        }
        
        $return .= '</ul>';
        $loop_hidden = ( array_key_exists( 'loop_hidden', $loop_hidden ) && $loop_hidden['loop_hidden'] ? 'checked' : '' );
        $return .= '<span class="sviHiddenLoop">Hide from <b>Product Loop</b>: (PRO VERSION FEATURE)</span>';
        $return .= '<input class="svipro-product_image_gallery" name="sviproduct_image_gallery[' . esc_html( $slug ) . ']" value="' . $product_image_gallery_svi . '" type="hidden">';
        $return .= '</div>';
        $return .= '</div>';
        $return .= '</div>';
        return $return;
    }
    
    /**
     * Builds the varitions display over AJAX Call
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function reloadSelect_json()
    {
        $pid = $_POST['data'];
        $attributes = get_post_meta( $pid, '_product_attributes' );
        $return = '';
        
        if ( count( $attributes ) < 1 ) {
            $return .= '<div id="message" class="inline notice woocommerce-message">';
            $return .= '<p>Before you can assign images to a variation you need to add some variation attributes on the <strong>Attributes</strong> tab and <b>save the product<b>.</p>';
            $return .= '</div>';
            echo  $return ;
            die;
        }
        
        $return .= "<select id='sviprobulk'>";
        $existing = array();
        foreach ( $attributes[0] as $att => $attribute ) {
            
            if ( $attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                $terms = wp_get_post_terms( $pid, urldecode( $att ), 'all' );
                
                if ( !empty($terms) ) {
                    $tax = get_taxonomy( $att );
                    $return .= '<optgroup label="' . $tax->label . '">';
                    foreach ( $terms as $tr => $term ) {
                        $return .= "<option value='" . esc_attr( $term->slug ) . "'>" . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . "</option>";
                        array_push( $existing, esc_attr( $term->slug ) );
                    }
                    $return .= '</optgroup>';
                }
            
            } elseif ( !$attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                $terms = explode( '|', $attribute['value'] );
                $return .= '<optgroup label="' . $attribute['name'] . '">';
                foreach ( $terms as $tr => $term ) {
                    $return .= "<option value='" . sanitize_title( $term ) . "'>" . esc_html( apply_filters( 'woocommerce_variation_option_name', $term ) ) . "</option>";
                    array_push( $existing, sanitize_title( $term ) );
                }
                $return .= '</optgroup>';
            }
        
        }
        $return .= "</select>";
        //$return .= "</div>";
        echo  $return ;
        die;
    }
    
    /**
     * Builds the varitions display over AJAX Call
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function buildSelect_json()
    {
        $pid = $_POST['data'];
        $attributes = get_post_meta( $pid, '_product_attributes' );
        $return = '';
        
        if ( count( $attributes ) < 1 ) {
            $return .= '<div id="message" class="inline notice woocommerce-message">';
            $return .= '<p>Before you can assign images to a variation you need to add some variation attributes on the <strong>Attributes</strong> tab and <b>save the product<b>.</p>';
            $return .= '</div>';
            echo  $return ;
            die;
        }
        
        $return .= '<div class="wc-metabox-content">';
        $return .= '<table cellspacing="0" cellpadding="0">';
        $return .= '<tr>';
        $return .= ' <td class="attribute_name">';
        $return .= '<strong>Assign Images to : </strong>';
        $return .= '</td>';
        $return .= '<td>';
        $return .= "<div id='sviselect_container'>";
        $return .= "<select id='sviprobulk'>";
        //$return .= "<option value='' selected='selected'>Choose variation to add images</option>";
        $existing = array();
        foreach ( $attributes[0] as $att => $attribute ) {
            
            if ( $attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                $terms = wp_get_post_terms( $pid, urldecode( $att ), 'all' );
                
                if ( !empty($terms) ) {
                    $tax = get_taxonomy( $att );
                    $return .= '<optgroup label="' . $tax->label . '">';
                    foreach ( $terms as $tr => $term ) {
                        $return .= "<option value='" . esc_attr( $term->slug ) . "'>" . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . "</option>";
                        array_push( $existing, urldecode( $term->slug ) );
                    }
                    $return .= '</optgroup>';
                }
            
            } elseif ( !$attribute['is_taxonomy'] && $attribute['is_variation'] ) {
                $terms = explode( '|', $attribute['value'] );
                $return .= '<optgroup label="' . $attribute['name'] . '">';
                foreach ( $terms as $tr => $term ) {
                    $return .= "<option value='" . sanitize_title( $term ) . "'>" . esc_html( apply_filters( 'woocommerce_variation_option_name', $term ) ) . "</option>";
                    array_push( $existing, sanitize_title( $term ) );
                }
                $return .= '</optgroup>';
            }
        
        }
        $return .= "</select>";
        $return .= "</div>";
        $return .= '<button id="addsviprovariation" class="button fr plus">Add</button>';
        $return .= '</td>';
        $return .= '</tr>';
        $return .= '<tr><td colspan="2">
            <b>(PRO VERSION FEATURES)</b><br><br>
        <b><small><span class="svibadge svibadge-warning">Default Gallery</span> Create a default gallery to be displayed. All other images/galleries will be hidden until matching occours.</small></b><br>
        <b><small><span class="svibadge svibadge-warning">SVI Global</span> Use this variation to assign global images to be showed in all variations.</small></b><br><br>
        <b><small><span class="svibadge svibadge-success">Multiple Attributes</span> Build galleries with more than 1 Attribute, ex: Size + Color.</small></b><br>
        <b><small><span class="svibadge svibadge-success">Sortable/draggable</span> Sort images into the correct position or drag and drop image between galleries.</small></b></td></tr>';
        $return .= '</table>';
        $return .= '</div>';
        $return .= '<div class="clear"></div>';
        $return .= '<div id="svigallery">';
        $return .= $this->output( $pid, $existing );
        $return .= '</div>';
        $return .= '<div id="svipro_clone" class="postbox svi-woocommerce-product-images hidden" data-svigal="">';
        $return .= '<h2><span class="svititle">Product Gallery</span><a href="#/" class="svi-pullright sviprobulk_remove"><span class="dashicons dashicons-trash"></span></a></h2>';
        $return .= '<div class="inside">';
        $return .= '<div class="svipro-product_images_container">';
        $return .= '<ul class="product_images ui-sortable product_galsort">';
        $return .= '<li class="add_product_images_svipro hide-if-no-js ui-state-disabled">';
        $return .= '<a href="#/" data-choose="Add Images to Product Gallery" data-update="Add to gallery" data-delete="Delete image" data-text="Delete"><span class="dashicons dashicons-plus"></span></a>';
        $return .= '</li>';
        $return .= '</ul>';
        $return .= '<span class="sviHiddenLoop">Hide from <b>Product Loop</b>: (PRO VERSION FEATURE)</span>';
        $return .= '<input class="svipro-product_image_gallery" name="" value="" type="hidden">';
        $return .= '</div>';
        $return .= '</div>';
        $return .= '</div>';
        echo  $return ;
        die;
    }
    
    /**
     * Saves the variation information on Save
     *
     *
     * @since 1.0.0
     * @return HTML
     */
    public function save_woosvibulk_meta( $post_id )
    {
        $post_type = get_post_type( $post_id );
        if ( "product" != $post_type ) {
            return;
        }
        $attachment_ids = ( isset( $_POST['product_image_gallery'] ) ? array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) ) : array() );
        if ( empty($attachment_ids) ) {
            delete_post_meta( $post_id, 'woosvi_slug' );
        }
        foreach ( $attachment_ids as $key => $value ) {
            delete_post_meta( $value, 'woosvi_slug' );
            //delete_post_meta($value, 'woosvi_slug_' . $post_id);
        }
        if ( !isset( $_POST['sviproduct_image_gallery'] ) ) {
            return;
        }
        $bulk = $_POST['sviproduct_image_gallery'];
        $keys = array();
        if ( array_key_exists( 'nullsvi', $bulk ) ) {
            if ( $bulk['nullsvi'] ) {
                foreach ( $bulk['nullsvi'] as $value ) {
                    $ids = explode( ',', wc_clean( $value ) );
                    foreach ( $ids as $id ) {
                        delete_post_meta( $id, 'woosvi_slug' );
                        //delete_post_meta($id, 'woosvi_slug_' . $post_id);mas
                    }
                }
            }
        }
        $ordered = array();
        if ( array_key_exists( 'nullsvi', $bulk ) ) {
            unset( $bulk['nullsvi'] );
        }
        if ( array_key_exists( 'unsigned_svi', $bulk ) ) {
            unset( $bulk['unsigned_svi'] );
        }
        foreach ( $bulk as $k => $v ) {
            
            if ( !empty($v) ) {
                $arr = array(
                    'slugs' => explode( '_svipro_', $k ),
                    'imgs'  => explode( ',', $v ),
                );
                array_push( $ordered, $arr );
            }
        
        }
        $product = wc_get_product( $post_id );
        $product->update_meta_data( 'woosvi_slug', $ordered );
        $product->save();
    }
    
    public function action_woocommerce_product_options_advanced()
    {
        global  $woocommerce, $post ;
        echo  '<div class="options_group">' ;
        // Checkbox
        woocommerce_wp_checkbox( array(
            'id'          => '_checkbox_svipro_enabled',
            'label'       => __( 'Disable SVI', 'wc_svi' ),
            'description' => __( 'Activating this option will make the product load the default theme image gallery and functions', 'wc_svi' ),
        ) );
        echo  '</div>' ;
    }
    
    public function woo_add_custom_general_fields_save( $post_id )
    {
        // Checkbox
        $woocommerce_checkbox = ( isset( $_POST['_checkbox_svipro_enabled'] ) ? 'yes' : 'no' );
        update_post_meta( $post_id, '_checkbox_svipro_enabled', $woocommerce_checkbox );
        $this->save_woosvibulk_meta( $post_id );
    }
    
    public function getProductGallery( $pid, $returnUrl = false )
    {
        $product_image_gallery = array();
        
        if ( metadata_exists( 'post', $pid, '_product_image_gallery' ) ) {
            $product_image_gallery = explode( ',', get_post_meta( $pid, '_product_image_gallery', true ) );
        } else {
            // Backwards compat
            $attachment_ids = get_posts( 'post_parent=' . $pid . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
            $attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
            if ( $attachment_ids ) {
                $product_image_gallery = explode( ',', $attachment_ids );
            }
        }
        
        if ( count( $product_image_gallery ) < 1 ) {
            return false;
        }
        $product_image_gallery = array_filter( $product_image_gallery );
        if ( $returnUrl ) {
            foreach ( $product_image_gallery as $k => $v ) {
                $product_image_gallery[$k] = array(
                    'id'  => $v,
                    'url' => array_pop( explode( '/', wp_get_attachment_url( $v ) ) ),
                );
            }
        }
        return $product_image_gallery;
    }
    
    public function woo_handle_export(
        $value,
        $meta,
        $product,
        $row
    )
    {
        
        if ( $meta->key == 'woosvi_slug' ) {
            foreach ( $value as $k => $v ) {
                foreach ( $v['imgs'] as $k2 => $v2 ) {
                    $value[$k]['imgs'][$k2] = array(
                        'id'  => $v2,
                        'url' => array_pop( explode( '/', wp_get_attachment_url( $v2 ) ) ),
                    );
                }
            }
            return json_encode( $value );
        }
    
    }

}