<?php

use Goteo\Library\Text,
    Goteo\Model\Template;

//$templates = Template::getAllMini();
$templates = array(
    '11' => 'Base',
    '27' => 'Aviso a los donantes',
    '38' => 'Recordatorio a los donantes',
    '35' => 'Testeo'
);
// lista de destinatarios segun filtros recibidos, todos marcados por defecto
?>
<script type="text/javascript">
jQuery(document).ready(function ($) {

    $('#template_load').click(function () {
       if (confirm('El asunto y el contenido actual se substiruira por el que hay en la plantilla. Seguimos?')) {

           if ($('#template').val() == '0') {
            $('#mail_subject').val('');
            $('#mail_content').html('');
           }
            content = $.ajax({async: false, url: '/ws/get_template_content/'+$('#template').val()}).responseText;
            var arr = content.split('#$#$#');
            $('#mail_subject').val(arr[0]);
            $('#mail_content').val(arr[1]);
        }
    });

});
</script>
<div class="widget">
    <p>Las siguientes variables se sustituir&aacute;n en el contenido:</p>
    <ul>
        <li><strong>%USERID%</strong> Para el id de acceso del destinatario</li>
        <li><strong>%USEREMAIL%</strong> Para el email del destinatario</li>
        <li><strong>%USERNAME%</strong> Para el nombre del destinatario</li>
        <li><strong>%SITEURL%</strong> Para la url de esta plataforma (<?php echo SITE_URL ?>)</li>
    </ul>
</div>
<div class="widget">
    <p><?php echo 'Vamos a comunicarnos con ' . $_SESSION['mailing']['filters_txt']; ?></p>
    <p>Son <?php echo count($_SESSION['mailing']['receivers']) ?> destinatarios.</p>
    <form action="/admin/mailing/send" method="post" enctype="multipart/form-data">
    <dl>
        <dt>Seleccionar plantilla:</dt>
        <dd>
            <select id="template" name="template" >
                <option value="0">Sin plantilla</option>
            <?php foreach ($templates as $templateId=>$templateName) : ?>
                <option value="<?php echo $templateId; ?>"><?php echo $templateName; ?></option>
            <?php endforeach; ?>
            </select>
            <input type="button" id="template_load" value="Cargar" />
        </dd>
    </dl>
    <dl>
        <dt>Asunto:</dt>
        <dd>
            <input id="mail_subject" name="subject" value="<?php echo $_SESSION['mailing']['subject']?>" style="width:500px;"/>
        </dd>
    </dl>
    <dl>
        <dt>Contenido: (en c&oacute;digo html; los saltos de linea deben ser con &lt;br /&gt;)</dt>
        <dd>
            <textarea id="mail_content" name="content" cols="100" rows="10"></textarea>
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
