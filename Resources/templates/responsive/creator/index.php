<?php $this->layout("layout", [
    'bodyClass' => 'project creator',
]);

$permanentProject = $this->permanentProject;
$listOfProjects = $this->listOfProjects;

$share_url = $this->get_url() . '/creator/' . $this->user->id;

$user_twitter = str_replace(
    [
        'https://',
        'http://',
        'www.',
        'twitter.com/',
        '#!/',
        '@'
    ], '', $this->user->twitter);
$author_share = !empty($user_twitter) ? ' '.$this->text('regular-by').' @'.$user_twitter.' ' : '';
$share_title = $author_share;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
$twitter_url = 'http://twitter.com/intent/tweet?text=' . urlencode($share_title . ': ' . $share_url . ' #Goteo');

?>

<?php $this->section('head'); ?>
    <?= $this->insert('creator/partials/styles') ?>
<?php $this->append(); ?>

<?php $this->section('content'); ?>

<main class="container-fluid main-info">
    <div class="container-fluid">
        <div class="row header text-center">
            <h1 class="project-title"><?= $this->markdown($this->ee($permanentProject->name)) ?></h1>
            <div class="project-by"><strong><?= $permanentProject->user->name ?></strong></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $this->insert('project/partials/media', ['project' => $permanentProject ]) ?>
            </div>
        </div>

        <section class="share-social">
            <ul class="info-extra list-inline">
                <li><a class="fa fa-2x fa-twitter" title="" target="_blank" href="<?= $twitter_url ?>"></a></li>
                <li><a class="fa fa-2x fa-facebook" title="" target="_blank" href="<?= $facebook_url ?>"></a></li>
                <li><a class="fa fa-2x fa-telegram" title="" target="_blank" href="https://telegram.me/share/url?url=<?= $share_url ?>&text=<?= urlencode($share_title) ?>"></a></li>
            </ul>
        </section>

        <?= $this->insert('creator/partials/subscriptions', ['project' => $permanentProject, 'subscriptions' => $permanentProject->getRewardsOrderBySubscribable()]) ?>

        <?= $this->insert('creator/partials/posts', ['project' => $permanentProject, 'subscriptions' => $permanentProject->getSubscribableRewards()]) ?>
    </div>
</main>

<?php $this->append(); ?>

<?php $this->section('footer'); ?>
    <?= $this->insert('creator/partials/javascript') ?>
<?php $this->append(); ?>
