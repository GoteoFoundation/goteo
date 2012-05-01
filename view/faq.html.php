<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$current = $this['current'];

$bodyClass = 'faq';

include 'view/prologue.html.php';
include 'view/header.html.php';

$go_up = Text::get('regular-go_up');

?>
<script type="text/javascript">
    $(function(){
        $(".faq-question").click(function (event) {
            event.preventDefault();

            if ($($(this).attr('href')).is(":visible")) {
                $($(this).attr('href')).hide();
            } else {
                $($(this).attr('href')).show();
            }

        });

        var hash = document.location.hash;
        if (hash != '') {
            $(hash).show();
        }
    });


</script>
<?php if (\NODE_ID == \GOTEO_NODE) : ?>
		<div id="sub-header-secondary">
            <div class="clearfix">
                <h2>GOTEO<span class="red">FAQ</span></h2>
                <?php echo new View('view/header/share.html.php') ?>
            </div>
        </div>
<?php endif; ?>
        <div id="main">
			<div id="faq-content">
				<div class="faq-page-title"><?php echo Text::get('regular-faq') ?>
                    <span class="line"></span>
                </div>
                
                <div class="goask"><?php echo Text::get('faq-ask-question'); ?></div>
                <div class="goask-button"><a class="button green" href="/contact"><?php echo Text::get('regular-ask'); ?></a></div>

                <br clear="both" />

                <ul id="faq-sections">
                <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
                    <li><a href="/faq/<?php echo ($sectionId == 'node') ? '' : $sectionId; ?>"<?php if ($sectionId == $current) echo ' class="current"'; ?> style="color: <?php echo $this['colors'][$sectionId] ?>;"><?php echo preg_replace('/\s/', '<br />', $sectionName, 1); ?></a></li>
                <?php endforeach; ?>
                </ul>

                <br clear="both" />

                <h3 style="color: <?php echo $this['colors'][$current] ?>;" ><?php echo $this['sections'][$current]; ?></h3>
                <ol>
                    <?php foreach ($this['faqs'][$current] as $question)  : ?>
                        <li>
                            <h4><a href="#q<?php echo $question->id; ?>" class="faq-question" style="color:<?php echo $this['colors'][$current] ?>;"><?php echo $question->title; ?></a></h4>
                            <div id="q<?php echo $question->id; ?>" style="<?php echo ($this['show'] == $question->id) ? 'display:block;' : 'display:none;' ?>"><?php echo $question->description; ?></div>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <a class="up" href="#"><?php echo $go_up; ?></a>

			</div>
        </div>
	<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>