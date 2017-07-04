<?php

use Goteo\Library\NormalForm;

$node = $this->node;

$colors = array(
    array(
        'value'     => 'white',
        'label'     => 'Blanco'
        ),
    array(
        'value'     => 'grey',
        'label'     => 'Gris'
        )
);

?>
<?php $this->layout('admin/node/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/node/edit" enctype="multipart/form-data">

    <?php echo new NormalForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'title'         => '',
        'class'         => 'aqua',
        'footer'        => array(
            'view-step-preview' => array(
                'type'  => 'submit',
                'name'  => 'save-node',
                'label' => $this->text('regular-save'),
                'class' => 'next'
            )
        ),
        'elements'      => array(
            'name' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'Nombre',
                'value'     => $node->name,
            ),
            'subtitle' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'Subtítulo',
                'value'     => $node->subtitle,
            ),

            'description' => array(
                'type'      => 'TextArea',
                'cols'      => 40,
                'rows'      => 4,
                'title'     => 'Presentación',
                'value'     => $node->description
            ),

            'logo' => array(
                'type' => 'Hidden',
                'value' => $node->logo->id,
            ),

            'thelogo' => array(
                'type'      => 'group',
                'title'     => 'Logo',
                'class'     => 'user_avatar',
                'children'  => array(
                    'logo_upload'    => array(
                        'type'  => 'file',
                        'label' => $this->text('form-image_upload-button'),
                        'class' => 'inline avatar_upload'
                    ),
                    'logo-image' => array(
                        'type'  => 'HTML',
                        'class' => 'inline avatar-image',
                        'html'  => is_object($node->logo) ?
                                    '<img src="' . '/img/small/' . $node->logo->id . '" alt="Avatar" title="'.$node->logo->id.'" /><button class="image-remove" type="submit" name="logo-'.$node->logo->hash.'-remove" title="Quitar este logo" value="remove">X</button>' :
                                   ''
                    )

                )
            ),

            'label' => array(
                'type' => 'Hidden',
                'value' => $node->label->id,
            ),

            'thelabel' => array(
                'type'      => 'group',
                'title'     => 'Sello',
                'class'     => 'user_avatar',
                'children'  => array(
                    'label_upload'    => array(
                        'type'  => 'file',
                        'label' => $this->text('form-image_upload-button'),
                        'class' => 'inline avatar_upload'
                    ),
                    'label-image' => array(
                        'type'  => 'HTML',
                        'class' => 'inline avatar-image',
                        'html'  => is_object($node->label) ?
                                   '<img src="' . '/img/small/' . $node->label->id . '" alt="Avatar"  title="'.$node->label->id.'"  /><button class="image-remove" type="submit" name="label-'.$node->label->hash.'-remove" title="Quitar este sello" value="remove">X</button>' :
                                   ''
                    )

                )
            ),

            'homeimg' => array(
                'type' => 'Hidden',
                'value' => $node->home_img->id,
            ),

            'thehomeimg' => array(
                'type'      => 'group',
                'title'     => 'Imagen home',
                'class'     => 'user_avatar',
                'children'  => array(
                    'homeimg_upload'    => array(
                        'type'  => 'file',
                        'label' => $this->text('form-image_upload-button'),
                        'class' => 'inline avatar_upload'
                    ),
                    'homeimg-image' => array(
                        'type'  => 'HTML',
                        'class' => 'inline avatar-image',
                        'html'  => is_object($node->home_img) ?
                                   '<img src="' . '/img/small/' . $node->home_img->id . '" alt="Avatar"  title="'.$node->home_img->id.'"  /><button class="image-remove" type="submit" name="homeimg-'.$node->home_img->hash.'-remove" title="Quitar esta imagen de portada" value="remove">X</button>' :
                                   ''
                    )

                )
            ),



            'owner_background' => array(
                'type'      => 'TextBox',
                'size'      => 10,
                'title'     => 'Background color (Hex)',
                'value'     => $node->owner_background,
            ),

            'owner_font_color' => array(
                'type'      => 'TextBox',
                'size'      => 10,
                'title'     => 'Color de fuente (Hex)',
                'value'     => $node->owner_font_color,
            ),
            'owner_social_color' => array(
                'title'     => 'Color iconos sociales',
                'type'      => 'Slider',
                'options'   => $colors,
                'class'     => 'currently cols_' . count($allow),
                'value'     => $node->owner_social_color
            ),

            'twitter' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'Twitter',
                'value'     => $node->twitter,
            ),

            'facebook' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'Facebook',
                'value'     => $node->facebook,
            ),

            'google' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'Google +',
                'value'     => $node->google,
            ),

            'linkedin' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'LinkedIn',
                'value'     => $node->linkedin,
            ),

            'location' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'Localización',
                'value'     => $node->location
            )

        )

    ));
    ?>

    </form>

<?php $this->replace() ?>
