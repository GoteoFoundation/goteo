<?php

use Goteo\Library\Text,
    Goteo\Library\Template;

$mailing = $this['mailing'];
$list = $this['list'];

$link = SITE_URL.'/mail/'.base64_encode(md5(uniqid()).'¬any¬'.$mailing->mail).'/?email=any';

// por defecto cogemos la newsletter
$tpl = 33;

// si el mailing está desactivado , mostrar mensaje y botón para iniciar de nuevo
if (empty($mailing) || !$mailing->active) :
    $templates = array(
        '27' => 'Aviso a los donantes',
        '38' => 'Recordatorio a los donantes',
        '33' => 'Boletin'
    );
    $template = Template::get($tpl);
?>
<script type="text/javascript">
jQuery(document).ready(function ($) {

    $('#template_load').click(function () {
       if ($('#template').val() == '0') {
        $('#mail_subject').val('');
       }
        content = $.ajax({async: false, url: '<?php echo SITE_URL; ?>/ws/get_template_content/'+$('#template').val()}).responseText;
        var arr = content.split('#$#$#');
        $('#mail_subject').val(arr[0]);
    });

});
</script>
<div class="widget board">
    <p>No hay ningun envio masivo programado. Cargar la plantilla, confirmar el asunto y pulsar [Iniciar] para generar uno nuevo (se puede revisar el contenido antes de activar el env&iacute;o).</p>
    <p><strong>NOTA:</strong> con este sistema no se pueden añadir variables en el contenido, se genera un solo &quot;si no ves&quot;. Para contenido personalizado hay que usar la funcionalidad <a href="/admin/mailing" >Comunicaciones</a>.</p>
    <form action="/admin/newsletter/init" method="post">
    <dl>
        <dt>Plantillas masivas:</dt>
        <dd>
            <select id="template" name="template" >
            <?php foreach ($templates as $tplId=>$tplName) : ?>
                <option value="<?php echo $tplId; ?>" <?php if ( $tplId == $tpl) echo 'selected="selected"'; ?>><?php echo $tplName; ?></option>
            <?php endforeach; ?>
            </select>
            <input type="button" id="template_load" value="Cargar" />
        </dd>
    </dl>
    <dl>
        <dt>Asunto:</dt>
        <dd>
            <input id="mail_subject" name="subject" value="<?php echo $template->title ?>" style="width:500px;"/>
        </dd>
    </dl>
    <dl>
        <dt>Es una prueba: (se envia a los destinatarios de pruebas)</dt>
        <dd>
            <input type="checkbox" name="test" value="1" />
        </dd>
    </dl>
        
        <input type="submit" name="init" value="Iniciar" />
    </form>
</div>
<?php endif; ?>
<?php if (!empty($mailing->id)) : ?>
<div class="widget board">
        <p>
           Asunto: <strong><?php echo $mailing->subject ?></strong><br />
           Iniciado el: <strong><?php echo $mailing->date ?></strong><br />
           Estado del envío automático: <?php echo ($mailing->active) 
               ? '<span style="color:green;font-weight:bold;">Activo</span>'
               : '<span style="color:red;font-weight:bold;">Inactivo</span>' ?>
        </p>

    <table>
        <thead>
            <tr>
                <th><!-- Si no ves --></th>
                <th>Fecha</th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=receivers" title="Ver destinatarios">Destinatarios</a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=sended" title="Ver enviados">Enviados</a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=failed" title="Ver fallidos">Fallidos</a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=pending" title="Ver pendientes">Pendientes</a></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="<?php echo $link; ?>" target="_blank">[Si no ves]</a></td>
                <td><?php echo $mailing->date; ?></td>
                <td style="width:15%"><?php echo $mailing->receivers; ?></td>
                <td style="width:15%"><?php echo $mailing->sended; ?></td>
                <td style="width:15%"><?php echo $mailing->failed; ?></td>
                <td style="width:15%"><?php echo $mailing->pending; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($list)) : ?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th><!-- Si no ves --></th>
                <th>Fecha</th>
                <th>Asunto</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item) : ?>
            <tr>
                <td><?php echo $item->id; ?></td>
                <td><a href="<?php echo $item->link; ?>" target="_blank">[Si no ves]</a></td>
                <td><?php echo $item->date; ?></td>
                <td><?php echo $item->subject; ?></td>
                <td><?php echo $item->active ? 'Activo' : 'Inactivo'; ?></td>
                <td><?php echo $item->bloqued ? 'Bloqueado' : ''; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
