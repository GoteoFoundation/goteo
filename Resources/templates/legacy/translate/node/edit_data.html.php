<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\NormalForm,
    Goteo\Model;

$node = $vars['node'];

$original = Model\Node::get($node);
$data = Model\Node::get($node, $_SESSION['translate_lang']);

$bodyClass = 'admin';
?>
<form method="post" action="/translate/node/<?php echo $node ?>/data/edit/<?php echo $node ?>" class="project" >
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

        'subtitle-orig' => array(
            'type'      => 'html',
            'title'     => 'Título',
            'html'     => $original->subtitle
        ),
        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $data->subtitle
        ),

        'description-orig' => array(
            'type'      => 'html',
            'title'     => 'Presentación',
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
