<?php

$project=$this->project;

if($this->is_pronto()):
    echo json_encode([
        'title' => $this->project->name,
        'content' => $this->supply('main-content')
        ]);
    return;
endif;

if($project->gallery[0])
    $meta_img=$project->secGallery['play-video'][0] ? $this->project->secGallery['play-video'][0]->imageData->getLink(780, 478, false, true) : $project->gallery[0]->imageData->getLink(700, 700, false, true);


$this->layout('layout', [
    'bodyClass' => 'project',
    'title' => $this->project->name,
    'meta_description' => $this->project->subtitle,
    'tw_image' => $meta_img,
    'og_image' => $meta_img
    ]);


$this->section('lang-metas');
    $langs = $project->getLangs();
    if (count($langs) > 1) {
        foreach($langs as $l => $lang) {
            if($l == $this->lang_current()) continue;
            echo  "\n\t" . '<link rel="alternate" href="' . $this->lang_url($l) .'" hreflang="' . $l . '" />';
        }
    }
$this->replace();

$this->section('sidebar-header');
    echo $this->insert('project/widgets/micro', ['project' => $project, 'admin' => $this->admin]);
$this->replace();


$this->section('content');

?>

<div class="container-fluid main-info"  >
	<div class="container-fluid">
		<div class="row header text-center">
			<h1 class="project-title"><?= $project->name ?></h1>
			<div class="project-by"><a href="/user/<?= $project->owner ?>"><?= $project->user->name ?></a></div>
		</div>

		<div class="row">
			<div class="col-sm-8">
				<?= $this->insert('project/partials/media', ['project' => $project ]) ?>
			</div>
			<div class="col-sm-4">
				<?= $this->insert('project/partials/meter', ['project' => $project ]) ?>
			</div>
		</div>

		<!-- Tags and share info -->
		<div class="row">

		<?= $this->insert('project/partials/main_extra', ['project' => $project ]) ?>

		</div>
</div>

<!-- End container fluid -->

<div class="container-fluid section">
	<div class="col-sm-8 section-content" id="project-tabs">

	<?= $this->supply('main-content') ?>

	</div>

	<!-- end Panel group -->

	<div class="col-sm-4 side">

	<?= $this->insert('project/partials/side', ['project' => $project]) ?>

	</div>

	<!-- end side -->

</div>

<aside class="related-projects">
    <div class="container-fluid">
		<h2 class="green-title">
		<?= $this->text('project-related') ?>
		</h2>

		<div class="row">
	    <?php foreach ($this->related_projects as $related_project) : ?>

	              <div class="col-sm-6 col-md-4 col-xs-12 spacer">
	                <?= $this->insert('project/widgets/normal', ['project' => $related_project, 'admin' => false]) ?>
	              </div>
	    <?php endforeach ?>
    	</div>

    </div>
</aside>


<!-- sticky menu -->

<div class="sticky-menu" data-offset-top="880" data-spy="affix">
	<div class="container-fluid">
		<div class="row">
			<a href="/project/<?= $project->id ?>" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
				<div class="home col-sm-2 hidden-xs sticky-item <?= $this->show=='home' ? 'current' : '' ?>">
					<img class="" src="<?= SRC_URL . '/assets/img/project/home.png' ?>">
		            <span class="label-sticky-item"><?= $this->text('project-menu-home') ?></span>
				</div>
			</a>
			<a href="/project/<?= $project->id ?>/updates"  class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
				<div class="updates col-sm-2 hidden-xs sticky-item <?= $this->show=='updates' ? 'current' : '' ?>">
					<img class="" src="<?= SRC_URL . '/assets/img/project/news.png' ?>">
	                <span class="label-sticky-item"><?= $this->text('project-menu-news') ?></span>
				</div>
			</a>
			<a href="/project/<?= $project->id ?>/participate" class="pronto" data-pronto-target="#project-tabs" data-pronto-scroll-to="#project-tabs-menu">
				<div class="participate col-sm-2 hidden-xs sticky-item <?= $this->show=='participate' ? 'current' : '' ?>">
					<img class="" src="<?= SRC_URL . '/assets/img/project/participate.png' ?>">
	                <span class="label-sticky-item"><?= $this->text('project-menu-participate') ?></span>
				</div>
			</a>

            <div class="col-xs-6 col-sm-3 col-md-2 col-md-offset-2 col-xs-offset-1 sticky-button">
                <?php if($project->inCampaign()): ?>
                    <a href="/invest/<?= $project->id ?>"><button class="btn btn-block side-pink"><?= $this->text('project-regular-support') ?></button></a>
                <?php endif ?>
            </div>

            <?php if(!$this->get_user() ): ?>
        		<a href="/project/favourite/<?= $project->id ?>">
    		<?php endif ?>
	            <div class="pull-left text-right favourite <?= $this->get_user()&&$this->get_user()->isFavouriteProject($project->id) ? 'active' : '' ?>" >
	                <span class="heart-icon glyphicon glyphicon-heart" aria-hidden="true"></span>
	                <span> <?= $this->text('project-view-metter-favourite') ?></span>
	            </div>
            <?php if(!$this->get_user() ): ?>
        		</a>
    		<?php endif ?>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="widgetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $this->text('project-spread-pre_widget') ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
        	<div class="col-sm-6">
        	<?= $this->raw('widget_code') ?>
        	</div>
        	<div class="col-sm-6">
     			<textarea class="widget-code" onclick="this.focus();this.select()" readonly="readonly" ><?= $this->widget_code ?></textarea>
        	</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>


