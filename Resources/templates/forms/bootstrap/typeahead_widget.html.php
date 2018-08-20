<div class="input-typeahead" data-sources="<?= $sources ?>" value-field="<?= $value_field ?>">
    <?php echo $view['form']->block($form, 'form_widget_simple', ['type' => 'text', 'name' => $fake_id, 'id' => $fake_id, 'full_name' => $fake_id, 'attr' => ['data-real-id' => $id], 'value' => $text]); ?>
    <?php echo $view['form']->block($form, 'form_widget_simple', ['type' => 'hidden', 'attr' => ['class' => '']]); ?>
</div>
