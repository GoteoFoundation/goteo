<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2><?php echo $this['title']; ?></h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
                    <?php foreach ($this['menu'] as $menu) : ?>
                        <li><a href="<?php echo $menu['url']; ?>"><?php echo $menu['label']; ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget">
                <!-- superform -->
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

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';