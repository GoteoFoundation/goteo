<?php
    use Goteo\Library\Text,
        Goteo\Application\Lang;

// si es un nodo
if (NODE_ID != GOTEO_NODE) {
    include __DIR__ . '/node/header.html.php';
    return;
}

?>
<?php include __DIR__ . '/header/currency.html.php' ?>
<?php include __DIR__ . '/header/lang.html.php'; ?>


<div id="header">
    <h1><?php echo Text::get('regular-main-header'); ?></h1>
    <div id="super-header">
	   <?php include __DIR__ . '/header/highlights.html.php' ?>

	   <div id="rightside" style="float:right;">
           <div id="about">
                <ul>
                    <li><a href="/about"><?php echo Text::get('regular-header-about'); ?></a></li>
                    <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li>
                    <li><a href="/faq"><?php echo Text::get('regular-header-faq'); ?></a></li>
                    <?php if($num_currencies>1) { ?>
                    <li id="currency"><a href="#" ><?php echo $select_currency." ".$_SESSION['currency']; ?></a></li>
                    <?php } ?>
                    <li id="lang"><a href="#" ><?php echo Lang::getShort(); ?></a></li>
                </ul>
            </div>


		</div>


    </div>

    <?php include __DIR__ . '/header/menu.html.php' ?>

</div>

