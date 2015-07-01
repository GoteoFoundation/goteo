<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/nodes/edit/<?= $this->node->id ?>" >


    <?php if(!$this->node->isMasterNode()): ?>
    <p>
        <label for="node-id">Identificador:</label><br />
        <input disabled style="border:1px solid #b00" type="text" id="node-id" name="id" value="<?= $this->node->id ?>" />
        <a href="#" id="enableNodeid">Cambiar</a>
    </p>
    <?php endif ?>

    <?= $this->insert('admin/nodes/partials/edit_common', ['masternode' => $this->node->isMasterNode()]) ?>

        <input type="submit" name="save" value="Guardar" />
    </form>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $(function(){
        $('#enableNodeid').click(function(e){
            e.preventDefault();
            if(confirm('Esto modificará las URLs del nodo actual. Seguro de continuar?')) {
                $('input#node-id').removeAttr('disabled').select();
                $(this).remove();
            }
        });
    });
</script>
<?php $this->append() ?>
