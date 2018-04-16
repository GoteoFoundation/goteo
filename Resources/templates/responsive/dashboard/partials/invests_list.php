
    <?php if(!$this->invests): ?>
        <p><?= $this->text('dashboard-certificates-no-pending') ?></p>
    <?php return; endif ?>
    <table class="footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs">#</th>
          <th data-type="string" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th><?= $this->text('invest-amount') ?></th>
          <th data-breakpoints="xs"><?= $this->text('invest-method') ?></th>
          <th data-type="html"><?= $this->text('project-menu-home') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
        $pool_txt = '<span class="label label-info">'.$this->text('invest-pool-method').'</span>';
        foreach($this->invests as $invest):
     ?>
        <tr>
          <td><?= $invest->id ?></td>
          <td><?= date_formater($invest->invested) ?></td>
          <td><?= amount_format($invest->amount) ?></td>
          <td><?= $invest->getMethod()->getName() ?></td>
          <td><?= $invest->project ? ($invest->isOnPool() ? '<span style="text-decoration: line-through">' . $invest->getProject()->name . '</span> ' . $pool_txt : $invest->getProject()->name) : $pool_txt ?></td>
        </tr>
    <?php endforeach ?>
      </tbody>
    </table>
