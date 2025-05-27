<?php
function mytheme_enqueue_styles() {
  wp_enqueue_style('my-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_styles');

function mytheme_setup() {
  register_nav_menu('main-menu', 'Main Menu');
}
add_action('after_setup_theme', 'mytheme_setup');


  