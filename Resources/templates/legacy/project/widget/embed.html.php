<?php
	use Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Model\Project;

    $URL = \SITE_URL;

    $project = $vars['project'];

	$url = $URL . '/widget/project/' . $vars['project']->id;
    if (LANG != 'es')
        $url .= '?lang=' . LANG;

    if (isset($vars['investor']) && is_object($vars['investor'])) {
        $url .= '/invested/'.$vars['investor']->id;
    }

	$widget_code = Text::widget($url);

/*
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    // Mark DOM as javascript-enabled
    jQuery(document).ready(function ($) {
        $("#code").click(function(){
            $("#code").focus();
            $("#code").select();
        });

    });
// @license-end
</script>
*/
?>
<div id="project-code">
    <?php
        // el proyecto de trabajo
        echo View::get('project/widget/project.html.php', array('project'=>$project));
    ?>
</div>
<div id="widget-code">
    <div class="wc-embed" onclick="$('#code').focus();$('#code').select()"><?php echo Text::get('project-spread-embed_code'); ?></div>
    <textarea id="code" onclick="this.focus();this.select()" readonly="readonly"><?php echo htmlentities($widget_code); ?></textarea>
</div>
