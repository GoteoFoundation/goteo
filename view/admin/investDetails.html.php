<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Detalle de la transacción <?php echo $this['invest']->id; ?> </h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/accounting">Volver a las transacciones</a></p>

            <?php if (!empty($this['project'])) : ?>
                <h3><?php echo $this['project']->name . ' ' . $this['status'][$this['project']->status]; ?></h3>
                <p><?php echo '<pre>' . print_r($this['invest'], 1) . '</pre>'; ?></p>
                <?php foreach ($this['details'] as $point=>$data) {
                    echo "<p><h4>$point</h4>";
                    echo "<pre>" . print_r($data, 1) . "</pre></p>";
                } ?>
            <?php endif;?>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';