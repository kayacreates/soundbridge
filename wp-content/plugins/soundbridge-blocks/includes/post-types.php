<?php
function soundbridge_register_post_types(){
  $types=[
    'program'=>['Programs','Program','dashicons-groups'],
    'event'=>['Events','Event','dashicons-calendar-alt'],
    'directory'=>['Music Directory','Directory Listing','dashicons-admin-users'],
  ];
  foreach($types as $slug=>$v){ register_post_type($slug,[
    'labels'=>['name'=>$v[0],'singular_name'=>$v[1],'add_new_item'=>'Add New '.$v[1],'edit_item'=>'Edit '.$v[1]],
    'public'=>true,'show_in_rest'=>true,'show_in_nav_menus'=>true,'menu_icon'=>$v[2],'has_archive'=>true,
    'hierarchical'=>$slug==='program',
    'rewrite'=>['slug'=>$slug==='directory'?'music-directory':$slug.'s'],
    'supports'=>$slug==='program'
      ? ['title','excerpt','thumbnail','revisions','page-attributes']
      : (in_array($slug, ['directory', 'event'], true)
        ? ['title','thumbnail','revisions']
        : ['title','editor','excerpt','thumbnail','revisions','custom-fields'])
  ]); }
  register_taxonomy('program_type','program',['label'=>'Program Types','public'=>true,'show_in_rest'=>true,'hierarchical'=>true]);
  register_taxonomy('program_age','program',['labels'=>['name'=>'Program Ages','singular_name'=>'Program Age'],'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'program-age']]);
  register_taxonomy('program_level','program',['labels'=>['name'=>'Program Levels','singular_name'=>'Program Level'],'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'program-level']]);
  register_taxonomy('program_instrument','program',['labels'=>['name'=>'Program Instruments','singular_name'=>'Program Instrument'],'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'show_admin_column'=>true,'rewrite'=>['slug'=>'program-instrument']]);
  register_taxonomy('event_type','event',['label'=>'Event Types','public'=>true,'show_in_rest'=>true,'hierarchical'=>true]);
  register_taxonomy('directory_category','directory',['label'=>'Directory Categories','public'=>true,'show_in_rest'=>true,'hierarchical'=>true]);
}
add_action('init','soundbridge_register_post_types');

/** Ensure the standard Program Age choices exist. */
function soundbridge_seed_program_age_terms(){
  if(get_option('soundbridge_program_age_terms_seeded_v1')) return;
  foreach(array_merge(range(5,18),['Adult']) as $age){
    if(!term_exists((string)$age,'program_age')) wp_insert_term((string)$age,'program_age');
  }
  update_option('soundbridge_program_age_terms_seeded_v1',1,false);
}
add_action('init','soundbridge_seed_program_age_terms',20);

/** Sort Program Age term objects numerically, with Adult at the end. */
function soundbridge_sort_program_age_terms($terms,$taxonomies){
  if(!in_array('program_age',(array)$taxonomies,true) || !is_array($terms)) return $terms;
  foreach($terms as $term) if(!($term instanceof WP_Term)) return $terms;
  usort($terms,static function($left,$right){
    $rank=static function($term){
      if(in_array(strtolower(trim($term->name)),['adult','adults'],true)) return PHP_INT_MAX;
      return preg_match('/\d+/', $term->name, $match) ? (int)$match[0] : PHP_INT_MAX-1;
    };
    $comparison=$rank($left)<=>$rank($right);
    return $comparison ?: strnatcasecmp($left->name,$right->name);
  });
  return $terms;
}
add_filter('get_terms','soundbridge_sort_program_age_terms',10,2);

/** Return Program meta, inheriting selected shared fields from a parent Program. */
function soundbridge_get_program_meta($program_id,$key){
  $value=get_post_meta($program_id,'sb_'.$key,true);
  $inheritable=['schedule','session_length','location','location_detail','question_url'];
  if((''===$value || null===$value) && in_array($key,$inheritable,true)){
    $parent_id=wp_get_post_parent_id($program_id);
    if($parent_id) $value=get_post_meta($parent_id,'sb_'.$key,true);
  }
  return $value;
}

/** Return Program taxonomy names, inheriting them from the parent when unset. */
function soundbridge_get_program_term_names($program_id,$taxonomy){
  $names=wp_get_post_terms($program_id,$taxonomy,['fields'=>'names']);
  if((is_wp_error($names) || !$names) && ($parent_id=wp_get_post_parent_id($program_id))){
    $names=wp_get_post_terms($parent_id,$taxonomy,['fields'=>'names']);
  }
  return is_wp_error($names) ? [] : $names;
}

/** Programs, events, and directory listings use structured fields instead of the block editor. */
function soundbridge_disable_structured_post_block_editor($use_block_editor, $post_type){
  return in_array($post_type, ['program', 'event', 'directory'], true) ? false : $use_block_editor;
}
add_filter('use_block_editor_for_post_type','soundbridge_disable_structured_post_block_editor',10,2);

/** Return a consistent age range label from Program age terms or legacy meta. */
function soundbridge_get_program_age_label($program_id){
  $ages=soundbridge_get_program_term_names($program_id,'program_age');
  $source=!is_wp_error($ages) && $ages ? implode(' ',$ages) : '';
  if(!$source) return '';
  $includes_adult=(bool) preg_match('/\badults?\b/i',$source);
  preg_match_all('/\d+(?:\.\d+)?/',$source,$matches);
  if(!$matches[0]) return $includes_adult ? 'Adult' : $source;
  $numbers=array_map('floatval',$matches[0]);
  $lowest=min($numbers);
  $highest=max($numbers);
  $format=static fn($age)=>(float)(int)$age===(float)$age ? (string)(int)$age : rtrim(rtrim(number_format($age,2,'.',''),'0'),'.');
  if($includes_adult) return 'Ages '.$format($lowest).'–Adult';
  if($lowest===$highest) return 'Age '.$format($lowest);
  return 'Ages '.$format($lowest).'–'.$format($highest);
}

/** Return Program levels as an ordered range. */
function soundbridge_get_program_level_label($program_id){
  $levels=soundbridge_get_program_term_names($program_id,'program_level');
  if(is_wp_error($levels) || !$levels) return '';
  $order=['beginner'=>0,'intermediate'=>1,'advanced'=>2];
  $selected=[];
  $other=[];
  foreach($levels as $level){
    $key=strtolower(trim($level));
    if(in_array($key,['all levels','all level'],true)) return 'All Levels';
    if(array_key_exists($key,$order)) $selected[$key]=$order[$key];
    else $other[]=$level;
  }
  if(count($selected)===3) return 'All Levels';
  if(!$selected) return implode(', ',$other);
  asort($selected);
  $names=array_keys($selected);
  $labels=['beginner'=>'Beginner','intermediate'=>'Intermediate','advanced'=>'Advanced'];
  $lowest=$labels[$names[0]];
  $highest=$labels[$names[count($names)-1]];
  return $lowest===$highest ? $lowest : $lowest.' to '.$highest;
}

/** Move legacy Program filter meta into the matching taxonomies once. */
function soundbridge_migrate_program_filter_taxonomies(){
  if(get_option('soundbridge_program_taxonomies_migrated_v2')) return;
  $program_ids=get_posts(['post_type'=>'program','post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids']);
  $taxonomies=['age'=>'program_age','level'=>'program_level','instrument'=>'program_instrument'];
  foreach($program_ids as $program_id){
    foreach($taxonomies as $meta_key=>$taxonomy){
      $value=trim((string)get_post_meta($program_id,'sb_'.$meta_key,true));
      if(!$value) continue;
      $migrated=has_term('',$taxonomy,$program_id) ? true : wp_set_object_terms($program_id,[$value],$taxonomy);
      if(!is_wp_error($migrated)) delete_post_meta($program_id,'sb_'.$meta_key);
    }
  }
  flush_rewrite_rules(false);
  update_option('soundbridge_program_taxonomies_migrated_v2',1,false);
}
add_action('admin_init','soundbridge_migrate_program_filter_taxonomies');
