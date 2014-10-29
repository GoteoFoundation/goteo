<?php
    use Goteo\Library\Text,
        Goteo\Library\Lang;

// si es un nodo
if (NODE_ID != GOTEO_NODE) {
    include 'view/node/header.html.php';
    return;
}

?>
<?php include 'view/header/lang.html.php' ?> 


<div id="header">
    <h1><?php echo Text::get('regular-main-header'); ?></h1>
    <div id="super-header">
	   <?php include 'view/header/highlights.html.php' ?>
    
	   <div id="rightside" style="float:right;">
           <div id="about">
                <ul>
                    <li><a href="/about"><?php echo Text::get('regular-header-about'); ?></a></li>
                    <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li>
                    <li><a href="/faq"><?php echo Text::get('regular-header-faq'); ?></a></li>  
                    <li id="lang"><a href="#" ><?php echo Lang::get(LANG)->short ?></a></li>                               
                </ul>
            </div>
            
            
		</div>
     

    </div>

    <?php include 'view/header/menu.html.php' ?>

</div>

