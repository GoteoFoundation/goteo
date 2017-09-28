<?php

$form = $this->raw('form');
$reward = $this->reward;
$translated = $reward->getLang($this->lang)

?>
<?= $this->form_row($form['reward_' . $reward->id], ['value' => $translated->reward, 'attr' => ['help' => $reward->reward]]) ?>
<?= $this->form_row($form['description_' . $reward->id], ['value' => $translated->description, 'attr' => ['help' => $reward->description]]) ?>
