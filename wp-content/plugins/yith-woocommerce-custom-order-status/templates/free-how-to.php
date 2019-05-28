<style>
    .yith-wccos-how-to-content {
        background : #fff;
        text-align : center;
    }

    .yith-wccos-how-to-content ul {
        list-style : none;
    }

    .yith-wccos-how-to-content ul li {
        padding : 20px;
    }

    .yith-wccos-how-to-content ul li:nth-child(even) {
        background : #f9f9f9;
    }

    .yith-wccos-how-to-content * {
        -webkit-transition : all .3s ease;
        -o-transition      : all .3s ease;
        transition         : all .3s ease;
    }

    .yith-wccos-how-to-content p {
        margin : 0;
    }

    .yith-wccos-how-to-content img {
        width      : 90%;
        max-width  : 700px;
        box-shadow : 0 2px 10px 0 #ccc;
        border     : 1px solid #ccc;
        margin     : 20px 0 10px 0;
    }

    .yith-wccos-how-to-content img:hover {
        transform : scale(1.1);
    }

    .yith-wccos-how-to-content a.yith-doc-button {
        background-color : #93ab07;
        color            : #ffffff;
        font-weight      : 700;
        text-transform   : uppercase;
        text-align       : center;
        padding          : 10px 36px;
        border-radius    : 6px;
        margin-top       : 10px;
        box-sizing       : border-box;
        display          : inline-block;
        text-decoration  : none;
    }

    .yith-wccos-how-to-content a.yith-doc-button:hover {
        background : #b8d223;
    }

</style>

<div class="yith-wccos-how-to-content">
    <ul>
        <li>
            <p>
                <?php echo __( 'In this page of the documentation you can find the procedure on how adding a new order status, and the description of the possible operations available with the free version of the plugin.', 'yith-woocommerce-custom-order-status' ) ?>
                <br/>
                <br/>
                <?php
                $order_status_url     = add_query_arg( array( 'post_type' => 'yith-wccos-ostatus' ), admin_url( 'edit.php' ) );
                $new_order_status_url = add_query_arg( array( 'post_type' => 'yith-wccos-ostatus' ), admin_url( 'post-new.php' ) );
                ?>
                <?php echo sprintf( __( 'Firstly, go to the %1$sOrder Status%2$s section and click on %3$sAdd Order Status%4$s.', 'yith-woocommerce-custom-order-status' ),
                                    "<strong><a href='$order_status_url'>",
                                    "</a></strong>",
                                    "<strong><a href='$new_order_status_url'>",
                                    "</a></strong>" ) ?>
            </p>
            <img src="<?php echo YITH_WCCOS_ASSETS_URL ?>/images/how-to/01.jpg" alt="bundle-01"/>
        </li>
        <li>
            <p>
                <?php echo __( 'Add the name of the new status and choose a color to identify it.', 'yith-woocommerce-custom-order-status' ); ?>
            </p>
            <img src="<?php echo YITH_WCCOS_ASSETS_URL ?>/images/how-to/02.jpg" alt="bundle-02"/>
        </li>
        <li>
            <p>
                <?php _e( 'If you create five different order status, these will be immediately available for each order of your shop.', 'yith-woocommerce-custom-order-status' ) ?>
            </p>
            <img src="<?php echo YITH_WCCOS_ASSETS_URL ?>/images/how-to/03.jpg" alt="bundle-03"/>
        </li>
        <li>
            <p>
                <?php _e( 'As for any other WooCommerce status, now click on one of the created status to apply it to the selected order.', 'yith-woocommerce-custom-order-status' ) ?>
            </p>
            <img src="<?php echo YITH_WCCOS_ASSETS_URL ?>/images/how-to/04.jpg" alt="bundle-04"/>
        </li>
        <li>
            <p>
                <a class="yith-doc-button" href="//yithemes.com/docs-plugins/yith-woocommerce-custom-order-status/"><?php _e( 'View plugin documentation', 'yith-woocommerce-custom-order-status' ) ?></a>
            </p>
        </li>
    </ul>
</div>