<?php
  $channel=$this->channel;
  $can_apply = empty($channel->findProject($this->project->id));          
  $tagmark = $can_apply ? 'open': 'active';
  $tagmark_text=$channel->name;
  $name=$channel->name;
  $image_url= $channel->getHomeImage()->getLink(40,40, true);
  $location = [$channel->location];
  // if($sphere=$channel->getMainSphere()) {
  //    $sphere_name=$sphere->name;
            // }
  $link= '/channel/' . $channel->id;
  // $amount=amount_format($channel->amount);
  $num_projects=$channel->getSummary()->projects;
  // $success=$channel->getAvailableAmount() ? '-' : $channel->getSuccessPorcentage().'%';
  $pitch_link = '/channel/'.$channel->id.'/apply/'. $this->project->id;
?>

<tr class="channel">
  <td data-container="body" data-toggle="tooltip" title="<?= $name ?>">
    <a class="<?= $tagmark ?>" href="<?= $link ?>" >
      <img alt="<?= $name ?>" class="img-owner" src="<?= $image_url ?>">
      <?= $tagmark_text ?>
    </a>
  </td>
  <td>
    <?= $this->t('regular-channel') ?>
  </td>
  <?php if (!$this->hide_spheres): ?>
    <td>
      <?= $sphere_name ?>
    </td>
    <?php endif; ?>
  <td>
    <?= $location[0] ?>
  </td>
  <td>
    <?= $amount ?>
  </td>
  <?php if (!$this->hide_projects): ?>
    <td>
      <?= $num_projects ?>
    </td>
  <?php endif; ?>
  <td>
    <?php if($can_apply): ?>

      <?=	'<a href="'.$pitch_link.'" tabindex="0" class="btn btn-block btn-lilac" target="_blank">'.$this->text('home-matchfunding-open').'</a>';
      ?>
    <?php else: ?>
      <div class="btn btn-block btn-cyan"> <?= $this->t('dashboard-project-pitch-already-applied') ?> </div>
    <?php endif; ?>
  </td>
</tr>
