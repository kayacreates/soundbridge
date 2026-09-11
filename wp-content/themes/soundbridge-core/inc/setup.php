<?php
function soundbridge_setup() {
  add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('align-wide');
  add_theme_support('editor-styles'); add_editor_style('assets/css/site.css');
  register_nav_menus(['primary'=>'Primary Navigation','footer'=>'Footer Navigation']);
}
add_action('after_setup_theme','soundbridge_setup');
function soundbridge_assets(){
  wp_enqueue_style('soundbridge-fonts','https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,400;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap',[],null);
  wp_enqueue_style('soundbridge-site',get_template_directory_uri().'/assets/css/site.css',[],wp_get_theme()->get('Version'));
  if (is_singular('program')) {
    $single_stylesheet = get_template_directory() . '/assets/css/single.css';
    wp_enqueue_style('soundbridge-single-program',get_template_directory_uri().'/assets/css/single.css',['soundbridge-site'],filemtime($single_stylesheet));
    $single_script = get_template_directory() . '/assets/js/single.js';
    wp_enqueue_script('soundbridge-single-program',get_template_directory_uri().'/assets/js/single.js',[],filemtime($single_script),true);
  }
  if (is_post_type_archive('program')) {
    $program_grid_stylesheet = WP_PLUGIN_DIR . '/soundbridge-blocks/build/program-grid/style-index.css';
    $archive_stylesheet = get_template_directory() . '/assets/css/archive-program.css';
    $archive_dependencies = ['soundbridge-site'];
    if (file_exists($program_grid_stylesheet)) {
      wp_enqueue_style('soundbridge-program-grid',plugins_url('build/program-grid/style-index.css', WP_PLUGIN_DIR . '/soundbridge-blocks/soundbridge-blocks.php'),['soundbridge-site'],filemtime($program_grid_stylesheet));
      $archive_dependencies[] = 'soundbridge-program-grid';
    }
    wp_enqueue_style('soundbridge-program-archive',get_template_directory_uri().'/assets/css/archive-program.css',$archive_dependencies,filemtime($archive_stylesheet));
  }
  if (is_post_type_archive('directory')) {
    $directory_stylesheet = get_template_directory() . '/assets/css/archive-directory.css';
    $directory_script = get_template_directory() . '/assets/js/archive-directory.js';
    wp_enqueue_style('soundbridge-directory-archive',get_template_directory_uri().'/assets/css/archive-directory.css',['soundbridge-site'],filemtime($directory_stylesheet));
    wp_enqueue_script('soundbridge-directory-archive',get_template_directory_uri().'/assets/js/archive-directory.js',[],filemtime($directory_script),true);
  }
  wp_enqueue_script('soundbridge-site',get_template_directory_uri().'/assets/js/site.js',[],wp_get_theme()->get('Version'),true);
}
add_action('wp_enqueue_scripts','soundbridge_assets');
