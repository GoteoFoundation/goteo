<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Core\ACL;

$bodyClass = 'admin';

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
                <a name="feed"></a>
                FEED Central
                <p>
                    <a href="/admin/feed/?feed=all#feed">TODO</a> <a href="/admin/feed/?feed=admin#feed">ADMINISTRADOR</a> <a href="/admin/feed/?feed=user#feed">USUARIO</a>
                    <br /><br />
                    Ver Feeds <?php echo $_GET['feed']; ?>
                </p>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
