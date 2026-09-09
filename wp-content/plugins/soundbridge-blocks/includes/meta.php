<?php
function soundbridge_register_meta(){
 $fields=[
  'program'=>['tagline','age','level','instrument','schedule','session_length','location','location_detail','cost','cost_detail','status','status_label','registration_url'],
  'event'=>['event_date','event_time','location','cost','audience','registration_url'],
  'directory'=>['specialty','location','contact','instrument']
 ];
 foreach($fields as $type=>$keys) foreach($keys as $key) register_post_meta($type,'sb_'.$key,[
   'show_in_rest'=>true,'single'=>true,'type'=>'string','sanitize_callback'=>'sanitize_text_field','auth_callback'=>fn()=>current_user_can('edit_posts')
 ]);
}
add_action('init','soundbridge_register_meta');
