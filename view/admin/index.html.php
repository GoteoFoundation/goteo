<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL,
    Goteo\Library\Feed,
    Goteo\Model\Node;

if (LANG != 'es') {
    header('Location: /admin/?lang=es');
}

// piñoncete para Diego
$allowed_contents = array(
    'base',
    'blog',
    'texts',
    'faq',
    'pages',
    'licenses',
    'icons',
    'tags',
    'criteria',
    'templates',
    'glossary',
    'info',
    'mailing' // para testeo newsletter
);

if ($_SESSION['user']->id == 'diegobus'
    && !empty($this['folder'])
    && !in_array($this['folder'], $allowed_contents)) {

    header('Location: /admin/');
}

// piñoncete para Mercè
$merx_allowed_contents = array(
    'users',
    'invests',
    'accounts'
);

if ($_SESSION['user']->id == 'merxxx'
    && !empty($this['folder'])
    && !in_array($this['folder'], $merx_allowed_contents)) {

    header('Location: /admin/');
}

if (isset($_SESSION['admin_node'])) {
    $nodeData = Node::get($_SESSION['admin_node']);
    $node_title = ' del nodo <span style="color:#20B2B3;">'.$nodeData->name.'</span>';
    $this['nodeData'] = $nodeData;
}


$bodyClass = 'admin';

$message = '';
if (!empty($this['errors']) || !empty($this['success'])) {
    $message = '<div class="widget"><p>'.implode('<br />', $this['errors']).' '.implode('<br />', $this['success']).'</p></div>';
}

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de administración<?php echo $node_title; ?></h2>
                <?php if (defined('ADMIN_BCPATH')) : ?>
                <blockquote><?php echo ADMIN_BCPATH; ?></blockquote>
                <?php endif; ?>
            </div>
        </div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

        <?php if (!empty($this['folder']) && !empty($this['file'])) : ?>

        <div id="main">

            <?php echo $message; ?>
            
<?php
                if ($this['folder'] == 'base') {
                    $path = 'view/admin/'.$this['file'].'.html.php';
                } else {
                    $path = 'view/admin/'.$this['folder'].'/'.$this['file'].'.html.php';
                }

                echo new View ($path, $this);
?>

<?php   else :

    // estamos en la portada, sacamos el feed
    $feed = empty($_GET['feed']) ? 'all' : $_GET['feed'];
    $items = Feed::getAll($feed, 'admin');
    ?>
        <div id="main">

            <div class="center">

                <?php echo $message; ?>

                <?php foreach ($this['menu'] as $sCode=>$section) :
                    // piñoncete para Diego
                    if ($_SESSION['user']->id == 'diegobus') {
                        if (!in_array($sCode, array('users','contents')))  {
                             continue;
                        }
                    }
                    // piñoncete para Mercè
                    if ($_SESSION['user']->id == 'merxxx') {
                        if (!in_array($sCode, array('users','accounting')))  {
                             continue;
                        }
                    }
                    ?>
                <a name="<?php echo $sCode ?>"></a>
                <div class="widget board collapse">
                    <h3 class="title"><?php echo $section['label'] ?></h3>
                    <ul>
                        <?php foreach ($section['options'] as $oCode=>$option) :
                    // re piñoncete para Diego, en seccion users solamente 'mailing'
                    if ($_SESSION['user']->id == 'diegobus') {
                        if ($sCode == 'users' && $oCode != 'mailing')  {
                             continue;
                        }
                    }
                    // re piñoncete para Mercè, en seccion users no ve la gestion de meritocracia
                    if ($_SESSION['user']->id == 'merxxx') {
                        if ($sCode == 'users' && $oCode != 'users')  {
                             continue;
                        }
                    }
                            echo '<li><a href="/admin/'.$oCode.'">'.$option['label'].'</a></li>
                                ';
                        endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>


            <div class="side">
                <a name="feed"></a>
                <div class="widget feed">
					<script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $('.scroll-pane').jScrollPane({showArrows: true});

                        $('.hov').hover(
                          function () {
                            $(this).addClass($(this).attr('rel'));
                          },
                          function () {
                            $(this).removeClass($(this).attr('rel'));
                          }
                        );

                    });
                    </script>
                    <h3>actividad reciente</h3>
                    Ver Feeds por:

                    <p class="categories">
                        <?php foreach (Feed::$admin_types as $id=>$cat) : ?>
                        <a href="/admin/?feed=<?php echo $id ?>#feed" <?php echo ($feed == $id) ? 'class="'.$cat['color'].'"': 'class="hov" rel="'.$cat['color'].'"' ?>><?php echo $cat['label'] ?></a>
                        <?php endforeach; ?>
                    </p>

                    <?php echo new View('view/admin/feed/list.html.php', array('items' => $items)); ?>

                    <a href="/admin/feed/<?php echo isset($_GET['feed']) ? '?feed='.$_GET['feed'] : ''; ?>" style="margin-top:10px;float:right;text-transform:uppercase">Ver más</a>
                    
                </div>
            </div>


            <?php endif; ?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
