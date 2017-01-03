<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$filters = $this->filters;

?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <div class="ui-widget">
        <label for="change_user_input">Buscador proyectos:</label><br />
        <?= $this->insert('admin/partials/typeahead_form', [
                                                            'id' => 'change_project_input',
                                                            'value' => $filters['project']
                                                            ]) ?>
    </div>
    <form id="filter-form" action="/admin/rewards" method="get">
        <input type="hidden" id="project" name="project" value="<?= $filters['project'] ?>" />

        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?= $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="status-filter">Mostrar por estado de recompensa:</label><br />
            <select id="status-filter" name="status" >
                <option value="">Todos</option>
            <?php foreach ($this->status as $statusId => $statusName) : ?>
                <option value="<?= $statusId ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?= $statusName ?></option>
            <?php endforeach ?>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="status-filter">Regalos:</label><br />
            <select id="status-filter" name="friend" >
                <option value="">--</option>
                <option value="only"<?php if ($filters['friend'] == 'only') echo ' selected="selected"';?>>Solo regalos</option>
                <option value="none"<?php if ($filters['friend'] == 'none') echo ' selected="selected"';?>>Solo NO regalos</option>
            </select>
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/rewards?reset=filters">[<?= $this->text('admin-remove-filters') ?>]</a>
</div>

<div class="widget board">
<?php if($this->list) : ?>
    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Cofinanciador</th>
                <th>Proyecto</th>
                <th>Recompensa</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->list as $reward) : ?>
            <tr>
                <td><a href="/admin/rewards/edit/<?= $reward->invest ?>" >[Modificar]</a></td>
                <td><a href="/admin/users/manage/<?= $reward->user ?>" target="_blank" title="<?= $reward->name ?>"><?= $reward->email ?></a></td>
                <td><a href="/admin/projects?name=<?= $reward->getProject()->name ?>" target="_blank"><?= $this->text_truncate($reward->getProject()->name, 20); if (!empty($invest->campaign)) echo '<br />('.$this->calls[$invest->campaign].')' ?></a></td>
                <td><?= $reward->reward_name ?></td>
                <?php if (!$reward->fulfilled) : ?>
                    <td style="color: red;" >Pendiente</td>
                    <td><a href="<?= "/admin/rewards/fulfill/{$reward->invest}" ?>">[Marcar cumplida]</a></td>
                <?php else : ?>
                    <td style="color: green;" >Cumplido</td>
                    <td><a href="<?= "/admin/rewards/unfill/{$reward->invest}" ?>">[Marcar pendiente]</a></td>
                <?php endif ?>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>


    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php else : ?>
    <p>No hay aportes que cumplan con los filtros.</p>
<?php endif;?>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){
     $.typeahead({
        input: "#change_project_input",
        // order: "asc",
        dynamic:true,
        hint:true,
        searchOnFocus:true,
        accent:true,
        emptyTemplate: 'No result for "{{query}}"',
        display: ["id", "name", "owner"],
        template: '<span>' +
            '<span class="avatar">' +
                '<img src="/img/tinyc/{{image}}">' +
            '</span> ' +
            '<span class="name">{{name}}</span> ' +
            '<span class="info">({{id}}, {{owner}})</span>' +
        '</span>',
        source: {
            list: {
                url: [{
                        url: "/api/projects",
                        data: {
                            q: "{{query}}",
                        }
                    }, 'list']
            }
        },
        callback: {
            onClickAfter: function (node, a, item, event) {
                event.preventDefault();
                $("#project").val(item.id);
            }
        },
        debug: true
    });
});
// @license-end
</script>

<?php $this->append() ?>
