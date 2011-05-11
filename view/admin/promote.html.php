<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Proyectos destacados</h2>

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
                                <td>Proyecto</td> <!-- preview -->
                                <td>Título</td> <!-- title -->
                                <td>Posición</td> <!-- order -->
                                <td><!-- Move up --></td>
                                <td><!-- Move down --></td>
                                <td><!-- Edit --></td>
                                <td><!-- Remove --></td>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($this['promoted'] as $promo) : ?>
                            <tr>
                                <td><a href="/project/<?php echo $promo->project; ?>" target="_blank" title="Preview"><?php echo $promo->name; ?></a></td>
                                <td><?php echo $promo->title; ?></td>
                                <td><?php echo $promo->order; ?></td>
                                <td><a href="?up=<?php echo $promo->project; ?>">[&uarr;]</a></td>
                                <td><a href="?down=<?php echo $promo->project; ?>">[&darr;]</a></td>
                                <td><a href="?edit=<?php echo $promo->project; ?>">[Editar]</a></td>
                                <td><a href="?remove=<?php echo $promo->project; ?>">[Quitar]</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';