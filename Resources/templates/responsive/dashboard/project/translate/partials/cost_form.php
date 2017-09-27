<?php

$form = $this->raw('form');
$cost = $this->cost;
$translated = $cost->getLang($this->lang)

?>
<?= $this->form_row($form['cost_' . $cost->id], ['value' => $translated->cost, 'attr' => ['help' => $cost->cost]]) ?>
<?= $this->form_row($form['description_' . $cost->id], ['value' => $translated->description, 'attr' => ['help' => $cost->description]]) ?>
