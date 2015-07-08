<?php
    use Goteo\Library\Text,
        Goteo\Application\Lang;
?>

<?php include __DIR__ . '/../../../../app/view/header/lang.html.php' ?>

<div id="header">
    <h1><?php echo Text::get('regular-main-header'); ?></h1>

    <div id="super-header">
		<div id="menu">
			<ul>
				<li class="home"><a href="/">Inicio</a></li>
                <li class="campanya">: <?php echo Text::get('call_header_riego'); ?> - <?php echo str_replace('-', ' ', $call->id) ?></li>
			</ul>
		</div>

        <div id="rightside">

        <ul class="menu-right">
            <li class="capital">
                <a href="/service/resources" target="_blank"><?php echo Text::get('call_header_whats_riego'); ?></a>
            </li>
            <?php if (!empty($_SESSION['user'])): ?>
            <li class="dashboard"><a href="/dashboard"><span><?php echo Text::get('dashboard-menu-main'); ?></span><img src="<?php echo $_SESSION['user']->avatar->getLink(28, 28, true); ?>" /></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/activity"><span><?php echo Text::get('dashboard-menu-activity'); ?></span></a></li>
                        <li><a href="/dashboard/profile"><span><?php echo Text::get('dashboard-menu-profile'); ?></span></a></li>
                        <li><a href="/dashboard/projects"><span><?php echo Text::get('dashboard-menu-projects'); ?></span></a></li>

                        <?php if ( isset($_SESSION['user']->roles['caller']) ) : ?>
                        <li><a href="/dashboard/calls"><span><?php echo Text::get('dashboard-menu-calls'); ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['translator']) ||  isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) ) : ?>
                        <li><a href="/translate"><span><?php echo Text::get('regular-translate_board'); ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['checker']) ) : ?>
                        <li><a href="/review"><span><?php echo Text::get('regular-review_board'); ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) ) : ?>
                        <li><a href="/admin"><span><?php echo Text::get('regular-admin_board'); ?></span></a></li>
                        <?php endif; ?>

                        <li class="logout"><a href="/user/logout"><span><?php echo Text::get('regular-logout'); ?></span></a></li>
                    </ul>
                </div>
            </li>
            <?php else: ?>
            <li class="login">
                <a href="/user/login?return=<?php echo $_SERVER['REQUEST_URI'] ?>"><?php echo Text::get('regular-login'); ?></a>
            </li>
            <?php endif ?>

			<li id="lang">
				<a href="#" ><?php echo Lang::getShort(); ?></a>
			</li>
		</ul>

		<script type="text/javascript">
		jQuery(document).ready(function ($) {
			 $("#lang").hover(function(){
               // posicionar idiomas
               var pos = $("#lang").position();
               var top = $("#wrapper").scrollTop();
               $("ul.lang").css("left", pos.left+5);
               $("ul.lang").css("top", top+25);

			   //desplegar idiomas
			   try{clearTimeout(TID_LANG)}catch(e){};
			   $("ul.lang").fadeIn();
		       $("#lang").css("background","url('<?php echo SRC_URL; ?>/view/css/bolita.png') 4px 5px no-repeat #808285");

		   },function() {
			   TID_LANG = setTimeout('$("ul.lang").hide()',100);
			});
			$('ul.lang').hover(function(){
				try{clearTimeout(TID_LANG)}catch(e){};
			},function() {
			   TID_LANG = setTimeout('$("ul.lang").hide()',100);
			   $("#lang").css("background","url('<?php echo SRC_URL; ?>/view/css/bolita_gris.png') 4px 5px no-repeat transparent");
			});


		});
		</script>

	   </div>
	</div>

</div>
