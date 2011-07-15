<?php

use Goteo\Library\Page,
    Goteo\Library\Text;

$bodyClass = 'about';

$page = Page::get('contact');

include 'view/prologue.html.php';
include 'view/header.html.php';
?>
    <div id="sub-header">
        <div>
            <h2><?php echo $page->name; ?></h2>
        </div>
    </div>

    <div id="main">

        <div class="widget">
            <?php echo $page->content; ?>

            <?php if (!empty($this['errors']) || !empty($this['message'])) : ?>
                <p>
                    <?php echo implode(', ', $this['errors']); ?>
                    <?php echo $this['message']; ?>
                </p>
            <?php endif; ?>

        </div>

        <div class="widget contact-message">

            <h3 class="title"><?php echo Text::get('contact-send_message-header'); ?></h3>

            <form method="post" action="/about/contact">
                <div class="field">
                    <label for="email"><?php echo Text::get('contact-email-field'); ?></label><br />
                    <input type="text" id="email" name="email" value="<?php echo $this['data']['email'] ?>"/>
                </div>

                <div class="field">
                    <label for="subject"><?php echo Text::get('contact-subject-field'); ?></label><br />
                    <input type="text" id="subject" name="subject" value="<?php echo $this['data']['subject'] ?>"/>
                </div>

                <div class="field">
                    <label for="message"><?php echo Text::get('contact-message-field'); ?></label><br />
                    <textarea id="message" name="message" cols="50" rows="5"><?php echo $this['data']['message'] ?></textarea>
                </div>

                <button class="green" type="submit" name="send"><?php echo Text::get('contact-send_message-button'); ?></button>
            </form>

        </div>

    </div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>