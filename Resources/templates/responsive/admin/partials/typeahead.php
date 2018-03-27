<div class="admin-typeahead" data-sources="project,call,user,channel">
  <div class="form-group has-feedback<?= ($this->value ? ' has-error' : '') ?>">

    <input class="typeahead form-control" autocomplete="off" type="text" placeholder="<?= $this->placeholder ?: $this->text('admin-search-global') ?>" value="<?= $this->value ?>">
    <span class="fa fa-search form-control-feedback" aria-hidden="true"></span>

    <?php if($this->value) {
      echo '<span class="help-block text-right"><a href="' . $this->get_pathinfo() . '" class="pronto text-danger"><i class="fa fa-close"></i> ' . $this->text('admin-remove-filters') . '</a> &nbsp;</span>';
    } ?>
  </div>
</div>
