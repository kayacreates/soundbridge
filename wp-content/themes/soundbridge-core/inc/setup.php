<?php
function soundbridge_setup() {
  add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('align-wide');
  add_theme_support('custom-logo', ['height'=>96,'width'=>275,'flex-height'=>true,'flex-width'=>true]);
  add_theme_support('editor-styles'); add_editor_style(['assets/css/site.css','assets/css/core-blocks.css']);
  register_nav_menus([
    'primary'=>'Primary Navigation',
    'footer'=>'Footer Navigation (Legacy)',
    'footer_programs'=>'Footer: Programs',
    'footer_about'=>'Footer: About',
    'footer_involved'=>'Footer: Get Involved',
    'footer_explore'=>'Footer: Explore',
    'footer_legal'=>'Footer: Legal',
  ]);
}
add_action('after_setup_theme','soundbridge_setup');

/** Footer content and integration settings. */
function soundbridge_customize_footer($customizer){
  $customizer->add_section('soundbridge_footer',['title'=>'Footer','priority'=>130]);
  $footer_uploads=wp_upload_dir();
  $customizer->add_setting('soundbridge_footer_logo',['default'=>trailingslashit($footer_uploads['baseurl']).'2026/09/sblogo_white.png','sanitize_callback'=>'esc_url_raw']);
  $customizer->add_control(new WP_Customize_Image_Control($customizer,'soundbridge_footer_logo',[
    'section'=>'soundbridge_footer',
    'label'=>'Footer logo',
  ]));
  $fields=[
    'soundbridge_footer_description'=>['Brand description','textarea'],
    'soundbridge_footer_newsletter_heading'=>['Newsletter heading','text'],
    'soundbridge_footer_newsletter_description'=>['Newsletter description','textarea'],
    'soundbridge_facebook_url'=>['Facebook URL','url'],
    'soundbridge_instagram_url'=>['Instagram URL','url'],
    'soundbridge_youtube_url'=>['YouTube URL','url'],
    'soundbridge_linkedin_url'=>['LinkedIn URL','url'],
  ];
  foreach($fields as $setting=>$details){
    $sanitize=$details[1]==='url'?'esc_url_raw':($details[1]==='textarea'?'sanitize_textarea_field':'sanitize_text_field');
    $customizer->add_setting($setting,['sanitize_callback'=>$sanitize]);
    $customizer->add_control($setting,['section'=>'soundbridge_footer','label'=>$details[0],'type'=>$details[1]]);
  }
  $form_choices=[0=>'Use contact link'];
  if(class_exists('\\FluentForm\\App\\Models\\Form')){
    foreach(\FluentForm\App\Models\Form::select(['id','title'])->where('status','published')->orderBy('title','ASC')->get() as $form){
      $form_choices[(int)$form->id]=$form->title;
    }
  }
  $customizer->add_setting('soundbridge_footer_newsletter_form_id',['default'=>0,'sanitize_callback'=>'absint']);
  $customizer->add_control('soundbridge_footer_newsletter_form_id',['section'=>'soundbridge_footer','label'=>'Newsletter form','type'=>'select','choices'=>$form_choices]);
}
add_action('customize_register','soundbridge_customize_footer');
function soundbridge_assets(){
  wp_enqueue_style('soundbridge-fonts','https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,400;1,9..144,600&family=Inter:wght@400;500;600;700&display=swap',[],null);
  wp_enqueue_style('soundbridge-site',get_template_directory_uri().'/assets/css/site.css',[],wp_get_theme()->get('Version'));
  $core_blocks_stylesheet=get_template_directory().'/assets/css/core-blocks.css';
  wp_enqueue_style('soundbridge-core-blocks',get_template_directory_uri().'/assets/css/core-blocks.css',['soundbridge-site'],filemtime($core_blocks_stylesheet));
  if (is_404()) {
    $not_found_stylesheet = get_template_directory() . '/assets/css/404.css';
    wp_enqueue_style('soundbridge-not-found',get_template_directory_uri().'/assets/css/404.css',['soundbridge-site'],filemtime($not_found_stylesheet));
  }
  if (is_singular('program')) {
    $single_stylesheet = get_template_directory() . '/assets/css/single.css';
    wp_enqueue_style('soundbridge-single-program',get_template_directory_uri().'/assets/css/single.css',['soundbridge-site'],filemtime($single_stylesheet));
    $single_script = get_template_directory() . '/assets/js/single.js';
    wp_enqueue_script('soundbridge-single-program',get_template_directory_uri().'/assets/js/single.js',[],filemtime($single_script),true);
  }
  if (is_post_type_archive('faculty') || is_singular('faculty')) {
    $faculty_stylesheet = get_template_directory() . '/assets/css/faculty.css';
    wp_enqueue_style('soundbridge-faculty',get_template_directory_uri().'/assets/css/faculty.css',['soundbridge-site'],filemtime($faculty_stylesheet));
  }
  if (is_singular('event')) {
    $event_single_stylesheet = get_template_directory() . '/assets/css/single-event.css';
    wp_enqueue_style('soundbridge-single-event',get_template_directory_uri().'/assets/css/single-event.css',['soundbridge-site'],filemtime($event_single_stylesheet));
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
    $archive_script = get_template_directory() . '/assets/js/archive-program.js';
    wp_enqueue_script('soundbridge-program-archive',get_template_directory_uri().'/assets/js/archive-program.js',[],filemtime($archive_script),true);
  }
  if (is_post_type_archive('directory')) {
    $directory_stylesheet = get_template_directory() . '/assets/css/archive-directory.css';
    $directory_script = get_template_directory() . '/assets/js/archive-directory.js';
    wp_enqueue_style('soundbridge-directory-archive',get_template_directory_uri().'/assets/css/archive-directory.css',['soundbridge-site'],filemtime($directory_stylesheet));
    wp_enqueue_script('soundbridge-directory-archive',get_template_directory_uri().'/assets/js/archive-directory.js',[],filemtime($directory_script),true);
  }
  if (is_post_type_archive('event')) {
    $event_stylesheet = get_template_directory() . '/assets/css/archive-event.css';
    $event_script = get_template_directory() . '/assets/js/archive-event.js';
    wp_enqueue_style('soundbridge-event-archive',get_template_directory_uri().'/assets/css/archive-event.css',['soundbridge-site'],filemtime($event_stylesheet));
    wp_enqueue_script('soundbridge-event-archive',get_template_directory_uri().'/assets/js/archive-event.js',[],filemtime($event_script),true);
  }
  wp_enqueue_script('soundbridge-site',get_template_directory_uri().'/assets/js/site.js',[],wp_get_theme()->get('Version'),true);
}
add_action('wp_enqueue_scripts','soundbridge_assets');
