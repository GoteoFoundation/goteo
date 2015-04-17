<?php

use Goteo\Library\Text,
    Goteo\Model\User;

$call = $vars['call'];
?>
<script type="text/javascript">
function save() {
    if (document.getElementById('save-post').value != '') {
        document.getElementById('form-save').submit();
        return true;
    } else {
        alert('No has seleccionado ningun administrador');
        return false;
    }
}
</script>
<div class="widget">
    <!-- asignar -->
    <table>
        <tr>
            <th>Entrada de blog</th>
            <th></th>
        </tr>
        <?php foreach ($call->posts as $post) : ?>
        <tr>
            <td><?php echo "({$post->id}) {$post->title}"; ?></td>
            <td><a href="/admin/calls/posts/<?php echo $call->id; ?>/?op=remove&post=<?php echo $post->id; ?>">[Quitar]</a></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td><br /></td>
        </tr>
        <tr>
            <form id="form-save" action="/admin/calls/posts/<?php echo $call->id; ?>" method="get">
                <input type="hidden" name="op" value="save" />
            <td colspan="2">
                <label>Nueva entrada:  (poner el id)<br />
                <input type="text" id="save-post" name="post" value="" />
                </label>
                &nbsp;&nbsp;&nbsp;
            <a href="#" onclick="return save();" class="button">A&ntilde;adir</a></td>
            </form>
        </tr>
    </table>
</div>
