<?php
/** Fluent Forms integration used by the Contact block. */

function soundbridge_get_fluent_forms(): array
{
    if (!class_exists('\FluentForm\App\Models\Form')) return array();
    $forms = \FluentForm\App\Models\Form::select(array('id', 'title'))->where('status', 'published')->orderBy('title', 'ASC')->get();
    return array_map(static fn($form) => array('id' => (int) $form->id, 'title' => (string) $form->title), $forms->all());
}

function soundbridge_register_fluent_forms_route(): void
{
    register_rest_route('soundbridge/v1', '/fluent-forms', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => static fn() => rest_ensure_response(soundbridge_get_fluent_forms()),
        'permission_callback' => static fn() => current_user_can('edit_posts'),
    ));
}
add_action('rest_api_init', 'soundbridge_register_fluent_forms_route');
