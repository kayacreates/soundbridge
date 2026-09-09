<?php
function soundbridge_register_post_types(){
  $types=[
    'program'=>['Programs','Program','dashicons-groups'],
    'event'=>['Events','Event','dashicons-calendar-alt'],
    'directory'=>['Music Directory','Directory Listing','dashicons-admin-users'],
  ];
  foreach($types as $slug=>$v){ register_post_type($slug,[
    'labels'=>['name'=>$v[0],'singular_name'=>$v[1],'add_new_item'=>'Add New '.$v[1],'edit_item'=>'Edit '.$v[1]],
    'public'=>true,'show_in_rest'=>true,'menu_icon'=>$v[2],'has_archive'=>true,
    'rewrite'=>['slug'=>$slug==='directory'?'music-directory':$slug.'s'],
    'supports'=>['title','editor','excerpt','thumbnail','revisions','custom-fields']
  ]); }
  register_taxonomy('program_type','program',['label'=>'Program Types','public'=>true,'show_in_rest'=>true,'hierarchical'=>true]);
  register_taxonomy('event_type','event',['label'=>'Event Types','public'=>true,'show_in_rest'=>true,'hierarchical'=>true]);
  register_taxonomy('directory_category','directory',['label'=>'Directory Categories','public'=>true,'show_in_rest'=>true,'hierarchical'=>true]);
}
add_action('init','soundbridge_register_post_types');
