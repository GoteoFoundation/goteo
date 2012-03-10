<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);

$node = $this['node'];

if (!$node instanceof Model\Node) {
    throw new Redirection('/admin');
}

// Superform
$sfid = 'sf-node-edit';
?>
<form method="post" action="/admin/node" class="project" enctype="multipart/form-data">

    <?php echo new SuperForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'title'         => '',
        'hint'          => Text::get('guide-node-edit'),
        'class'         => 'aqua',
        'footer'        => array(
            'view-step-preview' => array(
                'type'  => 'submit',
                'name'  => 'save-node',
                'label' => Text::get('regular-save'),
                'class' => 'next'
            )
        ),
        'elements'      => array(
            'name' => array(
                'type'      => 'textbox',
                'required'  => true,
                'size'      => 20,
                'title'     => 'Nombre',
                'hint'      => '',
                'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
                'value'     => $node->name,
            ),
            'subtitle' => array(
                'type'      => 'textbox',
                'required'  => true,
                'size'      => 20,
                'title'     => 'Subtítulo',
                'hint'      => '',
                'errors'    => !empty($errors['subtitle']) ? array($errors['subtitle']) : array(),
                'value'     => $node->subtitle,
            ),
            'logo' => array(
                'type'      => 'group',
                'required'  => true,
                'title'     => Text::get('node-fields-logo-title'),
                'hint'      => '',
                'errors'    => !empty($errors['logo']) ? array($errors['logo']) : array(),
                'ok'        => !empty($okeys['logo']) ? array($okeys['logo']) : array(),
                'class'     => 'user_avatar',
                'children'  => array(
                    'logo_upload'    => array(
                        'type'  => 'file',
                        'label' => Text::get('form-image_upload-button'),
                        'class' => 'inline avatar_upload',
                        'hint'  => '',
                    ),
                    'logo-current' => array(
                        'type' => 'hidden',
                        'value' => $node->logo->id,
                    ),
                    'logo-image' => array(
                        'type'  => 'html',
                        'class' => 'inline avatar-image',
                        'html'  => is_object($node->logo) ?
                                   $node->logo . '<img src="'.SRC_URL.'/image/' . $node->logo->id . '/128/128" alt="Avatar" /><button class="image-remove" type="submit" name="logo-'.$node->logo->id.'-remove" title="Quitar este logo" value="remove">X</button>' :
                                   ''
                    )

                )
            ),

            'location' => array(
                'type'      => 'textbox',
                'size'      => 20,
                'title'     => Text::get('profile-field-location'),
                'hint'      => '',
                'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
                'ok'        => !empty($okeys['location']) ? array($okeys['location']) : array(),
                'value'     => $node->location
            ),

            'description' => array(
                'type'      => 'textarea',
                'cols'      => 40,
                'rows'      => 4,
                'title'     => Text::get('overview-field-description'),
                'hint'      => '',
                'errors'    => !empty($errors['description']) ? array($errors['description']) : array(),
                'value'     => $node->description
            )

        )

    ));
    ?>

</form>