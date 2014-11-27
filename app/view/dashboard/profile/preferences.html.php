<?php

use Goteo\Library\Text,
    Goteo\Library\NormalForm,
    Goteo\Library\Lang,
    Goteo\Library\Currency;

$currencies = Currency::$currencies;

$errors = $this['errors'];
$preferences = $this['preferences'];

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

//Obtenemos todos los idiomas activos
$languages = Lang::getall(true);

foreach ($languages as $value => $objet) {
    $langs[] =  array(
        'value'     => $value,
        'label'     => $objet->name,
        );
}

//obtenemos todas las monedas
$currencies = Currency::$currencies;

foreach ($currencies as $ccyId => $ccy) {
    $curren[] =  array(
        'value'     => $ccyId,
        'label'     => $ccy['name'],
        );
}



?>
<form method="post" action="/dashboard/profile/preferences" class="project" >

<?php
echo new NormalForm(array(

    'level'         => 3,
    'method'        => 'post',
    'hint'          => Text::get('guide-dashboard-user-preferences'),
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'class' => 'next',
            'name'  => 'save-userPreferences'
        )
    ),
    'elements'      => array(

         'comlang' => array(
            'title'     => Text::get('user-preferences-comlang'),
            'type'      => 'select',
            'options'   => $langs,
            'class'     => 'currently cols_' . count($allow),
            'value'     => $preferences->comlang
        ),
        'comcurrency' => array(
            'title'     => Text::get('user-preferences-comcurrency'),
            'type'      => 'select',
            'options'   => $curren,
            'class'     => 'currently cols_' . count($allow),
            'value'     => $preferences->comcurrency
        ),
        'updates' => array(
            'title'     => Text::get('user-preferences-updates'),
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'value'     => (int) $preferences->updates
        ),
        'threads' => array(
            'title'     => Text::get('user-preferences-threads'),
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'value'     => (int) $preferences->threads
        ),
        'rounds' => array(
            'title'     => Text::get('user-preferences-rounds'),
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'value'     => (int) $preferences->rounds
        ),
        'mailing' => array(
            'title'     => Text::get('user-preferences-mailing'),
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'value'     => (int) $preferences->mailing
        ),
        'email' => array(
            'title'     => Text::get('user-preferences-email'),
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'value'     => (int) $preferences->email
        ),
        'tips' => array(
            'title'     => Text::get('user-preferences-tips'),
            'type'      => 'slider',
            'options'   => $allow,
            'class'     => 'currently cols_' . count($allow),
            'value'     => (int) $preferences->tips
        )
    )

));

?>
</form>
