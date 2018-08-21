<?php
// Inspired in https://codepen.io/zavoloklom/pen/IGkDzT

$list = $this->raw('list');
if($list):
    $first = current($list);

$total = $this->total ? $this->total : 0;
$limit = $this->limit ? $this->limit : 0;
$pag = $this->get_query('pag') ? $this->get_query('pag') : 0;

?>
  <div class="table-responsive-vertical shadow-z-1">
  <table class="material-table table-hover model-<?= $first->getModelName() ?>" data-total="<?= $total ?>" data-limit="<?= $limit ?>" data-page=<?= $pag ?>>
    <thead><tr>
    <?php foreach($first->getKeys() as $key):?>
        <th data-key="<?= $key ?>"><?= $first->getLabel($key) ?></th>
    <?php endforeach ?>
    </tr></thead>
    <tbody>
    <?php foreach($list as $entry): ?>
        <tr id="tr-<?= $entry->getId() ?>">
        <?php
        $t = count($entry);
        foreach($entry as $key => $val):
            $vars = ['value' => $val, 'key' => $key, 'ob' => $entry, 'link' => '', 'class' => ''];
        ?>
            <td data-title="<?= $entry->getLabel($key) ?>" data-key="<?= $key ?>" data-value="<?= $entry->getRawValue($key) ?>" class="td-<?= $key ?>"><?= $this->insertIf("admin/partials/objects/$key", $vars) ?: $this->insert("admin/partials/objects/text", $vars) ?></td>
        <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    </tbody>
  </table>
  </div>

  <?= $this->insert('partials/utils/paginator', ['total' => $total, 'limit' => $limit, 'pag' => $pag, 'a_extra' => 'class="pronto"']) ?>

<?php else: ?>
    <p class="alert alert-info"><?= $this->text('admin-empty-list') ?></p>
<?php endif ?>
