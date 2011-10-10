<?php

use Goteo\Library\Text;

// lista de destinatarios segun filtros recibidos, todos marcados por defecto
?>
<div class="widget">
    <p><?php echo 'Vamos a comunicarnos con ' . $_SESSION['mailing']['filters_txt']; ?></p>
    <form action="/admin/mailing/send" method="post">
    <dl>
        <dt>Asunto:</dt>
        <dd>
            <input name="subject" value="<?php echo $_SESSION['mailing']['subject']?>" style="width:300px;"/>
        </dd>
    </dl>
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
                <?php foreach ($_SESSION['mailing']['receivers'] as $usrid=>$usr) : ?>
                <li>
                    <input type="checkbox"
                           name="receiver_<?php echo $usr->id; ?>"
                           id="receiver_<?php echo $usr->id; ?>"
                           value="1"
                           checked="checked" />
                    <label for="receiver_<?php echo $usr->id; ?>"><?php echo $usr->name.' ['.$usr->email.']'; if (!empty($usr->project)) echo ' Proyecto: <strong>'.$usr->project.'</strong>'; ?></label>
                </li>
                <?php endforeach; ?>
            </ul>
        </dd>
    </dl>

    <input type="submit" name="send" value="Enviar"  onclick="return confirm('Has revisado el contenido y comprobado los destinatarios?');"/>

    </form>
</div>