<?php

$node = $this->node;

?>
<h4 class="title">Laterales</h4>
<?php if ($this->side_items) : ?>
    <table>
        <thead>
            <tr>
                <th>Posición</th> <!-- order -->
                <th>Elemento</th> <!-- item -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->side_items as $item) : ?>
            <tr>
                <td><?php echo $item->order; ?></td>
                <td><?php
                if ($item->adminUrl) {
                    echo '<a href="' . $item->adminUrl . '" style="text-decoration: underline;">'.$item->desc.'</a>';
                } else {
                    echo $item->desc;
                }
                ?></td>
                <td><a href="/admin/home/up/<?php echo $item->item; ?>/side">[&uarr;]</a></td>
                <td><a href="/admin/home/down/<?php echo $item->item; ?>/side">[&darr;]</a></td>
                <td><a href="/admin/home/remove/<?php echo $item->item; ?>/side">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay ning&uacute;n elemento lateral en portada</p>
<?php endif; ?>

<?php if ($this->side_availables) : ?>
    <form method="post" action="/admin/home" >
    <input type="hidden" name="action" value="add" />
    <input type="hidden" name="type" value="<?php echo $this->side_new->type ?>" />
    <input type="hidden" name="order" value="<?php echo $this->side_new->order ?>" />

    <p>
        <label for="home-item">Nuevo elemento:</label><br />
        <select id="home-item" name="item">
        <?php foreach ($this->side_availables as $item=>$name) : ?>
            <option value="<?php echo $item; ?>"><?php echo $name; ?></option>
        <?php endforeach; ?>
        </select>
        <br />
        <input type="submit" name="save" value="A&ntilde;adir" />
    </p>

    </form>
<?php endif; ?>
