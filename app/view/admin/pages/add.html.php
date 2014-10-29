<?php

use Goteo\Library\Text;

?>
<div class="widget board">
    <form method="post" action="/admin/pages/add">

        <p>
            <label for="page-id">Id:</label><br />
            /about/<input type="text" name="id" id="page-id" value="" />
        </p>

        <p>
            <label for="page-name">T&iacute;tulo:</label><br />
            <input type="text" name="name" id="page-name" value="" />
        </p>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>