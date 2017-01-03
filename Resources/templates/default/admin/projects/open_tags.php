<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

    <table>
        <tr>
            <th>Agrupación</th>
            <th></th>
        </tr>
        <?php foreach ($this->project->open_tags as $open_tagId => $open_tagName) : ?>
        <tr>
            <td><?= $open_tagName ?></td>
            <td><a href="/admin/projects/open_tags/<?= $this->project->id ?>?op=unassignOpen_tag&amp;open_tag=<?= $open_tagId ?>">[Desasignar]</a></td>
        </tr>
        <?php endforeach ?>
        <tr>
            <form id="form-assign" action="/admin/projects/open_tags/<?= $this->project->id ?>" method="get">
                <input type="hidden" name="op" value="assignOpen_tag" />
            <td colspan="2">
                <select id="assign-open-tag" name="open_tag">
                    <option value="">Asigna otra agrupación</option>
                    <?php foreach ($this->open_tags as $open_tagId => $open_tagName) :
                        if (isset($this->project->open_tags[$open_tagId])) continue;
                        ?>
                    <option value="<?= $open_tagId ?>"><?= $open_tagName ?></option>
                    <?php endforeach ?>
                </select>
            </td>
            <td><a href="#" onclick="return assign();" class="button">Asignar</a></td>
            </form>
        </tr>
    </table>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
function assign() {
    if (document.getElementById('assign-open-tag').value != '') {
        document.getElementById('form-assign').submit();
        return true;
    } else {
        alert('No has seleccionado ninguna agrupación');
        return false;
    }
}
// @license-end
</script>

<?php $this->append() ?>
