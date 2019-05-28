<?php

// File Security Check.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function the7pt_set_db_version_1_11_0() {
	The7PT_Install::update_db_version( '1.11.0' );
}

function the7pt_set_db_version_1_13_0() {
	The7PT_Install::update_db_version( '1.13.0' );
}
