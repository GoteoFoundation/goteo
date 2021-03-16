<?php
  $call = $this->call;
  $tagmark = $call->getTagmark();
  $tagmark_text= $call->status>=4 ? $this->text('call-short-tagmark-'.$tagmark) : $this->text('call-until').' '.date('d/m/Y', strtotime($call->getConf('date_stage1_out')))  ;
  $name=$call->name;
  $image_url= $call->user->avatar->getLink(40, 40, true);
  $location = explode(",", $call->call_location);
  
  if($sphere=$call->getMainSphere()) {
    $sphere_name=$sphere->name;
  }

  $link= $call->status>=4 ? 'call/'.$call->id.'/projects' : 'call/'.$call->id;
  $amount=amount_format($call->amount);
  $num_projects=$call->status==3 ? $call->getConf('max_projects') : $call->getPublished();
  $success=$call->status==5 ? $call->getSuccessPorcentage().'%' : '-';
  $pitch_link = '/project/'.$this->project->id.'/apply/'.$call->id.'?select';
  if ($project_call = $project->getCall())
    $can_apply = $project->call != $call->id;
  else
    $can_apply = true;
?>

<tr>
  <td data-container="body" data-toggle="tooltip" title="<?= $name ?>">
    <a class="<?= $tagmark ?>" href="<?= $link ?>" >
      <img alt="<?= $name ?>" class="img-owner" src="<?= $image_url ?>">
      <?= $tagmark_text ?>
    </a>
  </td>
  <td>
    <?= $this->t('regular-call') ?>
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
      <div class="btn btn-block btn-cyan" disabled> <?= $this->t('dashboard-project-pitch-already-applied') ?> </div>
    <?php endif; ?>
  </td>
</tr>
