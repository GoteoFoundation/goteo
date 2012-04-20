<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$icons  = $this['icons'];
?>
<!-- funcion jquery para mostrar uno y ocultar el resto
si no es $this['hide_promotes']  vamos a mostrar el promotes y ponerlo como current
-->
<script type="text/javascript">
    $(function(){
        $(".show_cat").click(function (event) {
            event.preventDefault();

            if ($("#node-projects-"+$(this).attr('href')).is(":visible")) {
                $(".button").removeClass('current');
                $(".rewards").removeClass('current');
                $(".node-projects").hide();
            } else {
                $(".button").removeClass('current');
                $(".rewards").removeClass('current');
                $(".node-projects").hide();
                $(this).parents('div').addClass('current');
                $("#node-projects-"+$(this).attr('href')).show();
            }

        });
    });


</script>
<div class="side_widget">
    <?php foreach ($this['searcher'] as $cat => $label) :
        if ($cat == 'byreward') : ?>
    <div class="block rewards rounded-corners">
        <p class="title"><?php echo $label ?></p>
        <p class="items">
            <?php foreach ($this['discover']['byreward'] as $icon=>$projs) : ?>
        	<a href="<?php echo $cat . '-' . $icon ?>" class="show_cat tipsy <?php echo $icon ?>" title="<?php echo $icons[$icon]->name ?>"><?php echo $icons['file']->name ?></a>
            <?php endforeach; ?>
    </div>
        <?php else:  ?>
    <div class="line button rounded-corners<?php if ($cat == 'promote' && !$this['hide_promotes']) echo ' current' ?>">
        <p><a href="<?php echo $cat ?>" class="show_cat"><?php echo $label ?></a></p>
    </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>
