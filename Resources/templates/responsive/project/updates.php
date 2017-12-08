<?php $this->layout('project/layout') ?>
<?php $this->section('main-content') ?>

<?php
    $num=0;
    $milestones = $this->milestones;
    if(!is_array($milestones)) $milestones = array();
    $total=count($milestones);
foreach($milestones as $update):
    $num++;
    $identical_date=0;

    if($current_date!=$update->date)
    {
        $date=new Datetime($update->date);
        $month=strtolower(strftime("%B",$date->getTimestamp()));
        $current_date=$update->date;
    }
    else
        $identical_date=1;

?>

<div class="row spacer new">
    <div class="col-sm-3 col-xs-4 date" <?= $update->post ? '' : 'id="milestone-'.$update->milestone->id.'"' ?> >
        <?php if(!$identical_date): ?>
            <div class="month">
            <?= $this->text('date-'.$month); ?>
            </div>
            <div class="day">
            <?= $date->format('d'); ?>
            </div>
            <?php if($num<$total): ?>
                <div class="extra">
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="extra-top">
            </div>
            <div class="repeat" >
            </div>
            <?php if($num<$total): ?>
                <div class="extra-bottom">
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if($update->post): ?>
    <div class="col-sm-9 col-xs-8 content">
        <a class="pronto" data-pronto-target="#project-tabs" href="<?= '/project/'.$this->project->id.'/updates/'.$update->post->id ?>">
            <h2><?= $update->post->title ?></h2>
        </a>

        <?php if (!empty($update->post->media->url)) :
            $embed = $update->post->media->getEmbedCode();
            if (!empty($embed))  : ?>
                <div class="embed-responsive embed-responsive-16by9 spacer-20">
                <?= $embed; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($update->post->text): ?>
        <div class="description spacer-20">
            <?= $this->text_truncate($this->text_plain($update->post->text), 250) ?>
            <a class="pronto" data-pronto-target="#project-tabs" href="<?= '/project/'.$this->project->id.'/updates/'.$update->post->id ?>"><span class="read-more">[<?= $this->text('regular-read_more') ?>]</span></a>
        </div>
        <?php endif ?>
        <?php if($update->post->num_comments): ?>
            <div class="comments spacer-20">
            <?= sprintf("%02d", $update->post->num_comments).' '.$this->text('blog-coments-header') ?>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
        <div class="col-sm-9 col-xs-8 milestone-content">

            <div class="inside">
                <?php if(!empty($update->milestone->link)): ?>
                    <?php if($update->milestone->link != "invest"): ?>
                        <a class="pronto expand-link" data-pronto-target="#project-tabs" href="/project/<?= $this->project->id . ($update->milestone->link === '/' ? '' : $update->milestone->link) ?>"></a>
                    <?php else: ?>
                        <a class="expand-link" href="/invest/<?= $this->project->id ?>" target="_blank"></a>
                    <?php endif;?>
                <?php endif?>
                <span class="pointer">
                    <img width="9" src="<?= SRC_URL . '/assets/img/project/updates/green-pointer.svg' ?> ">
                </span>

                <?php if(!empty($update->milestone->image_emoji)): ?>
                    <img class="emoji" src="<?= $update->milestone->image_emoji->getLink(50, 60, false) ?>" >
                <?php endif; ?>
                <?= $update->milestone->description ?>
                    <?php
                        $URL = \SITE_URL;
                        $share_url = $URL . '/project/' . $this->project->id.'/updates#milestone-'.$update->milestone->id;
                        $facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($update->milestone->description);
                        $twitter_url = 'http://twitter.com/home?status=' . urlencode($update->milestone->description . ': ' . $share_url . ' #Goteo');
                   ?>
                   <a href="<?= $twitter_url ?>" target="_blank">
                        <img class="twitter-icon" width="20" src="<?= SRC_URL . '/assets/img/project/updates/twitter-milestone.png' ?> ">
                    </a>
                    <a href="<?= $facebook_url ?>" target="_blank">
                        <img class="facebook-icon" width="20" src="<?= SRC_URL . '/assets/img/project/updates/facebook-milestone.png' ?> ">
                    </a>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php endforeach ?>

<div id="go-top" class="row btn-up">
    <div class="col-md-offset-5 col-md-3 col-sm-offset-4 col-sm-4 text-center">
        <button class="btn btn-block dark-grey"><?= $this->text('project-go-up') ?></button>
    </div>
</div>

<?php $this->replace() ?>
