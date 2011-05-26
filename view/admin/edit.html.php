<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2><?php echo $this['title']; ?></h2>

            <div class="menu">
                <ul>
                <?php foreach ($this['menu'] as $menu) : ?>
                    <li><a href="<?php echo $menu['url']; ?>"><?php echo $menu['label']; ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>

            <?php
            if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;
            ?>

            <!-- form -->
            <form action="<?php echo $this['form']['action']; ?>" method="post">
                <dl>
                    <?php foreach ($this['form']['fields'] as $Id=>$field) : ?>
                        <dt><label for="<?php echo $Id; ?>"><?php echo $field['label']; ?></label></dt>
                        <dd><?php switch ($field['type']) {
                            case 'text': ?>
                                <input type="text" id="<?php echo $Id; ?>" name="<?php echo $field['name']; ?>" <?php echo $field['properties']; ?> value="<?php $name = $field['name']; echo $this['data']->$name; ?>" />
                            <?php break;
                            case 'hidden': ?>
                                <input type="hidden" id="<?php echo $Id; ?>" name="<?php echo $field['name']; ?>" <?php echo $field['properties']; ?> value="<?php $name = $field['name']; echo $this['data']->$name; ?>" />
                            <?php break;
                            case 'textarea': ?>
                                <textarea id="<?php echo $Id; ?>" name="<?php echo $field['name']; ?>" <?php echo $field['properties']; ?>><?php $name = $field['name']; echo $this['data']->$name; ?></textarea>
                            <?php break;
                        } ?></dd>

                    <?php endforeach; ?>
                </dl>
                <input type="submit" name="<?php echo $this['form']['submit']['name']; ?>" value="<?php echo $this['form']['submit']['label']; ?>" />
            </form>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';