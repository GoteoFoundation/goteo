<?php $this->layout("layout", [
    'bodyClass' => 'project creator',
]);

$permanentProject = $this->permanentProject;
$listOfProjects = $this->listOfProjects;
?>

<?php $this->section('head'); ?>
    <?= $this->insert('creator/partials/styles') ?>
<?php $this->append(); ?>

<?php $this->section('content'); ?>

<main class="container-fluid main-info">
    <div class="container-fluid">
        <div class="row header text-center">
            <h1 class="project-title"><?= $this->markdown($this->ee($permanentProject->name)) ?></h1>
            <div class="project-by"><a href="/user/<?= $permanentProject->owner ?>"><?= $permanentProject->user->name ?></a></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $this->insert('project/partials/media', ['project' => $permanentProject ]) ?>
            </div>
        </div>

        <?= $this->insert('creator/partials/subscriptions', ['project' => $permanentProject, 'subscriptions' => $permanentProject->getSubscribableRewards()]) ?>

        <?= $this->insert('creator/partials/posts', ['project' => $permanentProject, 'subscriptions' => $permanentProject->getSubscribableRewards()]) ?>
    </div>
</main>

<?php $this->append(); ?>
