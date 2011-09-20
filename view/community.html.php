<?php
use Goteo\Library\Text,
    Goteo\Library\Feed;

$bodyClass = 'community about';

$items_goteo     = Feed::getAll('goteo', 'public');
$items_projects  = Feed::getAll('projects', 'public');
$items_community = Feed::getAll('community', 'public');

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

    <div id="sub-header">
        <div>
            <h2 style="margin-bottom:5px"><?php echo $this['description']; ?></h2>
        </div>
    </div>

    <div id="main">

<!--
        <div class="widget">
            <h3 class="title"><?php echo $this['name']; ?></h3>
            <?php echo $this['content']; ?>
        </div>
-->
        <div class="widget feed">
        	<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('.scroll-pane').jScrollPane({showArrows: true});
			});
			</script>
            <h3 class="title">Actividad reciente</h3>

            <div style="height:auto;overflow:auto;margin-left:15px">
            	
                <div class="block goteo">
                   <h4>Goteo</h4>
                   <div class="item scroll-pane">
                        <?php \trace($items_goteo); ?>
                   		<div class="subitem">
                            <div class="content-avatar">
                            	<a href="/user/olivier" class="avatar">
                                	<img src="/image/119/24/24/1" />
                                </a> 
                                <a href="/user/olivier" class="username">Olivier</a><br/>
                                <span class="datepub">Publicado hace 2 horas</span>
                            </div>
                            <div class="content-pub">
                            <span class="blue">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</span>, when an <a class="light-blue" href="#">unknown printer</a> took a galley of type and scrambled it to make a type specimen book. It has <span class="light-blue">Twittometro</span> not only five centuries, but also the leap into electronic typesetting.
                            </div>                           
                        </div>
                        <div class="subitem">
                            <div class="content-title">
                                <h5 class="light-blue">Felicitamos al proyecto<br/>Canal Alpha</h5>
                                <span class="datepub">Publicado hace 2 horas</span>
                           </div>
                           <div class="content-pub">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy <a href="#" class="blue">text ever since the 1500s</a>, when an unknown printer took a galley of type and <span class="red">scrambled it to make a type specimen book</span>. It has <a class="violet" href="#">survived not only </a>five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It <span class="light-blue">was popularised in the 1960s</span> with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                           </div>                           
                        </div>
                         <div class="subitem">
                            <div class="content-title">
                                <h5 class="light-blue">Felicitamos al proyecto<br/>Canal Alpha 2</h5>
                                <span class="datepub">Publicado hace 2 horas</span>
                           </div>
                           <div class="content-pub">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and <span class="red">scrambled it to make a type specimen book</span>. It has <a class="violet" href="#">survived not only </a>five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It <span class="light-blue">was popularised in the 1960s</span> with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                           </div>                           
                        </div>
                         <div class="subitem">
                            <div class="content-title">
                                <h5 class="light-blue">Felicitamos al proyecto<br/>Canal Alpha 3</h5>
                                <span class="datepub">Publicado hace 2 horas</span>
                           </div>
                           <div class="content-pub">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and <span class="red">scrambled it to make a type specimen book</span>. It has <a class="violet" href="#">survived not only </a>five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It <span class="light-blue">was popularised in the 1960s</span> with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                           </div>                           
                        </div>
                         <div class="subitem">
                            <div class="content-title">
                                <h5 class="light-blue">Felicitamos al proyecto<br/>Canal Alpha 4</h5>
                                <span class="datepub">Publicado hace 2 horas</span>
                           </div>
                           <div class="content-pub">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and <span class="red">scrambled it to make a type specimen book</span>. It has <a class="violet" href="#">survived not only </a>five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It <span class="light-blue">was popularised in the 1960s</span> with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                           </div>                           
                        </div>
                   </div>
                </div>
                
                <div class="block projects">
                	<h4>Proyectos</h4>  
                    <div class="item scroll-pane">
                        <?php \trace($items_projects); ?>
                        <div class="subitem">
                            <div class="content-image">
                                <a href="/user/olivier" class="image">
                                	<img src="/image/119/90/60/1" />
                                </a> 
                                <a href="/user/olivier" class="project light-blue">TodoJunto LetterPress</a><br/>
                                <span class="datepub">Publicado hace 2 horas</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's <span class="violet">standard dummy text ever since </span>the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen.
                            </div>                           
                        </div>
                        
                        <div class="subitem">                           
                           <span class="datepub">Publicado hace 2 horas</span>
                           <div class="content-pub">
                           Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
                           </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-image">
                                <a href="/user/olivier" class="image">
                                	<img src="/image/119/90/60/1" />
                                </a> 
                                <a href="/user/olivier" class="project light-blue">Canal Alpha</a><br/>
                                <span class="datepub">Publicado hace 2 horas</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's <span class="violet">standard dummy text ever since </span>the 1500s, when an unknown printer took a galley of type and scrambled it <span class="light-blue">to make a type specimen</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-image">
                                <a href="/user/olivier" class="image">
                                	<img src="/image/119/90/60/1" />
                                </a> 
                                <a href="/user/olivier" class="project light-blue">Canal Alpha</a><br/>
                                <span class="datepub">Publicado hace 2 horas</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's <span class="violet">standard dummy text ever since </span>the 1500s, when an unknown printer took a galley of type and scrambled it <span class="light-blue">to make a type specimen</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-image">
                                <a href="/user/olivier" class="image">
                                	<img src="/image/119/90/60/1" />
                                </a> 
                                <a href="/user/olivier" class="project light-blue">Canal Alpha</a><br/>
                                <span class="datepub">Publicado hace 2 horas</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's <span class="violet">standard dummy text ever since </span>the 1500s, when an unknown printer took a galley of type and scrambled it <span class="light-blue">to make a type specimen</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-image">
                                <a href="/user/olivier" class="image">
                                	<img src="/image/119/90/60/1" />
                                </a> 
                                <a href="/user/olivier" class="project light-blue">Canal Alpha</a><br/>
                                <span class="datepub">Publicado hace 2 horas</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's <span class="violet">standard dummy text ever since </span>the 1500s, when an unknown printer took a galley of type and scrambled it <span class="light-blue">to make a type specimen</span>.
                            </div>                           
                        </div>
                        
                        
                   </div>              
                </div>
                <div class="block community last">
                	<h4>Comunidad</h4>                
                    <div class="item scroll-pane">
                        <?php \trace($items_community); ?>
                        
                        <div class="subitem">                           
                           <span class="datepub">Publicado hace 2 horas</span>
                           <div class="content-pub">
                           Nuevo usuario en Goteo <span class="light-blue">Pepito Mendez</span>
                           </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-avatar">
                            	<a href="/user/olivier" class="avatar">
                                	<img src="/image/119/24/24/1" />
                                </a> 
                                <a href="/user/olivier" class="username">Andres P.</a><br/>
                                <span class="datepub">Publicado hace 2 d�as</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply <span class="light-blue">dummy </span>text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text <span class="grey">ever</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">                           
                           <span class="datepub">Publicado hace 2 horas</span>
                           <div class="content-pub">
                           Nuevo usuario en Goteo <span class="light-blue">Pepito Mendez</span>
                           </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-avatar">
                            	<a href="/user/olivier" class="avatar">
                                	<img src="/image/119/24/24/1" />
                                </a> 
                                <a href="/user/olivier" class="username">Andres P.</a><br/>
                                <span class="datepub">Publicado hace 2 d�as</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply <span class="green">dummy </span>text of the printing and typesetting industry. Lorem Ipsum has been the <span class="grey">industry's standard dummy text ever</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">                           
                           <span class="datepub">Publicado hace 2 horas</span>
                           <div class="content-pub">
                           Nuevo usuario en Goteo <span class="light-blue">Pepito Mendez</span>
                           </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-avatar">
                            	<a href="/user/olivier" class="avatar">
                                	<img src="/image/119/24/24/1" />
                                </a> 
                                <a href="/user/olivier" class="username">Andres P.</a><br/>
                                <span class="datepub">Publicado hace 2 d�as</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply <span class="light-blue">dummy </span>text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text <span class="grey">ever</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">                           
                           <span class="datepub">Publicado hace 2 horas</span>
                           <div class="content-pub">
                           Nuevo usuario en Goteo <span class="light-blue">Pepito Mendez</span>
                           </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-avatar">
                            	<a href="/user/olivier" class="avatar">
                                	<img src="/image/119/24/24/1" />
                                </a> 
                                <a href="/user/olivier" class="username">Andres P.</a><br/>
                                <span class="datepub">Publicado hace 2 d�as</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply <span class="green">dummy </span>text of the printing and typesetting industry. Lorem Ipsum has been the <span class="grey">industry's standard dummy text ever</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">                           
                           <span class="datepub">Publicado hace 2 horas</span>
                           <div class="content-pub">
                           Nuevo usuario en Goteo <span class="light-blue">Pepito Mendez</span>
                           </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-avatar">
                            	<a href="/user/olivier" class="avatar">
                                	<img src="/image/119/24/24/1" />
                                </a> 
                                <a href="/user/olivier" class="username">Andres P.</a><br/>
                                <span class="datepub">Publicado hace 2 d�as</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply <span class="light-blue">dummy </span>text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text <span class="grey">ever</span>.
                            </div>                           
                        </div>
                        
                        <div class="subitem">                           
                           <span class="datepub">Publicado hace 2 horas</span>
                           <div class="content-pub">
                           Nuevo usuario en Goteo <span class="light-blue">Pepito Mendez</span>
                           </div>                           
                        </div>
                        
                        <div class="subitem">
                            <div class="content-avatar">
                            	<a href="/user/olivier" class="avatar">
                                	<img src="/image/119/24/24/1" />
                                </a> 
                                <a href="/user/olivier" class="username">Andres P.</a><br/>
                                <span class="datepub">Publicado hace 2 d�as</span>
                            </div>
                            <div class="content-pub">
                            Lorem Ipsum is simply <span class="green">dummy </span>text of the printing and typesetting industry. Lorem Ipsum has been the <span class="grey">industry's standard dummy text ever</span>.
                            </div>                           
                        </div>
                        
                   </div>
                </div>
            </div>
            
        </div>

    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>