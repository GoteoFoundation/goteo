<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project  = $this['project'];
$original = $this['original'];
$errors   = $this['errors'];

// media del proyecto
if (!empty($project->media->url)) {
    $media = array(
            'type'  => 'media',
            'title' => Text::get('overview-field-media_preview'),
            'class' => 'inline media',
            'type'  => 'html',
            'html'  => !empty($project->media) ? $project->media->getEmbedCode() : ''
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
            'html'  => !empty($project->video) ? $project->video->getEmbedCode() : ''
    );
} else {
    $video = array(
        'type'  => 'hidden',
        'class' => 'inline'
    );
}


// nueva sección de contenido recompensas (oculta para el impulsor)
if ( $_SESSION['user']->id == $project->owner ) {
    $reward_orig = array (
        'type' => 'hidden',
        'name' => 'reward_orig',
        'value' => ''
    );
    $reward = array (
        'type' => 'hidden',
        'name' => 'reward',
        'value' => $project->reward
    );
} else {
    $reward_orig = array(
        'type'      => 'html',
        'title'     => Text::get('overview-field-reward'),
        'html'     => nl2br($original->reward)
    );
    $reward = array(
        'type'      => 'textarea',
        'title'     => '',
        'class'     => 'inline',
        'hint'      => Text::get('tooltip-project-reward'),
        'errors'    => array(),
        'ok'        => array(),
        'value'     => $project->reward
    );
}

?>

<form method="post" action="/dashboard/translates/overview/save" class="project" enctype="multipart/form-data">

<?php echo SuperForm::get(array(
    'autoupdate'    => false,
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-project-description'),
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-overview',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    'elements'      => array(
        'process_overview' => array (
            'type' => 'hidden',
            'value' => 'overview'
        ),

        /*
        'name' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-name'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-name'),
            'value'     => $project->name,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        */

        'subtitle-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('overview-field-subtitle'),
            'html'     => $original->subtitle
        ),
        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $project->subtitle,
            'hint'      => Text::get('tooltip-project-subtitle'),
            'errors'    => array(),
            'ok'        => array()
        ),

        'description-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('overview-field-description'),
            'html'     => nl2br($original->description)
        ),
        'description' => array(
            'type'      => 'textarea',
            'title'     => '',
            'class'     => 'inline',
            'hint'      => Text::get('tooltip-project-description'),
            'value'     => $project->description,
            'errors'    => array(),
            'ok'        => array()
        ),
        'description_group' => array(
            'type' => 'group',
            'children'  => array(
                'about-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('overview-field-about'),
                    'html'     => $original->about
                ),
                'about' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-about'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $project->about
                ),
                'motivation-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('overview-field-motivation'),
                    'html'     => nl2br($original->motivation)
                ),
                'motivation' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-motivation'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $project->motivation
                ),
                // video motivacion
                'video-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('overview-field-video'),
                    'html'     => (string) $original->video->url
                ),

                'video' => array(
                    'type'      => 'textbox',
                    'hint'      => Text::get('tooltip-project-video'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => (string) $project->video
                ),

                'video-upload' => array(
                    'name' => "upload",
                    'type'  => 'submit',
                    'label' => Text::get('form-upload-button'),
                    'class' => 'inline media-upload'
                ),

                'video-preview' => $video
                ,
                // fin video motivacion
                'goal-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('overview-field-goal'),
                    'html'     => nl2br($original->goal)
                ),
                'goal' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-goal'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $project->goal
                ),
                'related-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('overview-field-related'),
                    'html'     => nl2br($original->related)
                ),
                'related' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-related'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $project->related
                ),
                'reward-orig' => $reward_orig,
                'reward' => $reward
            )
        ),

        'keywords-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('overview-field-keywords'),
            'html'     => $original->keywords
        ),
        'keywords' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'hint'      => Text::get('tooltip-project-keywords'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => $project->keywords
        ),

        'media-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('overview-field-media'),
            'html'     => (string) $original->media->url
        ),

        'media' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'hint'      => Text::get('tooltip-project-media'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => (string) $project->media
        ),

        'media-upload' => array(
            'name' => "upload",
            'type'  => 'submit',
            'label' => Text::get('form-upload-button'),
            'class' => 'inline media-upload'
        ),

        'media-preview' => $media

    )

));
?>
</form>
