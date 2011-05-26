<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2><?php echo $this['title']; ?></h2>

            <p><a href=""></a></p>

            <!-- errores / success -->
            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

            <!-- Filtro -->
            <form id="filter-form" action="<?php echo $this['filters']['action']; ?>" method="get">
                <label for="id-filter"><?php echo $this['filters']['label']; ?></label>
                <select id="id-filter" name="filter" onchange="document.getElementById('filter-form').submit();">
                <?php foreach ($this['filters']['values'] as $val=>$opt) : ?>
                    <option value="<?php echo $val; ?>"<?php if ($this['filter'] == $val) echo ' selected="selected"';?>><?php echo $opt; ?></option>
                <?php endforeach; ?>
                </select>
            </form>

            <!-- lista -->
            <table>
            <?php foreach ($this['data'] as $item) : ?>
                <tr>
                    <td><a title="Registro <?php echo $item->$this['row']['id']; ?>" href='<?php $id = $this['row']['id']; echo $this['urlEdit'].$item->$id; ?>?filter=<?php echo $this['filter']; ?>'>[Editar]</a></td>
                    <td><p><?php echo $item->$this['row']['value']; ?></p></td>
                </tr>
            <?php endforeach; ?>
            </table>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';