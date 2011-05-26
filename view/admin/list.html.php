<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2><?php echo $this['title']; ?></h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
                    <?php foreach ($this['menu'] as $menu) : ?>
                        <li><a href="<?php echo $menu['url']; ?>"><?php echo $menu['label']; ?></a></li>
                    <?php endforeach; ?>
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

            <!-- Filtro -->
            <div class="widget">
                <form id="filter-form" action="<?php echo $this['filters']['action']; ?>" method="get">
                    <label for="id-filter"><?php echo $this['filters']['label']; ?></label>
                    <select id="id-filter" name="filter" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['filters']['values'] as $val=>$opt) : ?>
                        <option value="<?php echo $val; ?>"<?php if ($this['filter'] == $val) echo ' selected="selected"';?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- lista -->
            <div class="widget">
                <table>
                <?php foreach ($this['data'] as $item) : ?>
                    <tr>
                        <td><a title="Registro <?php echo $item->$this['row']['id']; ?>" href='<?php $id = $this['row']['id']; echo $this['urlEdit'].$item->$id; ?>?filter=<?php echo $this['filter']; ?>'>[Editar]</a></td>
                        <td><p><?php echo $item->$this['row']['value']; ?></p></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';