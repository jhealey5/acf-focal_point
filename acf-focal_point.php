<?php

/*
Plugin Name: ACF: Focal Point
Plugin URI: https://github.com/evu/acf-focal_point
Description: Adds a new field type to Advanced Custom Fields allowing users to draw focal points on images.
Version: 1.5
Author: John Healey / Shane Warner
Author URI: http://twitter.com/lostinnovation
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


// Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_focal_point( $version ) {
	
	include_once('acf-focal_point-v5.php');
}
add_action('acf/include_field_types', 'include_field_types_focal_point');	




// Include field type for ACF4
function register_fields_focal_point() {
	
	include_once('acf-focal_point-v4.php');
}
add_action('acf/register_fields', 'register_fields_focal_point');	
	
?>