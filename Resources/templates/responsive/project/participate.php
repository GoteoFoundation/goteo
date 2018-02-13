<?php $this->layout('project/layout') ?>
<?php $this->section('main-content') ?>

<?php $project=$this->project ?>

<div class="panel-group" id="accordion">

	<!-- Backers start -->
	<div class="panel panel-default widget">
		<div class="panel-heading">
			<h2 class="panel-title green-title" >
				<?= $project->num_investors.' '.$this->text('project-menu-supporters') ?>
			</h2>
			<h2 class="panel-title grey-title spacer-10" >
				<?= $project->num_messengers.' '.$this->text('project-collaborations-number') ?>
			</h2>
            <div class="chart-amount"></div>

            <a class="accordion-toggle" data-toggle="collapse" data-target="#collapseTwo">
		        <h2 class="panel-title green-title text-center accordion-title" >
		            <?= $this->text('project-show-donors') ?>
		            <span class="icon glyphicon glyphicon glyphicon-menu-down" aria-hidden="true"></span>
		        </h2>
		    </a>
		</div>
		<div id="collapseTwo" class="panel-collapse collapse in">
			<div class="panel-body">
				<span class="anchor-mark" id="supporters" >
                 </span>
				<?php foreach($this->investors_list as $invest): ?>
					<div class="invest">
						<div class="row info">
							<div class="pull-left">
								<?php if($invest->user!= 'anonymous'): ?>
									<a href="/user/<?= $invest->user ?>"><img class="avatar" src="<?= $invest->avatar->getLink(45, 45, true) ?>">
									</a>
								<?php else: ?>
									<img class="avatar" src="<?= $invest->avatar->getLink(45, 45, true) ?>">
								<?php endif ?>
							</div>
							<div class="pull-left personal">
								<h3 class="name" id="invest-<?= $invest->id ?>">
								<?php if($invest->user!= 'anonymous'): ?>
								<a href="/user/<?= $invest->user ?>"><?= ucfirst($invest->name) ?></a>
								<?php else: ?>
									<?= ucfirst($invest->name) ?>
								<?php endif;?>
								</h3>
								<div class="worth"><?= $this->worthcracy[$invest->worth]->name ?></div>
							</div>
                            <?php if ($invest->droped || $invest->campaign) : ?>
                                <div class="pull-right text-right drop">
                                    <div>
                                        <img width="30" src="<?= SRC_URL . '/assets/img/project/drop.svg' ?>">
                                    </div>
							      <?php if ($invest->method === 'drop'): ?>
									<div class="x2">
									x2
									</div>
							       <?php endif ?>
                                </div>
                            <?php endif ?>
							<div class="pull-right text-right">
								<div>
								<?= $this->text('project-invest') ?>
								</div>
								<div class="amount">
								<?= amount_format($invest->amount) ?>
								</div>
							</div>
						</div>

						<div class="row chart">
							<div class="green <?= $invest->worth==5 ? 'full-bar' : ''  ?> <?= empty($invest->worth) ? 'hidden' : ''  ?>" <?php if($invest->worth!=5): ?> style="width:<?= $invest->worth*20-1 ?>%; border-top-right-radius: 0px; border-bottom-right-radius: 0px;" <?php endif ?> >
							</div>
							<div class="grey <?= empty($invest->worth) ? 'full-bar' : ''  ?>" <?php if(!empty($invest->worth)): ?> style="width:<?= 100-$invest->worth*20 ?>%; border-top-left-radius: 0px; border-bottom-left-radius: 0px;" <?php endif ?> >
							</div>
							<?php if($invest->msg): ?>
							<div class="msg">
								<div class="msg-label"><?= $this->text('project-invest-msg') ?></div>
								<div class="text-font-normal"><?= $invest->msg ?></div>
							</div>
							<?php endif ?>
						</div>

					</div>
				<?php endforeach ?>

				<?=$this->insert('partials/utils/paginator', ['total' => $this->investors_total, 'limit' => $this->investors_limit ? $this->investors_limit : 10, 'a_extra' => 'class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#supporters"'])?>

			</div>
		<!-- end body -->
		</div>
	</div>
	<!-- Backers end -->

	<!-- Collaborations start -->
	<div class="panel panel-default widget no-padding ">
		<a class="accordion-toggle" data-toggle="collapse" data-target="#collapseOne">
			<div class="panel-heading">
				<h2 class="panel-title green-title normalize-padding">
					<?= $this->text('project-menu-messages') ?>
					<span class="icon glyphicon glyphicon-menu-down pull-right" aria-hidden="true"></span>
				</h2>
			</div>
		</a>
		<div id="collapseOne" class="panel-collapse collapse in">
			<div class="panel-body">

			<span class="anchor-mark" id="messages" >
            </span>

			<?php foreach($this->messages as $message): ?>
				<div class="row message no-margin normalize-padding" >
					<div class="pull-left user-name anchor-mark" id="msg-<?= $message->id ?>"><a href="/user/<?= $message->user->id ?>"><?= ucfirst($message->user->name) ?></a></div>
					<div class="pull-right time-ago">
					Hace <?= $message->timeago ?>
					</div>
					<div class="msg-content">
					<?= $message->message ?>
					</div>
					<?php if ($project->userCanEdit($this->get_user())): ?>
                        <a class="delete" href="/dashboard/project/<?= $project->id ?>/supports#comments-<?= $message->id ?>"><?= $this->text('regular-manage') ?></a>
                    <?php endif ?>
				</div>
				<?php if ($this->is_logged() && $project->isApproved()) : ?>
				<div class="row spacer-5 button-msg">
                    <div class="col-xs-5 main-button">
                       <button class="btn btn-block green"><?= $this->text('project-messages-answer_it') ?></button>
                    </div>
                    <div class="box clear-both normalize-padding">
                    	<div class="join-button pull-left" >
                    	</div>
                    	<div class="ajax-comments text-area" data-url="/api/comments" data-thread="<?= $message->id ?>" data-list="#comments-list-<?= $message->id ?>" data-project="<?= $project->id ?>">
                			<textarea id="message-text" name="message" class="message" required></textarea>
                            <p class="text-danger hidden error-message"></p>

                			<div class="col-sm-2 pull-right no-padding">
                   				<button class="btn btn-block green send-comment"><?= $this->text('blog-send_comment-button') ?></button>
               				</div>
                    	</div>
                    </div>
                </div>
                <?php endif ?>

                <div id="comments-list-<?= $message->id ?>">
                    <?php foreach ($message->getResponses() as $child): ?>
                    	<?= $this->insert('project/partials/comment', ['comment' => $child, 'project' => $project]) ?>
            		<?php endforeach ?>
                </div>

            <?php endforeach ?>
            <!-- End messages -->

			</div>
		</div>
	</div>
	<!-- Collaborations end -->

</div>
<!--End panel group -->


<?php $this->replace() ?>
