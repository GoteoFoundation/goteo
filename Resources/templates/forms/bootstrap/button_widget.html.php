<?php
$txt = false !== $translation_domain ? $view['translator']->trans($label, array(), $translation_domain) : $label;
if (!$label) { $label = isset($label_format)
    ? strtr($label_format, array('%name%' => $name, '%id%' => $id))
    : $view['form']->humanize($name); } ?>
<button type="<?php echo isset($type) ? $view->escape($type) : 'button' ?>" <?php echo $view['form']->block($form, 'button_attributes') ?><?php if($span) echo ' title="' .  $view->escape($txt) . '"' ?>><?php
 if($icon_class) {
    echo '<i class="' . $view->escape($icon_class) . '"></i> ';
 }

 if($span) echo '<span class="' . $span . '">';
 echo $view->escape($txt);
 if($span) echo '<span class="' . $span . '">';
?></button>
