<?php

use Goteo\Library\Text,
    Goteo\Library\NormalForm;

$call = $vars['call'];
$vars['level'] = 3;

$maxp_modes = array(
    'imp' => array(
        'value' => 'imp',
        'label' => Text::get('call-field-options-modemaxp_imp')
    ),
    'per' => array(
        'value' => 'per',
        'label' => Text::get('call-field-options-modemaxp_per')
    )
);

?>

<form method="post" action="/admin/calls/dropconf/<?php echo $call->id; ?>" class="project" enctype="multipart/form-data">
<?php
echo new NormalForm(array(

    'level'         => $vars['level'],
    'method'        => 'post',
    'hint'          => "Configuración financiera de la convocatoria " . addslashes($call->name),
    'class'         => 'gestion_financiera',

    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'class' => 'next',
            'name'  => 'save-dropconf'
        )
    ),
    'elements'      => array(

         'amount' => array(
            'type'      => 'textbox',
            'required'  => true,
            'title'     => Text::get('call-field-amount'),
            'size'      => 8,
            'class'     => 'amount',
            'hint'      => Text::get('tooltip-call-amount'),
            'errors'    => !empty($errors['amount']) ? array($errors['amount']) : array(),
            'ok'        => !empty($okeys['amount']) ? array($okeys['amount']) : array(),
            'value'     => $call->amount
        ),


        'maxdrop' => array(
            'type'      => 'textbox',
            'required'  => false,
            'title'     => Text::get('call-field-maxdrop'),
            'size'      => 8,
            'class'     => 'amount',
            'hint'      => Text::get('tooltip-call-maxdrop'),
            'errors'    => !empty($errors['maxdrop']) ? array($errors['maxdrop']) : array(),
            'ok'        => !empty($okeys['maxdrop']) ? array($okeys['maxdrop']) : array(),
            'value'     => $call->maxdrop
        ),

        'maxproj' => array(
            'type'      => 'textbox',
            'required'  => false,
            'title'     => Text::get('call-field-maxproj'),
            'size'      => 8,
            'class'     => 'days',
            'hint'      => Text::get('tooltip-call-maxproj'),
            'errors'    => !empty($errors['maxproj']) ? array($errors['maxproj']) : array(),
            'ok'        => !empty($okeys['maxproj']) ? array($okeys['maxproj']) : array(),
            'value'     => $call->maxproj
        ),

        'modemaxp' => array(
            'title'     => Text::get('call-field-modemaxp'),
            'type'      => 'slider',
            'required'  => false,
            'options'   => $maxp_modes,
            'class'     => 'inline scope cols_2',
            'hint'      => Text::get('tooltip-call-modemaxp'),
            'errors'    => !empty($errors['modemaxp']) ? array($errors['modemaxp']) : array(),
            'ok'        => !empty($okeys['modemaxp']) ? array($okeys['modemaxp']) : array(),
            'value'     => $call->modemaxp
        ),


    )

));

?>
</form>
