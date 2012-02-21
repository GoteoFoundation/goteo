<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$patrons = $this['patrons'];
?>
<div class="widget projects">

    <h2 class="title">Padrinos</h2>

    <?php foreach ($patrons as $patron) : ?>

            <?php echo new View('view/user/widget/patron.html.php', array(
                'user' => $patron
            )) ?>

    <?php endforeach ?>

</div>