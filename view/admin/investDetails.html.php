<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Administración de transacciones</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/accounting">Transacciones</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <h3>Detalle de la transacción <?php echo $this['invest']->id; ?> </h3>

            <?php if (!empty($this['project'])) : ?>
                <h4><?php echo $this['project']->name . ' ' . $this['status'][$this['project']->status]; ?></h4>
                <p><?php echo '<pre>' . print_r($this['invest'], 1) . '</pre>'; ?></p>
                <?php foreach ($this['details'] as $point=>$data) {
                    echo "<p>$point<pre>" . print_r($data, 1) . "</pre></p>";
                } ?>
            <?php endif;?>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';