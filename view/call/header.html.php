<?php 
    use Goteo\Library\Text,
        Goteo\Library\Lang,
        Goteo\Core\ACL;
?>

<?php include 'view/header/lang.html.php' ?> 


<div id="header">
    <h1><?php echo Text::get('regular-main-header'); ?></h1>

    <div id="super-header">    
		<div id="menu">
			<ul>
				<li class="home"><a href="/">Inicio</a></li>
				<li class="campanya">: Capital Riego - <?php echo $call->name ?></li>
			</ul>
		</div>
	   <div id="rightside">
		
		<ul>
            <li class="capital">
                <a href="/service/resources">Que es capital riego?</a>
            </li>
            <?php if (!empty($_SESSION['user'])): ?>
            <li class="dashboard"><a href="/dashboard"><span><?php echo Text::get('dashboard-menu-main'); ?></span><img src="<?php echo $_SESSION['user']->avatar->getLink(28, 28, true); ?>" /></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/activity"><span><?php echo Text::get('dashboard-menu-activity'); ?></span></a></li>
                        <li><a href="/dashboard/profile"><span><?php echo Text::get('dashboard-menu-profile'); ?></span></a></li>
                        <li><a href="/dashboard/projects"><span><?php echo Text::get('dashboard-menu-projects'); ?></span></a></li>
                        <?php if (ACL::check('/call/create')) : ?>
                        <li><a href="/dashboard/calls"><span><?php echo Text::get('dashboard-menu-calls'); ?></span></a></li>
                        <?php endif; ?>
                        <?php if (ACL::check('/translate')) : ?>
                        <li><a href="/translate"><span><?php echo Text::get('regular-translate_board'); ?></span></a></li>
                        <?php endif; ?>
                        <?php if (ACL::check('/review')) : ?>
                        <li><a href="/review"><span><?php echo Text::get('regular-review_board'); ?></span></a></li>
                        <?php endif; ?>
                        <?php if (ACL::check('/admin')) : ?>
                        <li><a href="/admin"><span><?php echo Text::get('regular-admin_board'); ?></span></a></li>
                        <?php endif; ?>
                        <li class="logout"><a href="/user/logout"><span><?php echo Text::get('regular-logout'); ?></span></a></li>
                    </ul>
                </div>
            </li>
            <?php else: ?>
            <li class="login">
                <a href="/user/login"><?php echo Text::get('regular-login'); ?></a>
            </li>
            <?php endif ?>
            
			<li id="lang">
				<a href="#" ><?php echo Lang::get(LANG)->short ?></a>
			</li>
		</ul>

		<script type="text/javascript">
		jQuery(document).ready(function ($) {
			 $("#lang").hover(function(){
			   //desplegar idiomas
			   try{clearTimeout(TID_LANG)}catch(e){};
			   var pos = $(this).offset().left;
			   $('ul.lang').css({left:pos+'px'});
			   $("ul.lang").fadeIn();
		       $("#lang").css("background","#808285 url('/view/css/bolita.png') 4px 7px no-repeat");

		   },function() {
			   TID_LANG = setTimeout('$("ul.lang").hide()',100);
			});
			$('ul.lang').hover(function(){
				try{clearTimeout(TID_LANG)}catch(e){};
			},function() {
			   TID_LANG = setTimeout('$("ul.lang").hide()',100);
			   $("#lang").css("background","#59595C url('/view/css/bolita.png') 4px 7px no-repeat");
			});


		});
		</script>
		
	   </div>
	</div>

</div>
