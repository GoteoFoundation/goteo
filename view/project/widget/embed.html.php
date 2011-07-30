<?php
	use Goteo\Core\View;

	$url = SITE_URL . '/widget/project/' . $this['project']->id;

    if (isset($this['investor']) && is_object($this['investor'])) {
        $url .= '/invested/'.$this['investor']->id;
    }

	$widget_code = '<iframe frameborder="0" height="380px" src="'.$url.'" width="250px"></iframe>';
?>
<script type="text/javascript">
	// Mark DOM as javascript-enabled
	jQuery(document).ready(function ($) { 
		/*$("#code").click(function(){
			$("#code").focus();
			$("#code").select();					
		});*/
		
	});
</script>
<div id="project-code">
    <?php
        // el proyecto de trabajo
        echo new View('view/project/widget/project.html.php', $this);
    ?>
</div>
<div id="widget-code">
    <div class="wc-embed">CÓDIGO EMBED</div>
    <textarea id="code" onclick="this.focus();this.select()" readonly="readonly">
        <?php echo htmlentities($widget_code); ?>
    </textarea>
</div>
