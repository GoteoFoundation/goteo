<?php

$form = $this->raw('form');

if($form){
    echo $form->row();die;
    foreach($form as $name => $element) {
        echo $this->insert('partials/forms/input', ['name' => $name, 'element' => $element]);
    }

}
