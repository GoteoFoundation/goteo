<ul class="project-filters list-inline center-block text-center">
    <li data-status="promoted" class="active">
        <?= $this->text('home-projects-team-favourites') ?>
    </li>
    <li data-status="outdated">
        <?= $this->text('home-projects-outdate') ?>
    </li>
    <!--<li data-status="near"> Eliminamos del navegador el cerca de ti y añadimos el popular
    </li>
    -->
    <li data-status="popular">
                <?= $this->text('home-projects-popular') ?>
    </li>
    <li data-status="recent">
        <?= $this->text('discover-group-recent-header') ?>
    </li>
</ul>
