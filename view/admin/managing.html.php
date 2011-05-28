<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Administración de usuarios y nodos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <h3>Gestión de usuarios del nodo, administradores de nodos, usuarios normales y gestión de nodos</h3>
            <p>Pendiente de planificación</p>
            <?php echo \trace($this['users']); ?>
            <p>
                <?php foreach ($this['users'] as $user) : ?>
                    <a href="/user/<?php echo $user->id; ?>" target="_blank"><?php echo $user->name; ?></a><br />
                <?php endforeach; ?>
            </p>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';