<?= $this->insert('project/partials/chart_costs.php', ['project' => $project]) ?>

<?= $this->insert('project/partials/chart_amount.php', ['project' => $project]) ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

    $(function(){
         // $(window).on("pronto.request", function(e){
         // });

         $(window).on("pronto.render", function(e){
            $("div.project-menu div.item, div.sticky-item").removeClass("current");

            $('table.footable').footable();
            var url = e.currentTarget.location.href;
            var section = 'home';
            if(url.indexOf('/updates') !== -1) section = 'updates';
            if(url.indexOf('/participate') !== -1) section = 'participate';
            // console.log('section', section);

            $("."+section).addClass("current");

            $("a.accordion-toggle").click(function(){
            if($(this).hasClass('collapsed'))
                $(this).find('span.glyphicon').removeClass("glyphicon-menu-down").addClass("glyphicon-menu-up");
            else
                $(this).find('span.glyphicon').removeClass("glyphicon-menu-up").addClass("glyphicon-menu-down");
            });

            $("#infoCarousel").swiperight(function() {
                $(this).carousel('prev');
            });

            $("#infoCarousel").swipeleft(function() {
                $(this).carousel('next');
            });

            $('#go-top').click(function(){
                $('body,html').animate({scrollTop : 0}, 500);
                return false;
            });

            $('div.button-msg').click(function(){
                $(".box").hide();
                $("div.button-msg .main-button button").removeClass("message-grey").addClass("green");
                $(this).find('.main-button button').removeClass("green").addClass("message-grey");
                $(this).find('.box').show();
            });

            $("#reset-chart").click(function(){
                $( "div.chart-costs" ).fadeOut( "slow", function() {
                    printCosts();
                 });
                 $( "div.chart-costs" ).fadeIn("slow");
            });

         });

        var _favourite_ajax = function() {

            var user= '<?= $this->get_user()->id ?>';

            if(user)
            {
                $.ajax({
                    url: "/project/favourite/<?= $project->id ?>",
                    data: {   },
                    type: 'post',
                    success: function(result){
                        $(".favourite").addClass('active');
                    }
                });
            }

        };

        var _delete_favourite_ajax = function() {

            var user= '<?= $this->get_user()->id ?>';

            if(user)
            {
                $.ajax({
                    url: "/project/delete-favourite",
                    data: { 'project' : '<?= $project->id ?>', 'user' : user  },
                    type: 'post',
                    success: function(result){
                        $(".favourite").removeClass('active');
                    }
                });
            }

        };

        $(".favourite").click(function(){
            if($(this).hasClass('active'))
                _delete_favourite_ajax();
            else
                _favourite_ajax();
        });

        $("a.accordion-toggle").click(function(){
            if($(this).hasClass('collapsed'))
                $(this).find('span.glyphicon').removeClass("glyphicon-menu-down").addClass("glyphicon-menu-up");
            else
                $(this).find('span.glyphicon').removeClass("glyphicon-menu-up").addClass("glyphicon-menu-down");
        });

        $("div.widget.rewards a.accordion-toggle").click(function(){
            if($(this).hasClass('collapsed'))
                $(this).parent().removeClass('rewards-collapsed');
            else
                $(this).parent().addClass('rewards-collapsed');
        });

        $("#infoCarousel").swiperight(function() {
            $(this).carousel('prev');
        });

        $("#infoCarousel").swipeleft(function() {
          $(this).carousel('next');
        });

        $('#go-top').click(function(){
            $('body,html').animate({scrollTop : 0}, 500);
            return false;
        });

        $('div.button-msg').click(function(){
            $(".box").hide();
            $("div.button-msg .main-button button").removeClass("message-grey").addClass("green");
            $(this).find('.main-button button').removeClass("green").addClass("message-grey");
            $(this).find('.box').show();
        });

        $("#show-link").click(function(){
            $("#link-box").toggle(600);
        });

        $("#reset-chart").click(function(){
             $( "div.chart-costs" ).fadeOut( "slow", function() {
                printCosts();
             });
             $( "div.chart-costs" ).fadeIn("slow");
        });

        $("div.row.call-info").hover(function(){
            $(".info-default-call").toggle();
            $(".info-hover-call").toggle();
        });


        // Send comments
        $(document).on('click', '.ajax-comments .send-comment', function (e) {
            e.preventDefault();
            var $parent = $(this).closest('.ajax-comments');
            var $list = $($parent.data('list'));
            var url = $parent.data('url');
            var $error = $parent.find('.error-message');
            var $textarea = $parent.find('[name="message"]');
            var data = {
                message: $textarea.val(),
                thread: $parent.data('thread'),
                project: $parent.data('project'),
                view: 'project'
            }

            $error.addClass('hidden').html('');
            $.post(url, data, function(data) {
                // console.log('ok!', data);
                $list.append(data.html);
                $textarea.val('');
                $parent.closest('.box').hide();
              }).fail(function(data) {
                var error = JSON.parse(data.responseText);
                // console.log('error', data, error)
                $error.removeClass('hidden').html(error.error);
              });
        });



    });

// @license-end
</script>

<?= $this->insert('partials/facebook_pixel', ['pixel' => $this->project->facebook_pixel]) ?>

<?php $this->append() ?>


