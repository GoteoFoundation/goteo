<?php

$this->layout("translate/layout");


$this->section('translate-content');

$fields = $this->fields;
$q = $this->get_query('q');
$query = $this->get_query() ? '?'. http_build_query($this->get_query()) : '';

?>
<div class="dashboard-content">
  <div class="inner-container">

    <table class="footable table table-striped">
      <thead>
        <tr>
          <th data-type="html" data-breakpoints="xs">ID</th>
          <?php
            $i=0;
            foreach($fields as $field => $type):
              if($field == 'pending') continue;
          ?>
          <th data-type="html" <?= $i != 0 ? ' data-breakpoints="xs"' :'' ?>><?= $this->text("translator-field-$field") ?></th>
          <?php
            $i++;
            endforeach;
          ?>
          <th data-type="html"><?= $this->text('translator-translations') ?></th>
          <th data-type="html">&nbsp;</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach($this->list as $ob): ?>
            <tr>
              <td><?= $q ? str_ireplace($q, '<span class="badge">' . $q . '</span>', $ob->id) : $ob->id ?></td>
              <?php
                foreach($fields as $field => $type):
                  if($field == 'pending') continue;
                  $val = $this->text_truncate(strip_tags($ob->{$field}));
              ?>
                <td><?= $q ? str_ireplace($q, '<span class="badge">' . $q . '</span>', $val) : $val ?></td>
              <?php endforeach ?>
              <td><?= implode(", ", array_map(function($l) use ($ob) {
                if(in_array($l, $ob->pendings)) {
                  return '<span class="text-danger" title="' . $this->text('translator-pending') . '">' . $l . '</span>';
                }
                return $l;
                }, $ob->translations)) ?></td>
              <td><a class="btn btn-sm btn-default" href="/translate/<?= $this->zone ?>/<?= $ob->id . $query ?>"><span class="glyphicon glyphicon-pencil"></span> <?= $this->text('regular-edit') ?></a></td>
            </tr>

        <?php endforeach ?>

      </tbody>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>
  </div>
</div>

<?php $this->replace() ?>
