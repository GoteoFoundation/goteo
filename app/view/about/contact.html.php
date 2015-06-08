<?php

use Goteo\Library\Page,
    Goteo\Library\Text,
    Goteo\Application\Config;

$bodyClass = 'about';

$page = Page::get('contact');
$tags = $vars['tags'];
$showCaptcha = $vars['showCaptcha'];

$_SESSION['msg_token'] = uniqid(rand(), true);

if ($showCaptcha) {
    // recaptcha
    require_once __DIR__ . '/../../../src/Goteo/Library/recaptchalib/recaptchalib.php';

    $RECAPTCHA = (\HTTPS_ON) ? RECAPTCHA_API_SECURE_SERVER : RECAPTCHA_API_SERVER;
    $RECAPTCHA .= '/challenge?k='. RECAPTCHA_PUBLIC_KEY;
}

include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';
?>
<style>#recaptcha_widget_div{display:none;}</style>
<?php if (!Config::isNode()) : ?>
    <div id="sub-header">
        <div>
            <h2><?php echo $page->description; ?></h2>
        </div>
    </div>
<?php endif; ?>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>
    <div id="main">

        <div class="widget contact-message">
            <h3 class="title"><?php echo $page->name; ?></h3>

            <?php if (!empty($vars['errors'])) : ?>
                <p style="color:red;">
                    <?php echo implode('<br />', $vars['errors']); ?>
                </p>
            <?php endif; ?>

            <div style="float:left;width: 450px;">
                <form method="post" action="/contact">
                    <input type="hidden" id="msg_token" name="msg_token" value="<?php echo $_SESSION['msg_token'] ; ?>" />
                    <table>
                        <tr>
                            <td>
                                <div class="field">
                                    <label for="name"><?php echo Text::get('contact-name-field'); ?></label><br />
                                    <input class="short" type="text" id="name" name="name" value="<?php echo $vars['data']['name'] ?>"/>
                                </div>
                            </td>
                            <td>
                                <div class="field">
                                    <label for="email"><?php echo Text::get('contact-email-field'); ?></label><br />
                                    <input class="short" type="text" id="email" name="email" value="<?php echo $vars['data']['email'] ?>"/>
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
                                            $sel = ($key == $vars['data']['tag']) ? ' selected="selected"' : '';
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
                                    <input type="text" id="subject" name="subject" value="<?php echo $vars['data']['subject'] ?>"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="field">
                                    <label for="message"><?php echo Text::get('contact-message-field'); ?></label><br />
                                    <textarea id="message" name="message" cols="50" rows="5"><?php echo $vars['data']['message'] ?></textarea>
                                </div>
                            </td>
                        </tr>

                        <?php if ($showCaptcha) { ?>
                        <tr>
                            <td colspan="2">
                                <div class="field">
                                    <label for="recaptcha_response"><?php echo Text::get('contact-captcha-field'); ?></label><br />
                                    <input type="text" id="recaptcha_response" name="recaptcha_response" value=""/>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>

                    <?php if ($showCaptcha) { ?>
                    <!--reCAPTCHA -->
                    <div id="recaptcha_image"></div><a href="javascript:Recaptcha.reload()"><?php echo Text::get('contact-captcha-refresh'); ?></a>
                    <script type="text/javascript" src="<?php echo $RECAPTCHA; ?>"></script>
                    <br />
                    <!-- fin reCAPTCHA -->
                    <?php } ?>

                    <button class="aqua" name="send" type="submit"><?php echo Text::get('contact-send_message-button'); ?></button>
                </form>
            </div>

            <div style="float:left;width: 450px;">
                <?php echo $page->content; ?>
            </div>

        </div>

    </div>

<?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
