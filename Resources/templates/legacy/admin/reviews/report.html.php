<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Criteria;

$review     = $vars['review'];
$evaluation = $vars['evaluation'];

$sections = Criteria::sections();
$criteria = array();
foreach ($sections as $sectionId=>$sectionName) {
    $criteria[$sectionId] = Criteria::getAll($sectionId);
}
?>
<div>
    <p>Informe de revisión del proyecto '<?php echo $review->name; ?>' de <?php echo $review->owner_name; ?></p>
    <p>La edición del proyecto alcanzó el <strong><?php echo $review->progress; ?>%</strong> y la puntuación promedio de la revisión: <span id="total-score"><?php echo $review->score . '/' . $review->max; ?></span></p>
</div>

<?php foreach ($sections as $sectionId=>$sectionName) : ?>
<div class="widget">
    <h2 class="title"><?php echo $sectionName; ?></h2>
    <?php foreach ($review->checkers as $user=>$user_data) : ?>
    <p>
        <strong><?php echo $user_data->name ?></strong> otorga puntos porque:<br />
        <blockquote>
        <?php foreach ($criteria[$sectionId] as $crit) :
            if ($evaluation[$user]['criteria'][$crit->id] > 0) echo '· ' . $crit->title . '<br />';
        endforeach; ?>
        </blockquote>
    </p>
    <?php endforeach; ?>
    <?php foreach ($review->checkers as $user=>$user_data) : ?>
    <p>
        <strong><?php echo $user_data->name ?></strong> evalua <?php echo strtolower($sectionName); ?>:<br />
        <blockquote><?php echo $evaluation[$user][$sectionId]['evaluation']; ?></blockquote>
    </p>
    <p>
        <strong><?php echo $user_data->name ?></strong> propone <?php echo strtolower($sectionName); ?>:<br />
        <blockquote><?php echo $evaluation[$user][$sectionId]['recommendation']; ?></blockquote>
    </p>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>
