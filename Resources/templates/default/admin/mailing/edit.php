<?php

$templates = $this->templates
?>

<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget">
    <p>Las siguientes variables se sustituir&aacute;n en el contenido:</p>
    <ul>
        <li><strong>%USERID%</strong> Para el id de acceso del destinatario</li>
        <li><strong>%USEREMAIL%</strong> Para el email del destinatario</li>
        <li><strong>%USERNAME%</strong> Para el nombre del destinatario</li>
        <li><strong>%SITEURL%</strong> Para la url de esta plataforma (<?= SITE_URL ?>)</li>
    </ul>
</div>

<div class="widget">
    <p><?= 'Vamos a comunicarnos con ' . $this->raw('filters_txt') ?></p>
    <p>Son <span class="label label-error"><?= $this->total ?></span> destinatarios posibles.</p>
    <form action="/admin/mailing/send" method="post" enctype="multipart/form-data">
    <dl>
        <dt>Seleccionar plantilla:</dt>
        <dd>
            <select id="template" name="template" >
                <option value="0">Sin plantilla</option>
            <?php foreach ($templates as $templateId => $templateName) : ?>
                <option value="<?= $templateId ?>"<?= ($this->templateId == $templateId ? ' selected="selected"' : '') ?>><?= $templateName ?></option>
            <?php endforeach ?>
            </select>
            <input type="button" id="template_load" value="Cargar" />
        </dd>
    </dl>
    <dl>
        <dt>Asunto:</dt>
        <dd>
            <input id="mail_subject" name="subject" value="<?= $this->subject ?>" style="width:500px;"/>
        </dd>
    </dl>
    <dl>
        <dt>Contenido: (en c&oacute;digo html; los saltos de linea deben ser con &lt;br /&gt;)</dt>
        <dd>
            <textarea id="mail_content" name="content" cols="80" rows="10"><?= $this->content ?></textarea>
        </dd>
    </dl>

    <div id="admin-mailing-receivers">
    <dl>
        <dt>Lista destinatarios:</dt>
        <dd>
            <ul>
                <?php foreach ($this->receivers as $usrid => $usr) : ?>
                <li>
                    <input class="mailing-receivers" type="checkbox"
                           name="receiver_<?= $usr->id ?>"
                           id="receiver_<?= $usr->id ?>"
                           value="<?= $usr->id ?>"
                           <?= $this->removed_receivers[$usrid] ? '' : 'checked="checked"' ?> />
                    <label for="receiver_<?= $usr->id ?>"><?= $usr->name.' ['.$usr->email.']'; if (!empty($usr->project)) echo ' Proyecto: <strong>'.$usr->project.'</strong>' ?></label>
                </li>
                <?php endforeach ?>
            </ul>
        </dd>
    </dl>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>
    <input type="submit" name="send" value="Enviar"  onclick="return confirm('Has revisado el contenido y comprobado los destinatarios?');"/>
    </div>

    </form>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
$(function ($) {

    $('#template_load').click(function () {
       if (confirm('El asunto y el contenido actual se substiruira por el que hay en la plantilla. Seguimos?')) {
            if($('#template').val() == 0) {
                $('#mail_subject').val('');
                $('#mail_content').val('');
                return true;
            }
            $.getJSON('/admin/mailing/get_template_content/' + $('#template').val(), function(data){
                if(data) {
                    if(data.title || data.text) {
                        $('#mail_subject').val(data.title);
                        $('#mail_content').val(data.text);
                    }
                    else {
                        alert('Error:', data);
                    }
                }
                else {
                    alert('Error retrieving template!');
                }
            });
        }
    });

    // mailing receivers checkbox
    $('#admin-mailing-receivers').delegate('.mailing-receivers', 'change', function(e){
        e.preventDefault();
        var check = $(this).is(':checked');
        var val = $(this).val();
        $.getJSON('/admin/mailing/receiver/' + val + '/' + (check ? 'add' : 'remove'), function(data){
            // alert(check+ ' '+data.id+ ' '+data.active)
            $('#receiver_' + data.id).prop('checked' , data.active);
        });
    });

    // paginator handleing
    $('#admin-mailing-receivers').delegate('.pagination a', 'click', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#admin-mailing-receivers').load(href + ' #admin-mailing-receivers');
    });
});
</script>
<?php $this->append() ?>
