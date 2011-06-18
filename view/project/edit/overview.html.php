<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this['project'];

$images = array();
foreach ($project->gallery as $image) {
    $images[] = array(
        'type'  => 'group',
        'class' => 'inline image',
        'children'  => array(
            'gallery-image' => array(
                'type'  => 'html',
                'class' => 'inline image',
                'html'  => is_object($image) ?
                           $image . '<img src="' . htmlspecialchars($image->getLink(110, 110)) . '" alt="Imagen" />' :
                           ''
                ),
             'remove' => array(
                'name' => "gallery-{$image->id}-remove",
                'type'  => 'submit',
                'label' => Text::get('form-remove-button'),
                'class' => 'inline remove image-remove'
            )
        )
    );

}


$categories = array();

foreach ($this['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $project->categories)
        );            
}

$currently = array();

foreach ($this['currently'] as $value => $label) {
    $currently[] =  array(
        'value'     => $value,
        'label'     => $label        
        );            
}

$errors = $project->errors[$this['step']] ?: array();

$superform = array(
    'level'         => $this['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('overview-main-header'),
    'hint'          => Text::get('guide-project-description'),
    'class'         => 'aqua',    
    'footer'        => array(
        'view-step-costs' => array(
            'type'  => 'submit',
            'name'  => 'view-step-costs',
            'label' => Text::get('form-next-button'),
            'class' => 'next'
        )        
    ),
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
        ),
        
        'images' => array(        
            'title'     => Text::get('overview-fields-images-title'),
            'type'      => 'group',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-image'),
            'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
            'class'     => 'images',
            'children'  => array(
                'image_upload'    => array(
                    'type'  => 'file',
                    'class' => 'inline image_upload',
                    'title' => Text::get('overview-field-image_upload'),
                    'hint'  => Text::get('tooltip-project-image_upload'),
                ),
                'gallery' => array(
                    'type'  => 'group',
                    'title' => Text::get('overview-field-image_gallery'),
                    'class' => 'inline gallery',
                    'children'  => $images
                )

            )
        ),        

        'description' => array(            
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-description'),
            'required'  => true,
            'hint'      => Text::get('tooltip-project-description'),
            'value'     => $project->description,            
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'children'  => array(                
                'about' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('overview-field-about'),
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-about'),
                    'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
                    'value'     => $project->about
                ),
                'motivation' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('overview-field-motivation'),
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-motivation'),
                    'errors'    => !empty($errors['motivation']) ? array($errors['motivation']) : array(),
                    'value'     => $project->motivation
                ),
                'goal' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-goal'),
                    'hint'      => Text::get('tooltip-project-goal'),
                    'errors'    => !empty($errors['goal']) ? array($errors['goal']) : array(),
                    'value'     => $project->goal
                ),
                'related' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('overview-field-related'),
                    'hint'      => Text::get('tooltip-project-related'),
                    'errors'    => !empty($errors['related']) ? array($errors['related']) : array(),
                    'value'     => $project->related
                ),
            )
        ),
       
        'category' => array(    
            'type'      => 'checkboxes',
            'name'      => 'categories[]',
            'title'     => Text::get('overview-field-categories'),
            'required'  => true,
            'options'   => $categories,
            'hint'      => Text::get('tooltip-project-category'),
            'errors'    => !empty($errors['categories']) ? array($errors['categories']) : array(),
        ),       

        'keywords' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-keywords'),
            'hint'      => Text::get('tooltip-project-keywords'),
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'value'     => $project->keywords
        ),

        'media' => array(
            'type'      => 'textarea',
            'title'     => Text::get('overview-field-media'),
            'class'     => 'media',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-media'),
            'errors'    => !empty($errors['media']) ? array($errors['media']) : array(),
            'value'     => (string) $project->media,
            'children'  => array(
                'media-preview' => array(
                    'title' => Text::get('overview-field-media_preview'),
                    'class' => 'media-preview inline',
                    'type'  => 'html',
                    'html'  => '<div>' . (!empty($project->media) ? $project->media->getEmbedCode() : '') .'</div>'
                )
            )
        ),
                
        'currently' => array(    
            'title'     => 'Estado actual',
            'type'      => 'slider',
            'options'   => $currently,
            'class'     => 'currently cols_' . count($currently),
            'hint'      => Text::get('tooltip-project-currently'),
            'errors'    => !empty($errors['currently']) ? array($errors['currently']) : array(),
            'value'     => $project->currently
        ),

        'location' => array(
            'type'      => 'textbox',
            'name'      => 'project_location',
            'title'     => 'Localización',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-project_location'),
            'errors'    => !empty($errors['project_location']) ? array($errors['project_location']) : array(),
            'value'     => $project->project_location
        )                                        

    )

);

foreach ($superform['elements'] as $id => &$element) {
    
    if (!empty($this['errors'][$this['step']][$id])) {
        $element['errors'] = arrray();
    }
    
}

echo new SuperForm($superform);