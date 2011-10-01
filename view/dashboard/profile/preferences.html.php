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
        'selfproj' => array(
            'title'     => 'Bloquear notificaciones de seguimiento de mis proyectos',
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'hint'      => Text::get('tooltip-preferences-selfproj'),
            'errors'    => array(),
            'value'     => (int) $preferences->selfproj
        ),
        'anymail' => array(
            'title'     => 'Bloquear todas las notificaciones que no sean exclusivamente para mi',
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'hint'      => Text::get('tooltip-preferences-anymail'),
            'errors'    => array(),
            'value'     => (int) $preferences->anymail
        )

    )

));

?>
</form>
