<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this['project'];
$errors = $project->errors[$this['step']] ?: array();
$okeys  = $project->okeys[$this['step']] ?: array();

$categories = array();

foreach ($this['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $project->categories)
        );            
}

/*
 * Aligerando superform
$currently = array();

foreach ($this['currently'] as $value => $label) {
    $currently[] =  array(
        'value'     => $value,
        'label'     => $label        
        );            
}

$scope = array();

foreach ($this['scope'] as $value => $label) {
    $scope[] =  array(
        'value'     => $value,
        'label'     => $label
        );
}
 */


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
// nueva sección de contenido recompensas
if ( isset($_SESSION['user']->roles['admin'])
  || isset($_SESSION['user']->roles['superadmin'])
  || isset($_SESSION['user']->roles['translator'])
  || isset($_SESSION['user']->roles['checker']) ) {
    // es admin o similar
    $reward = array(
        'type'      => 'textarea',
        'title'     => Text::get('overview-field-reward'),
        'hint'      => Text::get('tooltip-project-reward'),
        'errors'    => !empty($errors['reward']) ? array($errors['reward']) : array(),
        'ok'        => !empty($okeys['reward']) ? array($okeys['reward']) : array(),
        'value'     => $project->reward
    );
} else {
    // es user
    $reward = array (
        'type' => 'hidden',
        'name' => 'reward',
        'value' => $project->reward
    );
}


$superform = array(
    'level'         => $this['level'],
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

        'anchor-images' => array(
            'type' => 'html',
            'html' => '<a name="images"></a>'
        ),
        
        'description' => array(
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-description'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-description'),
            'value'     => $project->description,            
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'ok'        => !empty($okeys['description']) ? array($okeys['description']) : array()
        ),
        
        // video principal del proyecto
        'anchor-media' => array(
            'type' => 'html',
            'html' => '<a name="media"></a>'
        ),
        
        'media' => array(
            'type'      => 'textbox',
            'required'  => is_object($project->call) ? false : true, // solo obligatorio si no está aplicando a convocatoria
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
        
        // universal subtitles video principal
        'media_usubs' => array(
            'type'      => 'checkbox',
            'class'     => 'inline cols_1',
            'required'  => false,
            'label'     => Text::get('overview-field-usubs'),
            'name'      => 'media_usubs',
            'hint'      => Text::get('tooltip-project-usubs'),
            'errors'    => array(),
            'ok'        => array(),
            'checked'   => (bool) $project->media_usubs,
            'value'     => 1
        ),
        // fin media
        
        'description_group' => array(
            'type' => 'group',
            'children'  => array(                
                'about' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('overview-field-about'),
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-about'),
                    'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
                    'ok'        => !empty($okeys['about']) ? array($okeys['about']) : array(),
                    'value'     => $project->about
                ),
                'motivation' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('overview-field-motivation'),
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-motivation'),
                    'errors'    => !empty($errors['motivation']) ? array($errors['motivation']) : array(),
                    'ok'        => !empty($okeys['motivation']) ? array($okeys['motivation']) : array(),
                    'value'     => $project->motivation
                ),
                'goal' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-goal'),
                    'hint'      => Text::get('tooltip-project-goal'),
                    'errors'    => !empty($errors['goal']) ? array($errors['goal']) : array(),
                    'ok'        => !empty($okeys['goal']) ? array($okeys['goal']) : array(),
                    'value'     => $project->goal
                ),
                'related' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-related'),
                    'hint'      => Text::get('tooltip-project-related'),
                    'errors'    => !empty($errors['related']) ? array($errors['related']) : array(),
                    'ok'        => !empty($okeys['related']) ? array($okeys['related']) : array(),
                    'value'     => $project->related
                ),

                'reward' => $reward
                
            )
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

        // video motivacion
        'anchor-video' => array(
            'type' => 'html',
            'html' => '<a name="video"></a>'
        ),
        
        'video' => array(
            'type'      => 'textbox',
            'required'  => false,
            'title'     => Text::get('overview-field-video'),
            'hint'      => Text::get('tooltip-project-video'),
            'errors'    => !empty($errors['video']) ? array($errors['video']) : array(),
            'ok'        => !empty($okeys['video']) ? array($okeys['video']) : array(),
            'value'     => (string) $project->video
        ),

        'video-upload' => array(
            'name' => "upload",
            'type'  => 'submit',
            'label' => Text::get('form-upload-button'),
            'class' => 'inline media-upload',
            'onclick' => "document.getElementById('proj-superform').action += '#video';"
        ),

        'video-preview' => $video,

        // universal subtitles video motivacion
        'video_usubs' => array(
            'type'      => 'checkbox',
            'class'     => 'inline cols_1',
            'required'  => false,
            'name'      => 'video_usubs',
            'label'     => Text::get('overview-field-usubs'),
            'hint'      => Text::get('tooltip-project-usubs'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => 1,
            'checked'   => (bool) $project->video_usubs
        ),
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

        'location' => array(
            'type'      => 'textbox',
            'name'      => 'project_location',
            'title'     => Text::get('overview-field-project_location'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-project_location'),
            'errors'    => !empty($errors['project_location']) ? array($errors['project_location']) : array(),
            'ok'        => !empty($okeys['project_location']) ? array($okeys['project_location']) : array(),
            'value'     => $project->project_location
        ),

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
                    'view'  => new View('view/project/edit/errors.html.php', array(
                        'project'   => $project,
                        'step'      => $this['step']
                    ))                    
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-images',
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
    
    if (!empty($this['errors'][$this['step']][$id])) {
        $element['errors'] = arrray();
    }
    
}

echo new SuperForm($superform);