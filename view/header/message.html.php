<?php
$messages = $_SESSION['messages'];
unset($_SESSION['messages']);
?>
    <div id="message" style="position: absolute: top: 0; left: 0; background: red; width: 100%; padding: 20px; /* @FIXME: JAIME MODIFICALO YA! */">
        <ul>
<?php foreach($messages as $message): ?>
            <li>
                <span class="ui-icon ui-icon-<?php echo $message->type ?>">&nbsp;</span>
                <span><?php echo nl2br($message->content) ?></span>
            </li>
<?php endforeach; ?>
        </ul>
        <!--  Esto sólo debería salir con javascript! :) -->
        <input type="button" value="Cerrar" />
    </div>
