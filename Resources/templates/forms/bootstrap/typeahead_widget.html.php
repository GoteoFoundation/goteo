 <div class="input-typeahead" data-sources="<?= $sources ?>" data-value-field="<?= $value_field ?>">
 <?php if ($type == "multiple") : ?>
    <?php echo $view['form']->block($form, 'form_widget_simple', ['type' => 'text', 'name' => $fake_id, 'id' => $fake_id, 'full_name' => $fake_id, 'attr' => ['data-real-id' => $id, 'data-type' => $type], 'value' => '', 'required' => '']); ?>
    <?php echo $view['form']->block($form, 'form_widget_simple', ['full_name' => $full_name.'[]','type' => 'hidden', 'attr' => ['class' => ''], 'value' => '']); ?>
    <?php if ($text && is_array($text)) : ?>
        <?php foreach($text as $idText => $nameText) : ?>
            <?php echo $view['form']->block($form, 'form_widget_simple', ['full_name' => $full_name.'[]','type' => 'hidden', 'attr' => ['class' => ''], 'value' => $idText]); ?>
        <?php endforeach ?>
    <?php endif ?>
<?php else : ?>
    <?php echo $view['form']->block($form, 'form_widget_simple', ['type' => 'text', 'name' => $fake_id, 'id' => $fake_id, 'full_name' => $fake_id, 'attr' => ['data-real-id' => $id, 'data-type' => $type], 'value' => $text]); ?>
    <?php echo $view['form']->block($form, 'form_widget_simple', ['type' => 'hidden', 'attr' => ['class' => '']]); ?>
<?php endif ?>
</div>



<?php if ($type == "multiple") : ?>

    <div id="<?= $id ?>" class="bootstrap-tagsinput help-text">
    <?php if ($text && is_array($text)) : ?>
        <?php foreach($text as $idText => $nameText) : ?>
        <span class="tag label label-lilac"> <?= $nameText ?>
            <span id="remove-<?= $idText ?>-<?=$id?>" data-real-id="<?= $id ?>" data-value="<?= $idText ?>" data-role="remove"></span>
        </span>
        <?php endforeach ?>
    <?php endif ?>
</div>

<?php endif ?>