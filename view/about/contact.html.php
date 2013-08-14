<?php

use Goteo\Library\Page,
    Goteo\Library\Text;

$bodyClass = 'about';

$page = Page::get('contact');
$tags = $this['tags'];

include 'view/prologue.html.php';
include 'view/header.html.php';
?>
<?php if (\NODE_ID == \GOTEO_NODE) : ?>
    <div id="sub-header">
        <div>
            <h2><?php echo $page->description; ?></h2>
        </div>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

    <div id="main">

        <div class="widget contact-message">
            <h3 class="title"><?php echo $page->name; ?></h3>

            <?php if (!empty($this['errors'])) : ?>
                <p style="color:red;">
                    <?php echo implode(', ', $this['errors']); ?>
                </p>
            <?php endif; ?>

            <div style="float:left;width: 450px;">
                <form method="post" action="/contact">
                    <table>
                        <tr>
                            <td>
                                <div class="field">
                                    <label for="name"><?php echo Text::get('contact-name-field'); ?></label><br />
                                    <input class="short" type="text" id="name" name="name" value="<?php echo $this['data']['name'] ?>"/>
                                </div>
                            </td>
                            <td>
                                <div class="field">
                                    <label for="email"><?php echo Text::get('contact-email-field'); ?></label><br />
                                    <input class="short" type="text" id="email" name="email" value="<?php echo $this['data']['email'] ?>"/>
                                </div>
                            </td>
                        </tr>
                        <?php if (!empty($tags)) : ?>
                        <tr>
                            <td colspan="2">
                                <div class="field">
                                    <label for="tag"><?php echo Text::get('contact-tag-field'); ?></label><br />
                                    <select name="tag" id="tag">
                                        <?php foreach ($tags as $key => $val) { 
                                            $sel = ($key == $this['data']['tag']) ? ' selected="selected"' : '';
                                            echo '<option value="'.$key.'"'.$sel.'>'.$val.'</option>'; 
                                        } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="2">
                                <div class="field">
                                    <label for="subject"><?php echo Text::get('contact-subject-field'); ?></label><br />
                                    <input type="text" id="subject" name="subject" value="<?php echo $this['data']['subject'] ?>"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="field">
                                    <label for="message"><?php echo Text::get('contact-message-field'); ?></label><br />
                                    <textarea id="message" name="message" cols="50" rows="5"><?php echo $this['data']['message'] ?></textarea>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <button class="aqua" type="submit" name="send"><?php echo Text::get('contact-send_message-button'); ?></button>
                </form>
            </div>

            <div style="float:left;width: 450px;">
                <?php echo $page->content; ?>
            </div>

        </div>

    </div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>