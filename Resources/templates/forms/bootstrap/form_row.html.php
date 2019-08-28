<?php

if($type === 'title') {
    $title = false !== $translation_domain ? $view['translator']->trans($form->vars['label'], array(), $translation_domain) : $label;

    echo '<h4>' . $title . '</h4>';
    return;
}

$label_view = $label_position === 'none' ? '' : $view['form']->label($form);
?>
<div class="form-group<?= $row_class ? " $row_class" : '' ?><?= (count($errors) > 0) ? ' has-error' : '' ?>" id="form-<?= $form->vars['name'] ?>">

    <?= $label_position === 'right' ? '' :  $label_view ?>

    <?php if(!$no_input_wrap): ?><div class="input-wrap"><?php endif ?>

    <?php if($attr['pre-help']): ?>
        <div class="pre-help"><?= $attr['pre-help'] ?></div>
    <?php endif ?>

    <?php

    // This is a hack
    // some custom form types (ie: DropfilesType) does not seem to register the error
    // handling properly, I don't know the cause yet so this is here to force appear the
    // error message in the proper place
    // NOTE: this does not remove the global error messages
    if($form->parent && count($form->parent->vars['errors']) > 0) {
        if($form->parent->vars['errors'][0]->getOrigin()->getName() === $form->vars['name']) {
            echo $view['form']->errors($form->parent);
        }
    }

    echo $view['form']->errors($form);
    ?>

  <?php if(in_array($type, ['markdown', 'summernote'])): ?>
      <div class="<?= $type ?>">
  <?php endif ?>

    <?= $view['form']->widget($form) ?>

    <?php if($label_position !== 'right' && $attr['help']): ?>
        <div class="help-text"><?= $attr['help'] ?></div>
    <?php endif ?>

    <?php if($attr['info']): ?>
        <div class="help-text info"><?= $attr['info'] ?></div>
    <?php endif ?>

    <?php if(!$no_input_wrap): ?></div><?php endif ?>

    <?= $label_position === 'right' ? $label_view : '' ?>

    <?php if($label_position === 'right' && $attr['help']): ?>
        <div class="help-text"><?= $attr['help'] ?></div>
    <?php endif ?>

  <?php if(in_array($type, ['markdown', 'summernote'])): ?>
      </div>
  <?php endif ?>

</div>
