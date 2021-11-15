<?php
    $project = $this->project;
    $sdgs = $project->getSdgs();

    $footprints = [];

    foreach($sdgs as $sdg) {
        $footprints = array_merge($footprints, array_column($sdg->getFootprints(), null, 'id'));
    }

    $footprints = array_unique($footprints, SORT_REGULAR);
?>

<a href="/project/<?= $project->id ?>">
    <img src="<?= ($project->image)? $project->image->getLink(700,0,true) : '' ?>" class="bg-project" data-footprint="<?= (current($footprints))? current($footprints)->id : '' ?>">
</a>

<?php if (!empty($footprints)): ?>
    <div class="project-footprint">
        <?php foreach($footprints as $footprint): ?>
            <img src="assets/img/footprint/<?= $footprint->id ?>.svg" alt="<?= $footprint->name ?>" class="footprint" />
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<div class="project-description">
    <h2><?= $project->title ?></h2>
    <p><?= $this->text('regular-by') ?> <a href="/user/profile/<?= $project->user->id ?>"> <?= $project->user->name ?></a></p>
</div>
