<?= $this->insert('dashboard/project/partials/boolean', [
    'active' => $this->value,
    'label_type' => 'cyan',
    'url' => $this->raw('ob')->getApiProperty('active'),
    'confirm_yes' => $this->text('active-confirm-yes'),
    'confirm_no' => $this->text('active-confirm-no') ]) ?>
