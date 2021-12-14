<?php
function enqueue_custom_style() {
    wp_register_style( 'custom_wp_css', plugin_dir_url( __FILE__ ) . './css/style.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_css' );
  }
  add_action( 'admin_enqueue_scripts', 'enqueue_custom_style' ); 