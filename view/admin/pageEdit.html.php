<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Editando la pagina '<?php echo $this['page']->name; ?>'</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/pages">Volver a la lista de páginas</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

            <?php echo '<pre>' . print_r($this['page'], 1) . '</pre>'; ?>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';