<?php
	use Goteo\Core\View;
	echo new View ('view/dashboard/projects/selector.html.php', $this);
	$url = SITE_URL . '/widget/project/' . $_SESSION['project']->id;
	$widget_code = '<iframe frameborder="0" height="380px" src="'.$url.'" width="200px"></iframe>';
?>
<p>SOLO DEBE PODER COMPARTIR ESTE WIDGET SI EL PROYECTO ESTA en campaña, financiado o retorno cumplido</p>
<div class="widget projects">
    <h2 class="widget-title">PUBLICA EL WIDGET DEL PROYECTO</h2>
	<div class="widget-porject-legend">Copia y pega el código en tu web o blog y ayuda a difundir este proyecto</div>
    <div>
        <?php
			// el proyecto de trabajo
			echo new View('view/project/widget/project.html.php', array(
            'project'   => $this['project']
			)); 
		?>
    </div>
    <div id="widget-code">
        <div class="wc-embed">CÓDIGO EMBED</div> 
        <blockquote>
			<?php echo htmlentities($widget_code); ?>       
        </blockquote>
        <p><a href="<?php echo $url; ?>" target="_blank">Preview</a></p>
    </div>
</div>

