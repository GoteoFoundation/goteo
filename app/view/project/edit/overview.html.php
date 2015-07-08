<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $vars['project'];
$errors = $project->errors[$vars['step']] ?: array();
$okeys  = $project->okeys[$vars['step']] ?: array();

$categories = array();

foreach ($vars['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $project->categories)
        );
}

// preparamos selector idioma
foreach ($vars['languages'] as $value => $object) {
    $langs[] =  array(
        'value'     => $value,
        'label'     => $object->name,
    );
}

// preparamos campo de divisa
$currencies = $vars['currencies'];

if(count($currencies) > 1) {

    foreach ($currencies as $ccyId => $ccy) {
        $currnss[] =  array(
            'value'     => $ccyId,
            'label'     => $ccy['name'],
        );
    }
    $currency_field = array (
        'title'     => Text::get('overview-field-currency'),
        'type'      => 'select',
        'options'   => $currnss,
        'hint'      => Text::get('tooltip-project-currency'),
        'class'     => 'currently cols_2',
        'value'     => $project->currency
    );

} else {

    $currency_field = array (
        'type' => 'hidden',
        'value' => $vars['default_currency']
    );

}


// media del proyecto
if (!empty($project->media->url)) {
    $media = array(
            'type'  => 'media',
            'title' => Text::get('overview-field-media_preview'),
            'class' => 'inline media',
            'type'  => 'html',
            'html'  => !empty($project->media) ? $project->media->getEmbedCode($project->media_usubs) : ''
    );
} else {
    $media = array(
        'type'  => 'hidden',
        'class' => 'inline'
    );
}

// video de motivacion
if (!empty($project->video->url)) {
    $video = array(
            'type'  => 'media',
            'title' => Text::get('overview-field-media_preview'),
            'class' => 'inline media',
            'type'  => 'html',
            'html'  => !empty($project->video) ? $project->video->getEmbedCode($project->video_usubs) : ''
    );



} else {
    $video = array(
        'type'  => 'hidden',
        'class' => 'inline'
    );

}

// en función de si es pre-form o form

if (!$project->draft) {

    $about = array(
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-about'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-about'),
            'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
            'ok'        => !empty($okeys['about']) ? array($okeys['about']) : array(),
            'value'     => $project->about
    );

    $motivation= array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-motivation'),
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-motivation'),
                    'errors'    => !empty($errors['motivation']) ? array($errors['motivation']) : array(),
                    'ok'        => !empty($okeys['motivation']) ? array($okeys['motivation']) : array(),
                    'value'     => $project->motivation
    );

    $goal = array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-goal'),
                    'hint'      => Text::get('tooltip-project-goal'),
                    'errors'    => !empty($errors['goal']) ? array($errors['goal']) : array(),
                    'ok'        => !empty($okeys['goal']) ? array($okeys['goal']) : array(),
                    'value'     => $project->goal
    );

    $anchor_video = array(
            'type' => 'html',
            'html' => '<a name="video"></a>'
    );

    $video_field= array(
            'type'      => 'textbox',
            'required'  => false,
            'title'     => Text::get('overview-field-video'),
            'hint'      => Text::get('tooltip-project-video'),
            'errors'    => !empty($errors['video']) ? array($errors['video']) : array(),
            'ok'        => !empty($okeys['video']) ? array($okeys['video']) : array(),
            'value'     => (string) $project->video
    );

    $video_upload= array(
            'name' => "upload",
            'type'  => 'submit',
            'label' => Text::get('form-upload-button'),
            'class' => 'inline media-upload',
            'onclick' => "document.getElementById('proj-superform').action += '#video';"
    );

    $title_description_group=Text::get('overview-extra-fields');

} else {

    $about = array(
        'type'  => 'hidden',
        'class' => 'inline',
        'value'     => $project->about
    );

    $motivation = array(
        'type'  => 'hidden',
        'class' => 'inline',
        'value'     => $project->motivation
    );

    $goal = array(
        'type'  => 'hidden',
        'class' => 'inline',
        'value'     => $project->goal
    );

    $anchor_video = array(
        'type'  => 'hidden',
        'class' => 'inline'
    );
    $video_field=array(
        'type'  => 'hidden',
        'class' => 'inline',
        'value'     => (string) $project->video
    );

    $video_upload=array(
        'type'  => 'hidden',
        'class' => 'inline'
    );

}

$spread = array(
    'type'      => 'textarea',
    'title'     => Text::get('overview-field-spread'),
    'hint'      => Text::get('tooltip-project-spread'),
    'errors'    => !empty($errors['spread']) ? array($errors['spread']) : array(),
    'ok'        => !empty($okeys['spread']) ? array($okeys['spread']) : array(),
    'value'     => $project->spread
);

// nueva sección de contenido recompensas (oculta para el impulsor)
if ( $_SESSION['user']->id == $project->owner ) {
    $reward = array (
        'type' => 'hidden',
        'name' => 'reward',
        'value' => $project->reward
    );
} else {
    $reward = array(
        'type'      => 'textarea',
        'title'     => Text::get('overview-field-reward'),
        'hint'      => Text::get('tooltip-project-reward'),
        'errors'    => !empty($errors['reward']) ? array($errors['reward']) : array(),
        'ok'        => !empty($okeys['reward']) ? array($okeys['reward']) : array(),
        'value'     => $project->reward
    );
}


