<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\NormalForm;

$node = $this['node'];
$errors = $this['errors'];

$original = \Goteo\Model\node::get($node->id);
?>
<form method="post" action="/dashboard/translates/overview/save" class="project" >
<?php echo new NormalForm(array(
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-node_data',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    'elements'      => array(

        'subtitle-orig' => array(
            'type'      => 'html',
            'title'     => 'Título',
            'html'     => $original->subtitle
        ),
        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $node->subtitle
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
            'value'     => $node->description
        )

    )

));
?>
</form>