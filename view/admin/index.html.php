<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Área administración</h2>

            <ul>
                <li><a href="/admin/checking">Revisión de proyectos</a></li>
                <li><a href="/admin/texts">Administración de textos</a></li>
                <li><a href="/admin/managing">Administración de usuarios y nodos</a></li>
                <li><a href="/admin/accounting">Administración de transacciones</a></li>
            </ul>
        </div>        

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
