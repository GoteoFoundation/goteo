<?php

use Goteo\Library\Text;

$bodyClass = 'faq';

include 'view/prologue.html.php';

include 'view/header.html.php';

$go_up = Text::get('regular-go_up');

?>
		<div id="sub-header-secondary">
            <div class="clearfix">
                <h2>GOTEO<span class="red">FAQ</span></h2>
                <ul class="share-goteo">
					<li class="twitter"><a href="#"><?php echo Text::get('regular-share-twitter'); ?></a></li>
					<li class="facebook"><a href="#"><?php echo Text::get('regular-share-facebook'); ?></a></li>
					<li class="rss"><a href="#"><?php echo Text::get('regular-share-rss'); ?></a></li>
				</ul>
            </div>
        </div>
        <div id="main" class="threecols">
			<div id="faq-content">
				<h2><?php echo Text::get('regular-faq') ?></h2>
				<?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
					<div class="widget faq-content-module">
						<h3><?php echo $sectionName; ?></h3>
						<ol>
							<?php foreach ($this['faqs'][$sectionId] as $question)  : ?>
								<li>
									<a name="q<?php echo $question->id; ?>" />
									<h4 style="color:#20b3b2;"><?php echo $question->title; ?></h4>
									<p><?php echo $question->description; ?></p>
									<a href="#"><?php echo $go_up; ?></a>
								</li>
							<?php endforeach; ?>
						</ol>
					</div>
				<?php endforeach; ?>
			</div>
			<div id="faq-sidebar">
				<?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
					<div class="widget faq-sidebar-module">
						<h3 style="border-bottom-color: #20b3b2;" class="supertitle"><?php echo $sectionName; ?></h3>
						<ol>
							<?php foreach ($this['faqs'][$sectionId] as $question)  : ?>
								<li><a style="color: #20b3b2;" href="#q<?php echo $question->id; ?>"><?php echo $question->title; ?></a></li>
							<?php endforeach; ?>
						</ol>
					</div>
				<?php endforeach; ?>
				<div class="widget faq-sidebar-module">
					<h3 class="supertitle ask"><?php echo Text::get('regular-faq') ?></h3>
					<p class="ask-content"><?php echo Text::get('faq-ask-question'); ?></p>
					<a class="btn-ask" href="#"><?php echo Text::get('regular-ask'); ?></a>
				</div>
			</div>
        </div>        
	<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>