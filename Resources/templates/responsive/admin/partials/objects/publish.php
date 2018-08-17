<?= $this->insert('dashboard/project/partials/boolean', [
    'active' => $this->value,
    'label_type' => 'cyan',
    'url' => $this->raw('ob')->getApiProperty('publish'),
    'confirm_yes' => $this->text('publish-confirm-yes'),
    'confirm_no' => $this->text('publish-confirm-no')
]) ?>
