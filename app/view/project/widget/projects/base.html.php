<?php
if (!empty($vars['projects'])):
    foreach ($vars['projects'] as $project): ?>
        <div class="project">

            <!--
            <div class="balloon">
                <h4><?php echo htmlspecialchars($promo->title) ?></h4>
                <blockquote><?php echo $promo->description ?></blockquote>
            </div>
                -->
            <?php echo View::get('project/widget/project.html.php', array(
                'project' => $promo->projectData,
                'balloon' => '<h4>' . htmlspecialchars($promo->title) . '</h4>' .
                             '<blockquote>' . $promo->description . '</blockquote>'
            )) ?>

        </div>

<?php
     endforeach;
?>

</div>
<?php
endif;
?>
