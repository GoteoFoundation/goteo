<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $vars['project'];
$types   = $vars['types'];
$errors = $project->errors ?: array();

// miramos el pruimer paso con errores para mandarlo a ese
$goto = 'view-step-userProfile';
foreach ($vars['steps'] as $id => $data) {

    if (empty($step) && !empty($project->errors[$id])) {
        $goto = 'view-step-' . $id;
        break;
    }
}

// boton de revisar que no sirve para mucho
$buttons = array(
    'review' => array(
        'type'  => 'submit',
        'name'  => $goto,
        'label' => Text::get('form-self_review-button'),
        'class' => 'retry'
    )
);

// si es enviable ponemos el boton
if ($project->finishable) {
    $buttons['finish'] = array(
        'type'  => 'submit',
        'name'  => 'finish',
        'label' => Text::get('form-send_review-button'),
        'class' => 'confirm red'
    );
} else {
    $buttons['nofinish'] = array(
        'type'  => 'submit',
        'name'  => 'nofinish',
        'label' => Text::get('form-send_review-button'),
        'class' => 'confirm disabled',
        'disabled' => 'disabled'
    );
}

// elementos generales de preview
$elements      = array(
    'process_preview' => array (
        'type' => 'hidden',
        'value' => 'preview'
    ),

    'anchor-preview' => array(
        'type' => 'html',
        'html' => '<a name="preview"></a>'
    ),
    'preview' => array(
        'type'      => 'html',
        'class'     => 'fullwidth',
        'html'      =>  '<div class="contents"><a style="font-size:16px;" target="_blank" class="button aqua" href="/project/'.$project->id.'">'.Text::get('project-view-preview').'</a></div>'
    )
);

// si es enviable ponemos el campo de comentario
if ($project->finishable) {
    $elements['comment'] = array(
            'type'  =>'textarea',
            'title' => Text::get('preview-send-comment'),
            'rows'  => 8,
            'cols'  => 100,
            'hint'  => Text::get('tooltip-project-comment'),
            'value' => $project->comment
        );
}

// Footer, solo si sigue en edición
if ($project->status == 1) {
    $elements['footer'] = array(
        'type'      => 'group',
        'children'  => array(
            'errors' => array(
                'title' => Text::get('form-footer-errors_title'),
                'view'  => new View('project/edit/errors.html.php', array(
                        'project'   => $project,
                        'step'      => $vars['step']
                    ))
            ),
            'buttons'  => array(
                'type'  => 'group',
                'children' => $buttons
            )
        )

    );
}

// lanzamos el superform
echo SuperForm::get(array(
    'action'        => '',
    'level'         => $vars['level'],
    'method'        => 'post',
    'title'         => Text::get('preview-main-header'),
    'hint'          => Text::get('guide-project-preview'),
    'elements'      => $elements
));
