<?= $this->insert('dashboard/project/partials/boolean', [
    'active' => $this->value,
    'label_type' => 'cyan',
    'url' => '/api/blog/' . $this->raw('ob')->getSlug() . '/property/publish',
    'confirm_yes' => $this->text('publish-confirm-yes'),
    'confirm_no' => $this->text('publish-confirm-no') ]) ?>
