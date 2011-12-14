<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Call\call;

$call = $this['call'];

$projects = $this['projects'];

?>
<div class="widget gestrew">
    <div class="message">
        ESTO ES UNA VISUALIZACIÓN DE LOS PROYECTOS QUE RIEGAS.<br />
        LOS PROYECTOS QUE NO CONSIGAN EL MÍNIMO LIBERARÁN CAPITAL RIEGO
    </div>
    <?php foreach ($projects as $proj) : ?>
        <div class="widget">
            <?php echo $proj->name ?>
            <?php echo $proj->amount ?>
        </div>
    <?php endforeach; ?>
</div>