<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this['project'];

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
    'title'         => 'Proyecto/Descripción',
    'hint'          => Text::get('guide-project-description'),
    'class'         => 'aqua',    
    'footer'        => array(
        'view-step-costs' => array(
            'type'  => 'submit',
            'name'  => 'view-step-costs',
            'label' => 'Siguiente',
            'class' => 'next'
        )        
    ),
    'elements'      => array(
        
        'name' => array(
            'type'      => 'textbox',
            'title'     => 'Nombre del proyecto',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-name'),
            'value'     => $project->name,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
        ),
        
        'images' => array(        
            'title'     => 'Imágenes del proyecto',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-image'),
            'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
        ),        

        'description' => array(            
            'type'      => 'textarea',
            'title'     => 'Descripción',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-description'),
            'value'     => $project->description,            
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'children'  => array(                
                'about' => array(
                    'type'      => 'textarea',       
                    'title'     => 'Qué es',
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-about'),
                    'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
                    'value'     => $project->about
                ),
                'motivation' => array(
                    'type'      => 'textarea',       
                    'title'     => 'Motivación',
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-motivation'),
                    'errors'    => !empty($errors['motivation']) ? array($errors['motivation']) : array(),
                    'value'     => $project->motivation
                ),
                'goal' => array(
                    'type'      => 'textarea',
                    'title'     => 'Objetivos',
                    'required'  => true,
                    'hint'      => Text::get('tooltip-project-goal'),
                    'errors'    => !empty($errors['goal']) ? array($errors['goal']) : array(),
                    'value'     => $project->goal
                ),
                'related' => array(
                    'type'      => 'textarea',
                    'required'  => true,
                    'title'     => 'Experiencia relacionada y equipo',
                    'hint'      => Text::get('tooltip-project-related'),
                    'errors'    => !empty($errors['related']) ? array($errors['related']) : array(),
                    'value'     => $project->related
                ),
            )
        ),
       
        'category' => array(    
            'type'      => 'checkboxes',
            'name'      => 'categories[]',
            'title'     => 'Categorías',
            'required'  => true,
            'options'   => $categories,
            'hint'      => Text::get('tooltip-project-category'),
            'errors'    => !empty($errors['categories']) ? array($errors['categories']) : array(),
        ),       

        'keywords' => array(
            'type'      => 'textbox',
            'title'     => 'Palabras clave',   
            'required'  => true,
            'hint'      => Text::get('tooltip-project-keywords'),
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'value'     => $project->keywords
        ),

        'media' => array(
            'type'      => 'textarea',
            'title'     => 'Vídeo',
            'class'     => 'media',
            'required'  => true,
            'hint'      => Text::get('tooltip-project-media'),
            'errors'    => !empty($errors['media']) ? array($errors['media']) : array(),
            'value'     => $project->media,
            'children'  => array(
                'media-preview' => array(
                    'title' => 'Vista previa',
                    'class' => 'media-preview inline',
                    'type'  => 'html',
                    'html'  => '<div></div>'
                )
            )
        ),
                
        'currently' => array(    
            'title'     => 'Estado actual',
            'type'      => 'slider',
            'options'   => $currently,
            'required'  => true,
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
            'hint'      => Text::get('tooltip-project-location'),
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