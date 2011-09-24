<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL,
    Goteo\Library\Feed;

$bodyClass = 'admin';

$message = '';
if (!empty($this['errors']) || !empty($this['success'])) {
    $message = '<div class="widget"><p>'.implode('<br />', $this['errors']).' '.implode('<br />', $this['success']).'</p></div>';
}

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de administración</h2>
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

                <?php if (ACL::check('/translate')) : ?>
                <div class="widget">
                    <?php echo new View ('view/admin/selector.html.php', $this); ?>
                </div>
                <?php endif; ?>

                <?php foreach ($this['menu'] as $sCode=>$section) : ?>
                <a name="<?php echo $sCode ?>"></a>
                <div class="widget board collapse">
                    <h3 class="title"><?php echo $section['label'] ?></h3>
                    <ul>
                        <?php foreach ($section['options'] as $oCode=>$option) :
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
