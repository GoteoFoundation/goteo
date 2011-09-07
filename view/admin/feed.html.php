<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL,
    Goteo\Library\Feed;

$bodyClass = 'admin';

$feed = empty($_GET['feed']) ? 'all' : $_GET['feed'];

$items = Feed::getAll($feed, 'admin');

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

        <div id="main">

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
                    <h3 class="title">actividad reciente</h3>
                    Ver Feeds por:

                    <p class="categories">
                        <?php foreach (Feed::$admin_types as $id=>$cat) : ?>
                        <a href="/admin/feed/?feed=<?php echo $id ?>" <?php echo ($feed == $id) ? 'class="'.$cat['color'].'"': 'class="hov" rel="'.$cat['color'].'"' ?>><?php echo $cat['label'] ?></a>
                        <?php endforeach; ?>
                    </p>

                    <?php echo new View('view/admin/feed/list.html.php', array('items' => $items)); ?>

            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
