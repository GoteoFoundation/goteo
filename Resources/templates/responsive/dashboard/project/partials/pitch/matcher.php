<?php
  $matcher=$this->matcher;
  $tagmark = $matcher->getAvailableAmount() ? 'open' : 'completed';
  $tagmark_text= $matcher->name;
  $name=$matcher->name;
  $image_url= $matcher->getOwner()->avatar->getLink(40,40, true);
  $location = explode(",", $matcher->matcher_location);
  if($sphere=$matcher->getMainSphere()) {
    $sphere_name=$sphere->name;
            }
  $link= $matcher->link ? $matcher->link : '/matcher/' . $matcher->id;
  $amount=amount_format($matcher->amount);
  $num_projects=$matcher->getTotalProjects();
  $success=$matcher->getAvailableAmount() ? '-' : $matcher->getSuccessPorcentage().'%';
  $pitch_link = '/project/'.$this->project->id.'/apply/'.$matcher->id.'?select';
  $can_apply = empty($matcher->findProject($this->project->id, 'all'));
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
      <div class="btn btn-block btn-cyan"> <?= $this->t('dashboard-project-pitch-already-applied') ?> </div>
    <?php endif; ?>
  </td>
</tr>
