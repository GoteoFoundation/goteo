<?php
use Goteo\Library\Text;

$wordcount = $vars['wordcount'];

/* Secciones */
$sections = array(
    'texts' => array(
        'label'  => 'Textos interficie',
        'options' => Text::groups()
    ),
    'pages' => array(
        'label' => 'Páginas institucionales',
        'options' => array(
            'page' => 'Títulos',
            'page_node' => 'Contenidos',
        )
    ),
    'contents' => array(
        'label'   => 'Gestión de Textos y Traducciones',
        'options' => array (
            'post' => 'Blog',
            'faq' => 'FAQs',
            'category' => 'Categorias e Intereses',
            'license' => 'Licencias',
            'icon' => 'Tipos de Retorno',
            'tag' => 'Tags de blog',
            'criteria' => 'Criterios de revisión',
            'template' => 'Plantillas de email',
            'glossary' => 'Glosario',
            'info' => 'Ideas about',
            'worthcracy' => 'Meritocracia'
        )
    ),
    'home' => array(
        'label'   => 'Portada',
        'options' => array (
            'news' => 'Micronoticias',
            'promote' => 'Proyectos destacados'
        )
    )
);

// campos para las tablas que tienen diferentes campos
$fields = array(
  'category' => array('name','description'),
  'criteria' => array('title','description'),
  'post' => array('title','text','legend'),
  'template' => array('title','text'),
  'glossary' => array('title','text','legend'),
  'info' => array('title','text','legend'),
  'faq' => array('title','description'),
  'icon' => array('name','description'),
  'license' => array('name','description'),
  'news' => array('title','description'),
  'page' => array('name','description'),
  'page_node' => array('page','content'),
  'promote' => array('title','description'),
  'purpose' => array('purpose'),
  'tag' => array('name'),
  'worthcracy' => array('name'),
);

$total = 0;
?>
<div class="widget">
    <h3 class="title">Conteo de palabras</h3>
<?php foreach ($sections as $sCode=>$section) : ?>
        <h4><?php echo $section['label'] ?></h4>
        <table>
            <thead>
                <tr>
                    <th>Palabras</th>
                    <th>Seccion</th>
                    <th>Codigo</th>
                </tr>
            </thead>
            <?php foreach ($section['options'] as $oCode=>$option) :
                $_total = Text::wordCount($sCode,$oCode,$fields[$oCode], $total);
                if(is_null($_total)) continue;
                echo '<tr>
                    <td align="right">'.  $_total .'</td>
                    <td align="center">'.$option.'</td>
                    <td>'.$oCode.'</td>
                </tr>
                ';
            endforeach; ?>
        </table>
        <hr />
<?php endforeach; ?>
        <h4>Total: <?php echo $total ?> palabras</h4>
</div>
