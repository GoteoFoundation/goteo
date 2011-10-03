<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);


$errors = $this['errors'];
$preferences = $this['preferences'];

$allow = array(
    array(
        'value'     => 1,
        'label'     => 'Sí'
        ),
    array(
        'value'     => 0,
        'label'     => 'No'
        )
);


?>
<form method="post" action="/dashboard/profile/preferences" class="project" >

<?php
echo new SuperForm(array(

    'level'         => 3,
    'method'        => 'post',
    'hint'          => 'Marca \'Sí\' a las notificaciones automáticas que quieras bloquear.' , //Text::get('guide-dashboard-user-preferences'),
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'class' => 'next',
            'name'  => 'save-userPreferences'
        )
    ),
    'elements'      => array(

        'updates' => array(
            'title'     => 'Bloquear notificaciones de Novedades en los proyectos que apoyo',
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'hint'      => Text::get('tooltip-preferences-updates'),
            'errors'    => array(),
            'value'     => (int) $preferences->updates
        ),
        'threads' => array(
            'title'     => 'Bloquear notificaciones de respuestas en los mensajes que yo inicio',
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'hint'      => Text::get('tooltip-preferences-threads'),
            'errors'    => array(),
            'value'     => (int) $preferences->threads
        ),
        'rounds' => array(
            'title'     => 'Bloquear notificaciones de progreso de los proyectos que apoyo',
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'hint'      => Text::get('tooltip-preferences-rounds'),
            'errors'    => array(),
            'value'     => (int) $preferences->rounds
        ),
        'mailing' => array(
            'title'     => 'Bloquear el envio de newsletter',
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'hint'      => Text::get('tooltip-preferences-mailing'),
            'errors'    => array(),
            'value'     => (int) $preferences->mailing
        )

    )

));

?>
</form>
