<?php

$data_populate = ['data-geocoder-skip-population' => (string) !$form->vars['always_populate']];
$ob = $form->vars['location_object'];

?>

<?php if($populate = $form->vars['populate_fields']): ?>
    <?php
    if($form->vars['always_populate'] === 'auto') {
        $data_populate['data-geocoder-skip-population'] = 'true';
    }

    $data_populate['data-geocoder-populate-formatted_address'] = '.desc_' . $form->vars['id'];
    foreach($populate as $field):
        $data_populate['data-geocoder-populate-' . $field] = '.populate_' . $form[$field]->vars['id'];
    ?>
        <input type="hidden" name="<?= $form[$field]->vars['full_name'] ?>" id="<?= $form[$field]->vars['id'] ?>" class="populate_<?= $form[$field]->vars['id'] ?>" value="<?= $this->escape($form[$field]->vars['value']) ?>">
    <?php endforeach ?>
<?php endif ?>

<?= $view['form']->block($form['location'], 'form_widget_simple', ['attr' => $form->vars['attr'] + $data_populate]); ?>

<?php if($populate && $ob !== null): ?>
    <p class="help-block">
      <strong><i class="fa fa-map-marker"></i> <?= $view->escape(false !== $choice_translation_domain ? $view['translator']->trans('form-editor-exact-location', array(), $choice_translation_domain) : 'Exact location') ?>:</strong>
      <span class="location_desc desc_<?= $form->vars['id'] ?>"><?php
        if($ob->id) {
            echo $ob->getFormatted(true);
        } else {
            echo '-' . $view->escape(false !== $choice_translation_domain ? $view['translator']->trans('form-editor-change-unlocated', array(), $choice_translation_domain) : 'Unlocated') . '-';
        }
        ?></span>
      <a class="exact-location" href="#<?= $form['location']->vars['id'] ?>"><i class="fa fa-hand-o-right"></i> <?= $view->escape(false !== $choice_translation_domain ? $view['translator']->trans('form-editor-change-location', array(), $choice_translation_domain) : 'change') ?></a>
    </p>
<?php endif ?>
