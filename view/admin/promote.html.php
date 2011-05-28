<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Proyectos destacados</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/add">Nuevo destacado</a></li>
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
                            <th>Proyecto</th> <!-- preview -->
                            <th>Título</th> <!-- title -->
                            <th>Posición</th> <!-- order -->
                            <th><!-- Subir --></th>
                            <th><!-- Bajar --></th>
                            <th><!-- Editar--></th>
                            <th><!-- Quitar--></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['promoted'] as $promo) : ?>
                        <tr>
                            <td><a href="/project/<?php echo $promo->project; ?>" target="_blank" title="Preview"><?php echo $promo->name; ?></a></td>
                            <td><?php echo $promo->title; ?></td>
                            <td><?php echo $promo->order; ?></td>
                            <td><a href="/admin/promote/up/<?php echo $promo->project; ?>">[&uarr;]</a></td>
                            <td><a href="/admin/promote/down/<?php echo $promo->project; ?>">[&darr;]</a></td>
                            <td><a href="/admin/promote/edit/<?php echo $promo->project; ?>">[Editar]</a></td>
                            <td><a href="/admin/promote/remove/<?php echo $promo->project; ?>">[Quitar]</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';