<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

?>



<?php
            
$project = $this['project'];

// boton de revisar que no sirve para mucho
$buttons = array(
    array(
        'type'  => 'submit',
        'label' => 'Revisar',
        'class' => 'retry'
    )
);

// si es enviable ponemos el boton
if ($project->finishable) {
    $buttons[] = array(
        'type'  => 'submit',
        'label' => 'Enviar',
        'class' => 'confirm'
    );
}

// elementos generales de preview
$elements      = array(
    'process_preview' => array (
        'type' => 'hidden',
        'value' => 'preview'
    ),

    'preview' => array(
        'type'      => 'html',
        'class'     => 'fullwidth',
        'html'      =>   '<div class="project-preview" style="position: relative"><div>'
                       . '<div class="overlay" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0; z-index: 999"></div>'
                       . '<div style="z-index: 0">'
                       . new View('view/project/widget/support.html.php')
                       . new View('view/user/widget/user.html.php')
                       . new View('view/project/widget/media.html.php', array('project' => $project))
                       . new View('view/project/widget/share.html.php', array('project' => $project))
                       . new View('view/project/widget/summary.html.php', array('project' => $project))
                       . '</div>'
                       . '</div></div>'
    )
);

// si es enviable ponemos el campo de comentario
if ($project->finishable) {
    $elements['comment'] = array(
            'type'  =>'textarea',
            'title' => 'Notas adicionales para el administrador',
            'rows'  => 8,
            'cols'  => 100,
            'hint'  => Text::get('guide-project-comment')
        );
}

// lanzamos el superform
echo new SuperForm(array(
    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Previsualización',
    'hint'          => Text::get('guide-project-preview'),    
    'footer'        => $buttons,
    'elements'      => $elements
));