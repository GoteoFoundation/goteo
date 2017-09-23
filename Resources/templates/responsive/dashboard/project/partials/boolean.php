<div class="material-switch<?= $this->url ? ' auto-save-property' : '' ?>" data-url="<?= $this->url ?>" data-type="boolean" data-confirm-yes="<?= $this->confirm_yes ? $this->confirm_yes : '' ?>" data-confirm-no="<?= $this->confirm_no ? $this->confirm_no : '' ?>">
    <input type="checkbox"<?= $this->active ? ' checked="true"' : '' ?> name="<?= $this->name ?>">
    <label class="label-<?= $this->label_type ? $this->label_type : 'default' ?>"></label>
</div>
