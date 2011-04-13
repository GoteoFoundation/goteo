<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Administración de usuarios y nodos</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <p>
                Gestión de usuarios del nodo, administradores de nodos, usuarios normales y gestión de nodos
            </p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

            <ul>
            <?php foreach ($this['users'] as $user) : ?>
                <li>
                    <label><?php echo $user->name; ?>:</label>
                    <a href="/user/<?php echo $user->id; ?>" target="_blank">[Ver]</a>
                </li>
            <?php endforeach; ?>
            </ul>


        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';