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
                        <li class="accounting"><a href="/admin/accounting">Listado de aportes</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <h3>Detalles del aporte</h3>
            
            <p><?php echo '<pre>' . print_r($this['invest'], 1) . '</pre>'; ?></p>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';