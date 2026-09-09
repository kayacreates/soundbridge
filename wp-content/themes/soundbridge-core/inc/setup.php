<?php
function soundbridge_setup() {
  add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('align-wide');
  add_theme_support('editor-styles'); add_editor_style('assets/css/site.css');
  register_nav_menus(['primary'=>'Primary Navigation','footer'=>'Footer Navigation']);
}
add_action('after_setup_theme','soundbridge_setup');
function soundbridge_assets(){
  wp_enqueue_style('soundbridge-fonts','https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600;700&display=swap',[],null);
  wp_enqueue_style('soundbridge-site',get_template_directory_uri().'/assets/css/site.css',[],wp_get_theme()->get('Version'));
  wp_enqueue_script('soundbridge-site',get_template_directory_uri().'/assets/js/site.js',[],wp_get_theme()->get('Version'),true);
}
add_action('wp_enqueue_scripts','soundbridge_assets');
