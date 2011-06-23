<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Entradas de Blog Goteo</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li><a href="/admin/blog/add">Nueva entrada</a></li>
                        <li class="checking"><a href="/admin/posts">Ordenar la portada</a></li>
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
                <table>
                    <thead>
                        <tr>
                            <th>Título</th> <!-- title -->
                            <th>Fecha</th> <!-- date -->
                            <th>En portada</th> <!-- date -->
                            <td><!-- Edit --></td>
                            <td></td><!-- preview -->
                            <td><!-- Remove --></td>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['posts'] as $post) : ?>
                        <tr>
                            <td><?php echo $post->title; ?></td>
                            <td><?php echo $post->date; ?></td>
                            <td><?php echo $post->home ? 'Sí' : ''; ?></td>
                            <td><a href="/admin/blog/edit/<?php echo $post->id; ?>">[Editar]</a></td>
                            <td><a href="/blog/<?php echo $post->id; ?>">[Ver publicado]</a></td>
                            <td><a href="/admin/blog/remove/<?php echo $post->id; ?>">[Quitar]</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';