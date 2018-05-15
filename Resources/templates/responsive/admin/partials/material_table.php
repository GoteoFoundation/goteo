<?php
// Inspired in https://codepen.io/zavoloklom/pen/IGkDzT

$list = $this->raw('list');
if($list):
    $first = current($list);
?>
  <div class="table-responsive-vertical shadow-z-1">
  <table class="material-table table-hover model-<?= $first->getModelName() ?>">
    <thead><tr>
    <?php foreach($first->getDefaultKeys() as $key):?>
        <th><?= $first->getLabel($key) ?></th>
    <?php endforeach ?>
    </tr></thead>
    <tbody>
    <?php foreach($list as $entry): ?>
        <tr id="tr-<?= $entry->getId() ?>">
        <?php
        $t = count($entry);
        foreach($entry as $key => $val):
            $vars = ['value' => $val, 'ob' => $entry, 'link' => ''];
        ?>
            <td data-title="<?= $entry->getLabel($key) ?>"><?= $this->insertIf("admin/partials/objects/$key", $vars) ?: $this->insert("admin/partials/objects/text", $vars) ?></td>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    </tbody>
  </table>
  </div>

  <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10, 'a_extra' => 'class="pronto"']) ?>

<?php else: ?>
    <p class="alert alert-info"><?= $this->text('admin-empty-list') ?></p>
<?php endif ?>
