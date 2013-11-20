<?php

use Goteo\Library\Text,
    Goteo\Library\Normalform;

$call = $this['call'];
$conf = $this['conf'];
$this['level'] = 3;

// funcion para pintar un selector de limites
function limitSel ($label, $name, $current) {

    $limits = array(
        'normal' => 'No especificado',
        'unlimited' => 'A tope',
        'minimum' => 'Cubrir costes',
        'none' => 'Sin riego'
    );

    $sel = '<label>'.$label.': <select name="'.$name.'">';
    foreach ($limits as $key => $val) {
        $selected = ($val == $current) ? ' selected="selected"' : '';
        $sel .= '<option value="'.$key.'">'.$val.'</option>';
    }
    $sel .= '</select></label>';

    return $sel;
}

$allow = array(
    array(
        'value'     => 1,
        'label'     => Text::get('regular-yes')
        ),
    array(
        'value'     => 0,
        'label'     => Text::get('regular-no')
        )
);

$legend = "<strong>No especificado</strong>: ".Text::get('call-conf-normal')."
    <br /><br /><strong>Cubrir costes</strong>: ".Text::get('call-conf-minimum')."
    <br /><br /><strong>A tope</strong>: ".Text::get('call-conf-unlimited')."
    <br /><br /><strong>Sin riego</strong>: ".Text::get('call-conf-none');
?>
<form method="post" action="/admin/calls/conf/<?php echo $call->id; ?>" class="project" enctype="multipart/form-data">
<?php
echo new NormalForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'hint'          => "Configuración financiera y funcional de la convocatoria " . addslashes($call->name),

    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'class' => 'next',
            'name'  => 'save-conf'
        )
    ),
    'elements'      => array(

        'limits' => array(
            'type'      => 'group',
            'title'     => 'Límite de Capital Riego que puede conseguir un proyecto',
            'children'  => array(
                'limit_1st' => array(
                    'type'  => 'html',
                    'class' => 'inline',
                    'html'  => limitSel('En primera ronda', 'limit1', $values[$conf->limit1])
                ),
                'limit_2nd' => array(
                    'type'  => 'html',
                    'class' => 'inline',
                    'html'  => limitSel('En segunda ronda', 'limit2', $values[$conf->limit2])
                ),

                'legend'    => array(
                    'type'  => 'html',
                    'class' => 'inline',
                    'html'  => '<fieldset><legend>Límites</legend><p>'.$legend.'</p></fieldset>'
                )

            )
        ),

        'buzz' => array(
            'type'      => 'group',
            'title'     => 'Carrusel de tweets',
            'children'  => array(
                'buzz_own' => array(
                    'title'     => Text::get('call-conf-buzz_own'),
                    'type'      => 'slider',
                    'options'   => $allow,
                    'class'     => 'currently cols_' . count($allow),
                    'value'     => (int) $conf->buzz_own
                ),
                'buzz_mention' => array(
                    'title'     => Text::get('call-conf-buzz_mention'),
                    'type'      => 'slider',
                    'options'   => $allow,
                    'class'     => 'currently cols_' . count($allow),
                    'value'     => (int) $conf->buzz_mention
                ),
                'buzz_first' => array(
                    'title'     => Text::get('call-conf-buzz_first'),
                    'type'      => 'slider',
                    'options'   => $allow,
                    'class'     => 'currently cols_' . count($allow),
                    'value'     => (int) $conf->buzz_first
                )
            )
        ),

        'applied' => array(
            'type'  => 'textbox',
            'class'     => 'inline reward-amount',
            'title' => Text::get('call-conf-applied'),
            'size'  => 5,
            'value' => !empty($conf->applied) ? $conf->applied : ''
        ),

    )

));

?>
</form>