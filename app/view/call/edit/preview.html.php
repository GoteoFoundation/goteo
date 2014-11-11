<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Model;

$call = $this['call'];

$finishable = true;

// miramos el pruimer paso con errores para mandarlo a ese
$goto = 'view-step-userProfile';
foreach ($this['steps'] as $id => $data) {
    if (empty($step) && !empty($call->errors[$id])) {
        $goto = 'view-step-' . $id;
        $finishable = false;
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
if ($finishable) {
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

    'splash' => array(
        'type'      => 'html',
        'class'     => 'fullwidth',
        'html'      =>  '<table><tr><td>'
                        . '<a href="/call/'.$call->id.'/?preview=apply" class="button" target="_blank">'.Text::get('call-see_apply-button').'</a>'
                        . '</td><td>'
                        . '<a href="/call/'.$call->id.'/?preview=campaign" class="button" target="_blank">'.Text::get('call-see_campaign-button').'</a>'
                        . '</td></tr><tr><td>'
                        . '<a href="/dashboard/calls" class="button">'.Text::get('call-go_dashboard-button').'</a>'
                        . '</td></tr></table>'
    )
);

// Footer
$elements['footer'] = array(
    'type'      => 'group',
    'children'  => array(
        'errors' => array(
            'title' => Text::get('form-footer-errors_title'),
            'view'  => new View('view/project/edit/errors.html.php', array(
                'project'   => $call,
                'step'      => $this['step']
            ))                    
        ),
        'buttons'  => array(
            'type'  => 'group',
            'children' => $buttons
        )
    )

);

// lanzamos el superform
echo new SuperForm(array(
    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('preview-main-header'),
    'hint'          => Text::get('guide-call-preview'),
    'elements'      => $elements
));