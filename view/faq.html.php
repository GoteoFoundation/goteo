<?php

use Goteo\Library\Text;

$bodyClass = 'faq';

include 'view/prologue.html.php';

include 'view/header.html.php';

$go_up = Text::get('regular-go_up');

?>

        <div id="main">

            <h2><?php echo $this['name']; ?></h2>
            <p><?php echo $this['title']; ?></p>

            <div id="content"><?php echo $this['content']; ?></div>

            <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
                <div>
                    <h3><?php echo $sectionName; ?></h3>
                    <ol>
                        <?php foreach ($this['faqs'][$sectionId] as $question)  : ?>
                            <li><a href="#q<?php echo $question->id; ?>"><?php echo $question->title; ?></a></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endforeach; ?>

            <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
                <div>
                    <h3><?php echo $sectionName; ?></h3>
                    <?php foreach ($this['faqs'][$sectionId] as $question)  : ?>
                        <div>
                            <a name="q<?php echo $question->id; ?>" />
                            <h4><?php echo $question->title; ?></h4>
                            <blockquote><?php echo $question->description; ?></blockquote>
                            <a href="#"><?php echo $go_up; ?></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>