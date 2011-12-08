<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$call = $this['call'];
$errors = $call->errors[$this['step']] ?: array();
$okeys  = $call->okeys[$this['step']] ?: array();

$categories = array();

foreach ($this['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $call->categories)
        );            
}

$icons = array();

foreach ($this['icons'] as $value => $label) {
    $icons[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $call->icons)
        );
}

$superform = array(
    'level'         => $this['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('call-overview-main-header'),
    'hint'          => Text::get('guide-call-description'),
    'class'         => 'aqua',        
    'elements'      => array(
        'process_overview' => array (
            'type' => 'hidden',
            'value' => 'overview'
        ),
        
        'name' => array(
            'type'      => 'textbox',
            'title'     => Text::get('call-field-name'),
            'required'  => true,
            'hint'      => Text::get('tooltip-call-name'),
            'value'     => $call->name,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        
        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => Text::get('overview-field-subtitle'),
            'required'  => false,
            'value'     => $call->subtitle,
            'hint'      => Text::get('tooltip-call-subtitle'),
            'errors'    => !empty($errors['subtitle']) ? array($errors['subtitle']) : array(),
            'ok'        => !empty($okeys['subtitle']) ? array($okeys['subtitle']) : array()
        ),

        'logo' => array(
            'type'      => 'group',
            'required'  => true,
            'title'     => Text::get('call-field-logo-title'),
            'hint'      => Text::get('tooltip-call-logo'),
            'errors'    => !empty($errors['logo']) ? array($errors['logo']) : array(),
            'ok'        => !empty($okeys['logo']) ? array($okeys['logo']) : array(),
            'class'     => 'logo',
            'children'  => array(
                'logo_upload'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline image_upload',
                    'hint'  => Text::get('tooltip-call-logo'),
                ),
                'logo-current' => array(
                    'type' => 'hidden',
                    'value' => $call->logo,
                ),
                'logo-image' => array(
                    'type'  => 'html',
                    'class' => 'inline logo-image',
                    'html'  => !empty($call->logo)  ?
                               '<img src="'.SRC_URL.'/image/' . $call->logo . '/128/128" alt="Logo" /><button class="image-remove" type="submit" name="logo-'.$call->logo.'-remove" title="Quitar imagen" value="remove">X</button>' :
                               ''
                )

            )
        ),

        'image' => array(
            'type'      => 'group',
            'required'  => true,
            'title'     => Text::get('call-field-image-title'),
            'hint'      => Text::get('tooltip-call-image'),
            'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
            'ok'        => !empty($okeys['image']) ? array($okeys['image']) : array(),
            'class'     => 'image',
            'children'  => array(
                'image_upload'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline avatar_upload',
                    'hint'  => Text::get('tooltip-call-image'),
                ),
                'image-current' => array(
                    'type' => 'hidden',
                    'value' => $call->image,
                ),
                'image-image' => array(
                    'type'  => 'html',
                    'class' => 'inline image-image',
                    'html'  => !empty($call->image) ?
                               '<img src="'.SRC_URL.'/image/' . $call->image . '/128/128" alt="Imagen" /><button class="image-remove" type="submit" name="image-'.$call->image.'-remove" title="Quitar imagen" value="remove">X</button>' :
                               ''
                )

            )
        ),

        'description' => array(            
            'type'      => 'textarea',
            'title'     => Text::get('call-field-description'),
            'required'  => true,
            'hint'      => Text::get('tooltip-call-description'),
            'value'     => $call->description,
            'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
            'ok'        => !empty($okeys['description']) ? array($okeys['description']) : array()
        ),
        'description_group' => array(
            'type' => 'group',
            'children'  => array(                
                'whom' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('call-field-whom'),
                    'required'  => true,
                    'hint'      => Text::get('tooltip-call-whom'),
                    'errors'    => !empty($errors['whom']) ? array($errors['whom']) : array(),
                    'ok'        => !empty($okeys['whom']) ? array($okeys['whom']) : array(),
                    'value'     => $call->whom
                ),
                'apply' => array(
                    'type'      => 'textarea',       
                    'title'     => Text::get('call-field-apply'),
                    'required'  => true,
                    'hint'      => Text::get('tooltip-call-apply'),
                    'errors'    => !empty($errors['apply']) ? array($errors['apply']) : array(),
                    'ok'        => !empty($okeys['apply']) ? array($okeys['apply']) : array(),
                    'value'     => $call->apply
                ),
                'legal' => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('call-field-legal'),
                    'hint'      => Text::get('tooltip-call-legal'),
                    'errors'    => !empty($errors['legal']) ? array($errors['legal']) : array(),
                    'ok'        => !empty($okeys['legal']) ? array($okeys['legal']) : array(),
                    'value'     => $call->legal
                ),
            )
        ),
       
        'category' => array(    
            'type'      => 'checkboxes',
            'name'      => 'categories[]',
            'title'     => Text::get('call-field-categories'),
            'required'  => true,
            'class'     => 'cols_3',
            'options'   => $categories,
            'hint'      => Text::get('tooltip-call-category'),
            'errors'    => !empty($errors['categories']) ? array($errors['categories']) : array(),
            'ok'        => !empty($okeys['categories']) ? array($okeys['categories']) : array()
        ),       

        'icon' => array(
            'type'      => 'checkboxes',
            'name'      => 'icons[]',
            'title'     => Text::get('call-field-icons'),
            'required'  => true,
            'class'     => 'cols_3',
            'options'   => $icons,
            'hint'      => Text::get('tooltip-call-icons'),
            'errors'    => !empty($errors['icons']) ? array($errors['icons']) : array(),
            'ok'        => !empty($okeys['icons']) ? array($okeys['icons']) : array()
        ),


        'location' => array(
            'type'      => 'textbox',
            'name'      => 'call_location',
            'title'     => Text::get('call-field-call_location'),
            'required'  => true,
            'hint'      => Text::get('tooltip-call-call_location'),
            'errors'    => !empty($errors['call_location']) ? array($errors['call_location']) : array(),
            'ok'        => !empty($okeys['call_location']) ? array($okeys['call_location']) : array(),
            'value'     => $call->call_location
        ),

        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('view/project/edit/errors.html.php', array(
                        'project'   => $call,
                        'step'      => $this['step']
                    ))                    
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-costs',
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