<?php

$node = $this->node;

?>
<h4 class="title">Central</h4>
<?php if ($this->central_items) : ?>
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
            <?php foreach ($this->central_items as $item) : ?>
            <tr>
                <td><?php echo $item->order; ?></td>
                <td><?php
                if ($item->adminUrl) {
                    echo '<a href="' . $item->adminUrl . '" style="text-decoration: underline;">'.$item->desc.'</a>';
                } else {
                    echo $item->desc;
                }
                ?></td>
                <td><a href="/admin/home/up/<?php echo $item->item; ?>/main">[&uarr;]</a></td>
                <td><a href="/admin/home/down/<?php echo $item->item; ?>/main">[&darr;]</a></td>
                <td><a href="/admin/home/remove/<?php echo $item->item; ?>/main">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay ning&uacute;n elemento en portada</p>
<?php endif; ?>

<?php if ($this->central_availables) : ?>
    <form method="post" action="/admin/home" >
    <input type="hidden" name="action" value="add" />
    <input type="hidden" name="type" value="<?php echo $this->central_new->type ?>" />
    <input type="hidden" name="order" value="<?php echo $this->central_new->order ?>" />

    <p>
        <label for="home-item">Nuevo elemento:</label><br />
        <select id="home-item" name="item">
        <?php foreach ($this->central_availables as $item => $name) : ?>
            <option value="<?php echo $item; ?>"><?php echo $name; ?></option>
        <?php endforeach; ?>
        </select>
        <br />
        <input type="submit" name="save" value="A&ntilde;adir" />
    </p>

    </form>
<?php endif; ?>
