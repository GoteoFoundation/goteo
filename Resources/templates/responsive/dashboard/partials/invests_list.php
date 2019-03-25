
    <?php if(!$this->invests): ?>
        <p><?= $this->text('dashboard-certificates-no-pending') ?></p>
    <?php return; endif ?>
    <table class="footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs">#</th>
          <th data-type="string" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th><?= $this->text('invest-amount') ?></th>
          <th data-type="html"><?= $this->text('project-menu-home') ?></th>
          <th data-breakpoints="xs"><?= $this->text('invest-method') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
        $pool_txt = '<span class="label label-info">'.$this->text('invest-pool-method').'</span>';
        $donate_txt = '<span class="label label-success">'.$this->text('invest-status-donated').'</span>';
        foreach($this->invests as $invest):
     ?>
        <tr>
          <td><?= $invest->id ?></td>
          <td><?= date_formater($invest->invested) ?></td>
          <td>
            <?= $invest->amount ? \amount_format($invest->amount) : '' ?>
            <?= $invest->amount&&$invest->donate_amount ? '<br>' : '' ?>
            <?= $invest->donate_amount ? \amount_format($invest->donate_amount) : '' ?> 
          </td>
          <td><?php 
                if($invest->project){ 
                    if($invest->isOnPool()) 
                      echo '<span style="text-decoration: line-through">' . $invest->getProject()->name . '</span> ' . $pool_txt;
                    else 
                      echo $invest->getProject()->name;
                    if($invest->donate_amount)
                      echo '<br>'.$donate_txt;
                }
                elseif($invest->isDonated())
                  echo $donate_txt;
                else
                  echo $pool_txt; 
              ?>  
          </td>
          <td><?= $invest->getMethod()->getName() ?></td>
        </tr>
    <?php endforeach ?>
      </tbody>
    </table>
