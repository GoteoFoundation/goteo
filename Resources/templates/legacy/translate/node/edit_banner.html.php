<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\NormalForm,
    Goteo\Model;

$node = $vars['node'];
$id   = $vars['id'];

$original = Model\Banner::get($id);
$data     = Model\Banner::get($id, $_SESSION['translate_lang']);

$bodyClass = 'admin';
?>
<form method="post" action="/translate/node/<?php echo $node ?>/banner/edit/<?php echo $id ?>" class="project" >
<?php echo new NormalForm(array(
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    'elements'      => array(

        'lang' => array(
            'type'      => 'hidden',
            'value'     => $_SESSION['translate_lang']
        ),

        'title-orig' => array(
            'type'      => 'html',
            'title'     => 'Título',
            'html'     => $original->title
        ),
        'title' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $data->title
        ),

        'description-orig' => array(
            'type'      => 'html',
            'title'     => 'Texto',
            'html'     => $original->description
        ),
        'description' => array(
            'type'      => 'textarea',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $data->description
        )

    )

));
?>
</form>
