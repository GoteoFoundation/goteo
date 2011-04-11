<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Administración de transacciones</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <p><?php echo $this['content']; ?></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';