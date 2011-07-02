<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Gestión de páginas institucionales</h2>
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
                            <th>Página</th>
                            <th>Descripción</th>
                            <th><!-- Editar --></th>
                            <th><!-- Previsualizar --></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this['pages'] as $page) : ?>
                        <tr>
                            <td><?php echo $page->name; ?></td>
                            <td><?php echo $page->description; ?></td>
                            <td><a href="/admin/pages/edit/<?php echo $page->id; ?>">[Edit]</a></td>
                            <td><a href="<?php echo $page->url; ?>" target="_blank">[Preview]</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';