<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Entradas para la portada</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="?add">Nueva entrada</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <table>
                    <thead>
                        <tr>
                            <th>Título</th> <!-- title -->
                            <th>Posición</th> <!-- order -->
                            <td><!-- Move up --></td>
                            <td><!-- Move down --></td>
                            <td><!-- Edit --></td>
                            <td><!-- Remove --></td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['posts'] as $post) : ?>
                        <tr>
                            <td><?php echo $post->title; ?></td>
                            <td><?php echo $post->order; ?></td>
                            <td><a href="?up=<?php echo $post->id; ?>">[&uarr;]</a></td>
                            <td><a href="?down=<?php echo $post->id; ?>">[&darr;]</a></td>
                            <td><a href="?edit=<?php echo $post->id; ?>">[Editar]</a></td>
                            <td><a href="?remove=<?php echo $post->id; ?>">[Quitar]</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';