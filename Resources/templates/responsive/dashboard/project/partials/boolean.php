<div class="material-switch<?=$this->class ? ' '.$this->class : '' ?><?= $this->url ? ' auto-save-property' : '' ?>" data-url="<?= $this->url ?>" data-type="boolean" data-confirm-yes="<?= $this->confirm_yes ? $this->confirm_yes : '' ?>" data-confirm-no="<?= $this->confirm_no ? $this->confirm_no : '' ?>">
    <input type="checkbox"<?= $this->active ? ' checked="true"' : '' ?> name="<?= $this->name ?>"<?php
    if($this->input_class) {
        echo ' class="'.$this->input_class.'"';
    }
    if($this->input_data && is_array($this->input_data)) {
        foreach($this->input_data as $key => $value) {
            echo " data-$key=\"$value\"";
        }
    } ?>>
    <label class="label-<?= $this->label_type ? $this->label_type : 'default' ?>"></label>
</div>
