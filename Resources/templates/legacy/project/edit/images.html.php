<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $vars['project'];
$errors = $project->errors[$vars['step']] ?: array();
$okeys  = $project->okeys[$vars['step']] ?: array();

$images = array();
foreach ($project->images as $image) {
    $images[] = array(
        'type'  => 'html',
        'class' => 'inline gallery-image',
        'html'  => is_object($image) ?
                   '<img src="' . SITE_URL . '/image/' . $image->id . '/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->hash.'-remove" title="Quitar imagen" value="remove" onclick="document.getElementById(\'proj-superform\').action += \'#images\';"></button>' :
                   ''
    );

}

/*
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
*/

$superform = array(
    'level'         => $vars['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('images-main-header'),
    'hint'          => Text::get('guide-project-images'),
    'class'         => 'aqua',
    'elements'      => array(
        'process_images' => array (
            'type' => 'hidden',
            'value' => 'images'
        ),


        'anchor-images' => array(
            'type' => 'html',
            'html' => '<a name="images"></a>'
        ),

        'images' => array(
            'title'     => Text::get('overview-fields-images-title'),
            'type'      => 'group',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-image'),
            'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
            'ok'        => !empty($okeys['image']) ? array($okeys['image']) : array(),
            'class'     => 'images',
            'children'  => array(
                'image_upload'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline image_upload',
                    'hint'  => Text::get('tooltip-project-image'),
                    'onclick' => "document.getElementById('proj-superform').action += '#images';"
                )
            )
        ),
        'gallery' => array(
            'type'  => 'group',
            'title' => Text::get('overview-field-image_gallery'),
            'class' => 'inline',
            'children'  => $images
        ),


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
        $element['errors'] = array();
    }

}

echo SuperForm::get($superform);
