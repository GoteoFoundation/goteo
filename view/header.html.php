<?php use Goteo\Library\Text; ?>
<div id="header">


    <?php include 'view/header/lang.html.php' ?>

    <h1><?php echo Text::get('regular-main-header'); ?></h1>

    <div id="super-header">

        <div id="about">
            <ul>
                <li><a href="/about"><?php echo Text::get('regular-header-about'); ?></a></li>
                <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li>
                <li><a href="/faq"><?php echo Text::get('regular-header-faq'); ?></a></li>
            </ul>
        </div>

        <?php include 'view/header/highlights.html.php' ?>

    </div>

    <?php include 'view/header/menu.html.php' ?>

    <?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

</div>