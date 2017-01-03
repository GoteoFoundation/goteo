<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php

$filters = $this->filters;
$status = $this->statuses;

?>
<div class="widget board">
    <div class="ui-widget">
        <label for="projects-filter">Proyecto: (autocomplete nombre)</label><br />
        <?= $this->insert('admin/partials/typeahead_form', [
                                                'id' => 'projects-filter',
                                                'value' => $filters['project']
                                                // 'hidden' => true
                                                ]) ?>
    </div>
    <div class="ui-widget" style="margin:1em 0">
    <form id="filter-form" action="/admin/commons" method="get">
            <p><label for="projStatus-filter">Solo proyectos en estado (afecta al filtro de proyectos):</label><br />
            <select id="projStatus-filter" name="projStatus">
                <option value="">Cualquier exitoso</option>
            <?php foreach ($this->projStatus as $id => $Name) : ?>
                <option value="<?= $id ?>"<?php if ($filters['projStatus'] == $id) echo ' selected="selected"';?>><?= $Name ?></option>
            <?php endforeach ?>
            </select></p>


        <p>
            <input type="submit" value="filtrar" />
            <input type="hidden" name="project" id="project" value="<?= $filters['project'] ?>" />
        </p>
    </form>
    </div>

    <a href="/admin/commons?reset=filters">[<?= $this->text('admin-remove-filters') ?>]</a>
</div>

<div class="widget board">
<?php if ($this->projects) : ?>
    <table>
        <thead>
            <tr>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Cumplidos</th>
                <th>Vencimiento</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php  foreach ($this->projects as $project) :

            // calculo fecha de vencimiento (timestamp de un año despues de financiado)
            $deadline = mktime(0, 0, 0,
                date('m', strtotime($project->success)),
                date('d', strtotime($project->success)),
                date('Y', strtotime($project->success)) + 1
            );

            ?>
            <tr>
                <td><a href="/project/<?= $project->id?>" target="blank"><?= $project->name ?></a></td>
                <td><?= $status[$project->status] ?></td>
                <td style="text-align: center;"><?= $project->cumplidos.'/'.count($project->social_rewards) ?></td>
                <td><?= date('d-m-Y', $deadline) ?></td>
                <td>
                    <a href="/admin/commons/view/<?= $project->id?>">[Gestionar]</a>&nbsp;
                    <a href="/admin/commons/info/<?= $project->id?>">[Ver Contacto]</a>&nbsp;
                    <?php if ($project->status == 4) : ?><a href="<?= "/admin/commons/fulfill/{$project->id}" ?>" onclick="return confirm('Se va a cambiar el estado del proyecto, ok?');">[Cumplido]</a>&nbsp;<?php endif ?>
                    <a href="/admin/projects?proj_id=<?= $project->id?>" target="blank">[Admin]</a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php else : ?>
    <p>No hay transacciones que cumplan con los filtros.</p>
<?php endif;?>
</div>

<?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php $this->replace() ?>



<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){
    $.typeahead({
        input: "#projects-filter",
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
