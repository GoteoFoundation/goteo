<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Entradas para la portada</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

            <?php if (!empty($this['success'])) : ?>
                <p><span style="color:green;"><?php echo $this['success']; ?></span><br /></p>
            <?php endif;?>

            <p><a href="?add">Añadir</a></p>

                    <table>
                        <thead>
                            <tr>
                                <td>Título</td> <!-- title -->
                                <td>Posición</td> <!-- order -->
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

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';