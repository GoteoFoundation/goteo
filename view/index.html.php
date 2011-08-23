<?php 

use Goteo\Core\View,
    Goteo\Library\Text;

$currentPost = $this['posts'][$this['post']];

$bodyClass = 'home';

include 'view/prologue.html.php';
include 'view/header.html.php' ?>

    <script type="text/javascript">

    jQuery(document).ready(function ($) {

        $("#home-post-1").show();
        $("#navi-home-post-1").addClass('active');

        $(".navi-arrow").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-home-post").removeClass('active');
            $(".post").hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-home-post-"+this.rel).addClass('active');
            $("#home-post-"+this.rel).show();

            var prev;
            var next;

            if (this.id == 'home-navi-next') {
                prev = parseFloat($("#home-navi-prev").attr('rel')) - 1;
                next = parseFloat($("#home-navi-next").attr('rel')) + 1;
            } else {
                prev = parseFloat(this.rel) - 1;
                next = parseFloat(this.rel);
            }

            if (prev < 1) {
                prev = <?php echo count($this['posts']) ?>;
            }

            if (next > <?php echo count($this['posts']) ?>) {
                next = 1;
            }

            if (next < 1) {
                next = <?php echo count($this['posts']) ?>;
            }

            if (prev > <?php echo count($this['posts']) ?>) {
                prev = 1;
            }

            $("#home-navi-prev").attr('rel', prev);
            $("#home-navi-next").attr('rel', next);
        });

        $(".navi-home-post").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-home-post").removeClass('active');
            $(".post").hide();
            /* Poner acctive a este, mostrar este*/
            $(this).addClass('active');
            $("#"+this.rel).show();
        });

    });
    </script>
		<script>
			$(function(){
				$('#sub-header').slides();
			});
		</script>
        <div id="sub-header" class="banners">
			<div class="clearfix">
				<div class="slides_container">
					<!-- Módulo de texto más sign in -->
					<div class="subhead-banner">
						<h2 class="message">Red social para <span class="greenblue">cofinanciar y colaborar con</span><br /> proyectos creativos que fomentan el procomún<br /> ¿Tienes un proyecto con <span class="greenblue">adn abierto</span>?</h2>
                        <a href="/contact" class="button banner-button">Contáctanos</a>
<!--						<ul class="sign-in-with">
							<li>Accede con facebook</li>
							<li>Accede con Twitter</li>
							<li>Accede con Open ID</li>
						</ul> -->
					</div>
					<!-- Módulo banner imagen más resumen proyecto -->
					<div class="subhead-banner">
                    	
                        <a href="#meter-aqui-enlace-proyecto" class="expand">&nbsp;</a>
						<div class="shb-info clearfix">	                       
							<h2>Todojunto Letter Press Press Press</h2>
							<small>Por: Todojunto</small>
							<div class="col-return clearfix">
								<h3>Retorno colectivo</h3>
								<p>Se haran manuales de como montar imprimir con tecnicas artesanales</p>
								<ul>
									<li><img src="view/css/icon/s/design.png" alt="design" /></li>
									<li><img src="view/css/icon/s/other.png" alt="other" /></li>
									<li><img src="view/css/icon/s/money.png" alt="rewards" /></li>
								</ul>
								<div class="license"><img src="view/css/license/ccbync.png" alt="ccbync" /></div>
							</div>
							<ul class="financ-meter">
								<li>OBTENIDO</li>
								<li class="reached">200 <span class="euro">€</span></li>
								<li>DE</li>
								<li class="optimun">3000 <span class="euro">€</span></li>
								<li>QUEDAN</li>
								<li class="days">2 días</li>
							</ul>
						</div>
						<div class="shb-img"></div>
					</div>
				</div>
				<div class="mod-pojctopen"><?php echo Text::html('open-banner-header'); ?></div>
			</div>
			<div class="sliderbanners-ctrl">
				<a class="prev">prev</a>
				<ul class="paginacion"></ul>
				<a class="next">next</a>
			</div>
        </div>
        <div id="main">
            
            <?php if (!empty($this['posts'])): ?>
            
            <div class="widget learn">
                
            <h2 class="title"><?php echo Text::get('home-posts-header'); ?></h2>
            
                <?php $i = 1; foreach ($this['posts'] as $post) : ?>
                <div class="post" id="home-post-<?php echo $i; ?>">
                    <h3><?php echo $post->title; ?></h3>
                    <?php if (!empty($post->media->url)) : ?>
                        <div class="embed">
                            <?php echo $post->media->getEmbedCode(); ?>
                        </div>
                    <?php elseif (!empty($post->image)) : ?>
                        <div class="image">
                            <img src="/image/<?php echo $post->image->id; ?>/500/285" alt="Imagen"/>
                        </div>
                    <?php endif; ?>
                    
                    <div class="description">
                        <?php echo Text::recorta(nl2br(Text::urlink($post->text)), 500) ?>
                    </div>

                    <div class="read_more"><a href="/blog/<?php echo $post->id; ?>"><?php echo Text::get('regular-read_more') ?></a></div>
                </div>                
                <?php $i++; endforeach; ?>
               
                <ul>
        			<li class="prev"><a href="#" id="home-navi-prev" rel="<?php echo count($this['posts']) ?>" class="navi-arrow">Anterior</a></li>
                    <?php $i = 1; foreach ($this['posts'] as $post) : ?>
                    <li class="navi"><a href="#" id="navi-home-post-<?php echo $i ?>" rel="home-post-<?php echo $i ?>" class="tipsy navi-home-post" title="<?php echo htmlspecialchars($post->title) ?>">
                        <?php echo htmlspecialchars($post->title) ?></a>
                    </li>
                    <?php $i++; endforeach ?>
        			<li class="next"><a href="#" id="home-navi-next" rel="2" class="navi-arrow">Siguiente</a></li>
                </ul>

            </div>
            
            <?php endif ?>
            
            <div class="widget projects">
                
                <h2 class="title"><?php echo Text::get('home-promotes-header'); ?></h2>
            
                <?php foreach ($this['promotes'] as $promo) : ?>
                
                        <?php echo new View('view/project/widget/project.html.php', array(
                            'project' => $promo->projectData,
                            'balloon' => '<h4>' . htmlspecialchars($promo->title) . '</h4>' .
                                         '<blockquote>' . $promo->description . '</blockquote>'
                        )) ?>
                    
                <?php endforeach ?>

            </div>

        </div>

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>