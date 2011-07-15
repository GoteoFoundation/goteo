<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Entradas para la portada o al pie</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/posts/add//home">Nueva entrada en portada</a></li>
                        <li><a href="/admin/posts/add//footer">Nueva entrada al pie</a></li>
                        <li class="accounting"><a href="/admin/blog">Gestión de blog</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors']) || !empty($this['success'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode(',', $this['errors']); ?>
                        <?php echo implode(',', $this['success']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="widget board">
                <h3 class="title">Portada</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th> <!-- title -->
                            <th>Portada</th>
                            <th>Pie</th>
                            <th>Posición</th> <!-- order -->
                            <td><!-- Move up --></td>
                            <td><!-- Move down --></td>
                            <td><!-- Edit --></td>
                            <td><!-- Remove --></td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['home_posts'] as $post) : ?>
                        <tr>
                            <td><?php echo $post->title; ?></td>
                            <td><?php if ($post->home == 1) echo 'Portada'; ?></td>
                            <td><?php if ($post->footer == 1) echo 'Pie'; ?></td>
                            <td><?php echo $post->order; ?></td>
                            <td><a href="/admin/posts/up/<?php echo $post->id ?>/<?php echo $post->type ?>">[&uarr;]</a></td>
                            <td><a href="/admin/posts/down/<?php echo $post->id ?>/<?php echo $post->type ?>">[&darr;]</a></td>
                            <td><a href="/admin/blog/edit/<?php echo $post->id ?>/<?php echo $post->type ?>">[Editar] (Ojo! salta a gestión de blog)</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <div class="widget board">
                <h3 class="title">Pie</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th> <!-- title -->
                            <th>Portada</th>
                            <th>Pie</th>
                            <th>Posición</th> <!-- order -->
                            <td><!-- Move up --></td>
                            <td><!-- Move down --></td>
                            <td><!-- Edit --></td>
                            <td><!-- Remove --></td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['footer_posts'] as $post) : ?>
                        <tr>
                            <td><?php echo $post->title; ?></td>
                            <td><?php if ($post->home == 1) echo 'Portada'; ?></td>
                            <td><?php if ($post->footer == 1) echo 'Pie'; ?></td>
                            <td><?php echo $post->order; ?></td>
                            <td><a href="/admin/posts/up/<?php echo $post->id ?>/<?php echo $post->type ?>">[&uarr;]</a></td>
                            <td><a href="/admin/posts/down/<?php echo $post->id ?>/<?php echo $post->type ?>">[&darr;]</a></td>
                            <td><a href="/admin/blog/edit/<?php echo $post->id ?>/<?php echo $post->type ?>">[Editar] (Ojo! salta a gestión de blog)</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';