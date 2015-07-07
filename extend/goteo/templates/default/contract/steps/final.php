<?php

$this->layout('contract/edit');

$contract = $this->contract;
$step = $this->step;
$steps = $this->steps;

$errors = $contract->errors ?: array();

// miramos el pruimer paso con errores para mandarlo a ese
$goto = 'promoter';
foreach($errors as $id => $err)  {
    if($err) {
        $goto = $id;
        break;
    }
}
// boton de revisar sirve para volver al principio del formulario
$buttons = array(
    'review' => array(
        'type'  => 'Button',
        'buttontype'  => 'submit',
        'name'  => 'step',
        'value'  => $goto,
        'label' => $this->text('form-self_review-button'),
        'class' => 'retry'
    )
);

// si es enviable ponemos el boton
if ($contract->finishable) {
    $buttons['finish'] = array(
        'type'  => 'submit',
        'name'  => 'finish',
        'label' => 'Cerrar datos',
        'class' => 'confirm red'
    );
} else {
    $buttons['nofinish'] = array(
        'type'  => 'submit',
        'name'  => 'nofinish',
        'label' => 'Cerrar datos',
        'class' => 'confirm disabled',
        'disabled' => 'disabled'
    );
}

// elementos generales de final
$elements      = array(
    'process_final' => array (
        'type' => 'hidden',
        'value' => 'final'
    ),

    'final' => array(
        'type'      => 'HTML',
        'class'     => 'fullwidth',
        'html'      =>   '<div class="contract-final" style="position: relative"><div>'
                       . '<div class="overlay" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0; z-index: 999"></div>'
                       . '<div style="z-index: 0">'
                       . $this->insert('contract/widget/review', array('contract' => $contract))
                       . '</div>'
                       . '</div></div>'
    )
);

if (!$contract->status->owner) {
    // Footer
    $elements['footer'] = array(
        'type'      => 'Group',
        'children'  => array(
            'errors' => array(
                'title' => $this->text('form-footer-errors_title'),
                'content'  => $this->insert('contract/partials/errors', array(
                    'contract'   => $contract,
                    'step'      => $step
                ))
            ),
            'buttons'  => array(
                'type'  => 'Group',
                'children' => $buttons
            )
        )

    );
}


$this->section('contract-edit-step');

// lanzamos el superform
echo \Goteo\Library\SuperForm::get(array(
    'action'        => '',
    'level'         => 3,
    'method'        => 'post',
    'title'         => $this->text('contract-step-final'),
    'hint'          => $this->text('guide-contract-final'),
    'elements'      => $elements
));

$this->replace();
