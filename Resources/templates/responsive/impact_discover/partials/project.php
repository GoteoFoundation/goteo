<?php
    $project = $this->project;
    $sdgs = $project->getSdgs();

    $footprints = [];

    foreach($sdgs as $sdg) {
        $footprints = array_merge($footprints, array_column($sdg->getFootprints(), null, 'id'));
    }
?>

<img src="<?= ($project->image)? $project->image->getLink(700,0,true) : '' ?>" class="bg-project" data-footprint="<?= (current($footprints))? current($footprints)->id : '' ?>">

<?php if (!empty($footprints)): ?>
    <div class="project-footprint">
        <img src="assets/img/footprint/<?= current($footprints)->id ?>.svg" alt="<?= current($footprints)->name ?>" class="footprint" />
    </div>
<?php endif; ?>
<div class="project-description">
    <h2><?= $project->title ?></h2>
    <p><?= $this->text('regular-by') ?> <a href="/user/profile/<?= $project->user->id ?>"> <?= $project->user->name ?></a></p>
</div>
