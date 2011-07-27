<?php
	use Goteo\Core\View;
?>
<div class="widget projects">
    <h2 class="widget-title">PUBLICA EL WIDGET DEL PROYECTO</h2>
	<div class="widget-porject-legend">Copia y pega el código en tu web o blog y ayuda a difundir este proyecto</div>
    <?php echo new View('view/project/widget/embed.html.php', array('project'=>$this['project'])) ?>
</div>

