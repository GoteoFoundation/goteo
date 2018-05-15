<?php $this->layout('project/layout') ?>
<?php $this->section('main-content') ?>

<div class="widget post">
    <h2 class="title"><?= $this->post->title ?></h2>
    <div class="date"><?= $this->post->fecha ?></div>
    <?php if (count($this->post->gallery) > 1) : ?>
        <!-- carousel slider -->
            <div id="postCarousel" class="carousel slide spacer-20" data-ride="carousel" data-interval="false">

            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php for($slide=0;$slide<count($this->post->gallery);$slide++): ?>
                <li data-target="#postCarousel" data-slide-to="<?= $slide?>" <?= !$slide ? 'class="active"' : '' ?> ></li>
                <?php endfor; ?>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <?php foreach($this->post->gallery as $key => $image): ?>
                    <div class="item <?= !$key ? 'active' : '' ?>">
                         <img src="<?= $image->getLink(700, 700) ?>" class="img-responsive">
                    </div>
                  <?php endforeach ?>
               </div>
                 <!-- Left and right controls -->
                <a class="left carousel-control" href="#postCarousel" role="button" data-slide="prev">
                  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>

                <a class="right carousel-control" href="#postCarousel" role="button" data-slide="next">
                   <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                   <span class="sr-only">Next</span>
                </a>

               </div>
    <?php elseif( $this->post->image instanceof \Goteo\Model\Image ): ?>
        <div class="spacer-20">
            <img src="<?= $this->post->image->getLink(700, 700); ?>" class="img-responsive" alt="<?= $this->post->title ?>" >
        </div>
    <?php endif; ?>

    <div class="general-text spacer-20">
        <?= $this->markdown($this->post->text) ?>
    </div>

    <?php if (!empty($this->post->media->url)) :
            $embed = $this->post->media->getEmbedCode();
            if (!empty($embed))  : ?>
                <div class="embed-responsive embed-responsive-16by9 spacer-20">
                <?= $embed; ?>
                </div>
            <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($this->post->legend)) : ?>
        <div class="embed-legend">
            <?= $this->post->legend; ?>
        </div>
    <?php endif; ?>

    <h3><?= $this->text('blog-coments-header') ?></h3>

    <?php if (!empty($this->post->comments)): ?>
        <?php foreach ($this->post->comments as $child) : ?>
        <div id="child-msg-<?= $child->id ?>" class="row no-margin normalize-padding message child<?= ($child->user->id == $project->owner) ? ' owner' : ' no-owner' ?> no-margin normalize-padding">
            <?php if($child->user->id != $project->owner): ?>
            <div class="pull-left anchor-mark" id="comment<?= $child->id ?>">
                <img class="avatar" src="<?= $child->user->avatar->getLink(45, 45, true); ?>">
            </div>
            <?php endif; ?>
            <div class="pull-left user-name" ><?= ucfirst($child->user->name) ?></div>
            <div class="pull-right time-ago">
                Hace <?= $child->timeago ?>
             </div>
            <div class="msg-content">
                 <?= $child->text?>
            </div>
        </div>

        <?php endforeach ?>
    <?php endif; ?>

    <span id="add-comment" class="anchor-mark" ></span>

    <?php if(!empty($_SESSION['user'])): ?>

    <div class="row standard-margin-top">
        <form class="col-xs-12" method="post" action="/message/post/<?= $this->post->id ?>/<?= $this->project->id ?>">
            <div class="alert alert-danger" role="alert" id="error" style="display:none;">
            </div>
            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            <div class="col-sm-4 no-padding margin-2 standard-margin-top">
                <button type="submit" class="btn btn-block btn-success" id="send-msg" value=""><?= $this->text('blog-send_comment-button') ?></button>
            </div>
        </form>
    </div>

    <?php else: ?>
        <p><a href="/login?return=<?= urlencode($this->get_uri().'#add-comment') ?>"><?= $this->text('project-comment-start-sesion') ?></a> <?= $this->text('project-comment-start-sesion-2') ?></p>
    <?php endif; ?>

</div>
<!-- End widget -->

<?php $this->replace() ?>
