<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

echo new View('view/project/widget/support.html.php'),
     new View('view/user/widget/user.html.php'),
     new View('view/project/widget/media.html.php', array('project' => $project)),
     new View('view/project/widget/share.html.php', array('project' => $project)),
     new View('view/project/widget/summary.html.php', array('project' => $project));
            
$project = $this['project'];

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Previsualización',
    'hint'          => Text::get('guide-project-preview'),    
    'class'         => 'aqua',
    'footer'        => array(
        array(
            'type'  => 'submit',
            'label' => 'Revisar',
            'class' => 'retry'
        ),
        array(
            'type'  => 'submit',
            'label' => 'Enviar',
            'class' => 'confirm'
        )
    ),    
    'elements'      => array(
        
        'preview' => array(
            'type'      => 'group'
                  
        )
        
    )

));