$superform = array(
    'level'         => $vars['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('overview-main-header'),
    'hint'          => Text::get('guide-project-description'),
    'class'         => 'aqua',
    'elements'      => array(
        'process_overview' => array (
            'type' => 'hidden',
            'value' => 'overview'
        ),

        'anchor-overview' => array(
            'type' => 'html',
            'html' => '<a name="overview"></a>'
        ),

        'name' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-name'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-name'),
            'value'     => $project->name,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),

        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-subtitle'),
            'required'  => false,
            'value'     => $project->subtitle,
            'hint'      => Text::get('tooltip-project-subtitle'),
            'errors'    => !empty($errors['subtitle']) ? array($errors['subtitle']) : array(),
            'ok'        => !empty($okeys['subtitle']) ? array($okeys['subtitle']) : array()
        ),

        // idioma en el que se escribe el proyecto
        'lang' => array(
            'title'     => Text::get('overview-field-lang'),
            'type'      => 'select',
            'options'   => $langs,
            'hint'      => Text::get('tooltip-project-lang'),
            'class'     => 'currently cols_2',
            'value'     => $project->lang
        ),

        // divisa (por defecto) de visualización del proyecto
        'currency' => $currency_field,

        'description' => array(
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-description'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-description'),
            'value'     => $project->description,
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'ok'        => !empty($okeys['description']) ? array($okeys['description']) : array()
        ),

        'category' => array(
            'type'      => 'checkboxes',
            'name'      => 'categories[]',
            'title'     => Text::get('overview-field-categories'),
            'required'  => true,
            'class'     => 'cols_3',
            'options'   => $categories,
            'hint'      => Text::get('tooltip-project-category'),
            'errors'    => !empty($errors['categories']) ? array($errors['categories']) : array(),
            'ok'        => !empty($okeys['categories']) ? array($okeys['categories']) : array()
        ),

        'location' => array(
            'type'      => 'textbox',
            'name'      => 'project_location',
            'title'     => Text::get('overview-field-project_location'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-project_location'),
            'errors'    => !empty($errors['project_location']) ? array($errors['project_location']) : array(),
            'ok'        => !empty($okeys['project_location']) ? array($okeys['project_location']) : array(),
            'value'     => $project->project_location,
            //use google maps autocomplete
            'class'     => 'geo-autocomplete',
            //write data to location tables
            'data'      => array('geocoder-type' => 'project', 'geocoder-item' => $project->id)
        ),

        // video principal del proyecto
        'anchor-media' => array(
            'type' => 'html',
            'html' => '<a name="media"></a>'
        ),

        'media' => array(
            'type'      => 'textbox',
            'required'  => is_object($project->called) ? false : true, // solo obligatorio si no está aplicando a convocatoria
            'title'     => Text::get('overview-field-media'),
            'hint'      => Text::get('tooltip-project-media'),
            'errors'    => !empty($errors['media']) ? array($errors['media']) : array(),
            'ok'        => !empty($okeys['media']) ? array($okeys['media']) : array(),
            'value'     => (string) $project->media
        ),

        'media-upload' => array(
            'name' => "upload",
            'type'  => 'submit',
            'label' => Text::get('form-upload-button'),
            'class' => 'inline media-upload',
            'onclick' => "document.getElementById('proj-superform').action += '#media';"
        ),

        'media-preview' => $media,

        'related' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-related'),
                    'hint'      => Text::get('tooltip-project-related'),
                    'errors'    => !empty($errors['related']) ? array($errors['related']) : array(),
                    'ok'        => !empty($okeys['related']) ? array($okeys['related']) : array(),
                    'value'     => $project->related
        ),

        'spread' => $spread,

        // fin media

        'description_group' => array(
            'type' => 'group',
            'title' => $title_description_group,
            'children'  => array(

                'about' => $about,

                'motivation' => $motivation,

                'goal' => $goal,


                //'reward' => $reward

            )
        ),
        // video motivacion
        'anchor-video' => $anchor_video,

        'video' => $video_field,

        'video-upload' => $video_upload,

        'video-preview' => $video,

        // fin video motivacion

        /*
        'keywords' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-keywords'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-keywords'),
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'ok'        => !empty($okeys['keywords']) ? array($okeys['keywords']) : array(),
            'value'     => $project->keywords
        ),
         */

        /* Aligerando superform
        'currently' => array(
            'title'     => Text::get('overview-field-currently'),
            'type'      => 'slider',
//            'required'  => true,
            'options'   => $currently,
            'class'     => 'currently cols_' . count($currently),
            'hint'      => Text::get('tooltip-project-currently'),
            'errors'    => !empty($errors['currently']) ? array($errors['currently']) : array(),
            'ok'        => !empty($okeys['currently']) ? array($okeys['currently']) : array(),
            'value'     => $project->currently
        ),
         */



        /* Aligerando superform
        'scope' => array(
            'title'     => Text::get('overview-field-scope'),
            'type'      => 'slider',
//            'required'  => true,
            'options'   => $scope,
            'class'     => 'scope cols_' . count($scope),
            'hint'      => Text::get('tooltip-project-scope'),
            'errors'    => !empty($errors['scope']) ? array($errors['scope']) : array(),
            'ok'        => !empty($okeys['scope']) ? array($okeys['scope']) : array(),
            'value'     => $project->scope
        ),
         */

        'footer' => array(
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
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-'.$vars['next'],
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )

        )

    )

);


foreach ($superform['elements'] as $id => &$element) {

    if (!empty($vars['errors'][$vars['step']][$id])) {
        $element['errors'] = arrray();
    }

}

echo SuperForm::get($superform);
