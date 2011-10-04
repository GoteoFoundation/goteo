<?php

use Goteo\Library\Text;

// lista de destinatarios segun filtros recibidos, todos marcados por defecto

?>
<div class="widget">
    <form action="/admin/mailing/send" method="post">
    <dl>
        <dt>Contenido:</dt>
        <dd>
            <textarea name="content" cols="100" rows="5"></textarea>
        </dd>
    </dl>
    <dl>
        <dt>Lista destinatarios:</dt>
        <dd>
            <ul>
                <li><input type="checkbox" name="user-1" id="user-1"><label for="user-1">User name</label></li>
                <li><input type="checkbox" name="user-2" id="user-2"><label for="user-2">User name</label></li>
                <li><input type="checkbox" name="user-3" id="user-3"><label for="user-3">User name</label></li>
            </ul>
        </dd>
    </dl>

    <input type="submit" name="send" value="Enviar"  onclick="return confirm('Has revisado el contenido y comprobado los destinatarios?');"/>

    </form>
</div>