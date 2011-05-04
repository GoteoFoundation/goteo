<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];
$user = $this['user'];

$interests = array();

$errors = $project->errors[$this['step']] ?: array();

foreach ($this['interests'] as $value => $label) {
    $interests[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $user->interests)
        );            
}

$user_webs = array();

foreach ($user->webs as $web) {
        
    $user_webs['web' . $web->id] = array(
        'type'      => 'group',
        'class'     => 'web',
        'children'  => array(            
            'web-' . $web->id . '-url' => array(
                'type'      => 'textbox',
                'value'     => $web->url,
                'errors'    => array(),
                'required'  => true,
                'class'     => 'web-url inline'
            ),
            'web-' . $web->id . '-remove' => array(
                'type'      => 'submit',                
                'label'     => 'Quitar',
                'class'     => 'web-remove inline remove'
            )
        )
    );
    
}

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Usuario/Perfil',
    'hint'          => Text::get('guide-project-user-information'),    
    'footer'        => array(
        'view-step-userPersonal' => array(
            'type'  => 'submit',
            'label' => 'Siguiente',
            'name'  => 'view-step-userPersonal',
            'class' => 'next'
        )        
    ),    
    'elements'      => array(
        'user_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => 'Nombre completo',
            'hint'      => Text::get('tooltip-user-name'),
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'value'     => $user->name,
        ),                
        'user_avatar' => array(                  
            'title'     => 'Tu imagen',
            'type'      => 'group',
            'hint'      => Text::get('tooltip-user-image'),
            'errors'    => !empty($errors['avatar']) ? array($errors['avatar']) : array(),
            'class'     => 'user_avatar',
            'children'  => array(                
                'avatar_upload'    => array(
                    'type'  => 'file',
                    'class' => 'inline avatar_upload',
                    'title' => 'Subir una imagen',
                    'hint'  => Text::get('tooltip-user-avatar_upload'),
                ),                
                'avatar-current' => array(
                    'type'  => 'group',
                    'title' => 'Tu imagen actual',                    
                    'class' => 'inline avatar',
                    'children'  => array(                        
                        'avatar' => array(
                            'type'  => 'hidden',
                            'value' => $user->avatar->id,                    
                        ),
                        'avatar-image' => array(
                            'type'  => 'html',
                            'class' => 'inline',
                            'html'  => is_object($user->avatar) ? 
                                       $user->avatar . '<img src="' . htmlspecialchars($user->avatar->getLink(110, 110)) . '" alt="Avatar" />' :
                                       ''
                        )                        
                    )               
                )
                
            )            
        ),        
        
        'user_about' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => 'Cuéntanos algo sobre ti',
            'hint'      => Text::get('tooltip-user-about'),
            'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
            'value'     => $user->about
        ),          
        'interests' => array(
            'type'      => 'checkboxes',
            'name'      => 'user_interests[]',
            'title'     => 'Tus intereses',
            'hint'      => Text::get('tooltip-user-interests'),            
            'errors'    => !empty($errors['interests']) ? array($errors['interests']) : array(),
            'options'   => $interests
        ),  
        'user_keywords' => array(
            'type'      => 'textbox',
            'size'      => 20,
            'title'     => 'Palabras clave',
            'hint'      => Text::get('tooltip-user-keywords'),
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'value'     => $user->keywords
        ), 
        'user_contribution' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => 'Qué podrías aportar a Goteo',
            'hint'      => Text::get('tooltip-user-contribution'),
            'errors'    => !empty($errors['contribution']) ? array($errors['contribution']) : array(),
            'value'     => $user->contribution
        ),
        'user_webs' => array(
            'title'     => 'Mis webs',            
            'hint'      => Text::get('tooltip-user-webs'),
            'class'     => 'webs',
            'children'  => $user_webs + array(                
                'web-add' => array(
                    'type'  => 'submit',
                    'label' => 'Añadir',
                    'class' => 'add'                    
                )
            )
        ),
        'user_social' => array(            
            'type'      => 'group',
            'title'     => 'Perfiles sociales',
            'children'  => array(
                'user_facebook' => array(
                    'type'      => 'textbox',
                    'class'     => 'facebook',
                    'size'      => 40,
                    'title'     => 'Facebook',
                    'hint'      => Text::get('tooltip-user-facebook'),
                    'errors'    => !empty($errors['facebook']) ? array($errors['facebook']) : array(),
                    'value'     => $user->facebook
                ), 
                'user_twitter' => array(
                    'type'      => 'textbox',
                    'class'     => 'twitter',
                    'size'      => 40,
                    'title'     => 'Twitter',
                    'hint'      => Text::get('tooltip-user-twitter'),
                    'errors'    => !empty($errors['twitter']) ? array($errors['twitter']) : array(),
                    'value'     => $user->twitter
                ), 
                'user_linkedin' => array(
                    'type'      => 'textbox',
                    'class'     => 'linkedin',
                    'size'      => 40,
                    'title'     => 'LinkedIn',
                    'hint'      => Text::get('tooltip-user-linkedin'),
                    'errors'    => !empty($errors['linkedin']) ? array($errors['linkedin']) : array(),
                    'value'     => $user->linkedin
                )
            )            
        )        
    )
));