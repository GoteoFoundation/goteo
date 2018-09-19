<?php
/**
 * Migration Task class.
 */
class GoteoOds
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
    // add the post-migration code here
    $es = [
        [1, 'Fin de la pobreza', 'Poner fin a la pobreza en todas sus formas en todo el mundo'],
        
    ];
    $en = [
    [1, 'No poverty', 'Economic growth must be inclusive to provide sustainable jobs and promote equality.'],
    [2, 'Zero Hunger', 'The food and agriculture sector offers key solutions for development, and is central for hunger and poverty eradication.'],
    [3, 'Good Health and Well-Being', 'Ensuring healthy lives and promoting the well-being for all at all ages is essential to sustainable development.'],
    [4, 'Quality Education', 'Obtaining a quality education is the foundation to improving people’s lives and sustainable development.'],
    [5, 'Gender Equality', 'Gender equality is not only a fundamental human right, but a necessary foundation for a peaceful, prosperous and sustainable world.'],
    [6, 'Clean Water and Sanitation', 'Clean, accessible water for all is an essential part of the world we want to live in.'],
    [7, 'Affordable and Clean Energy', 'Energy is central to nearly every major challenge and opportunity.'],
    [8, 'Decent Work and Economic Growth', 'Sustainable economic growth will require societies to create the conditions that allow people to have quality jobs.'],
    [9, 'Industry, Innovation and Infrastructure', 'Investments in infrastructure are crucial to achieving sustainable development.'],
    [10, 'Reduced Inequalities', 'To reduce inequalities, policies should be universal in principle, paying attention to the needs of disadvantaged and marginalized populations.'],
    [11, 'Sustainable Cities and Communities', 'There needs to be a future in which cities provide opportunities for all, with access to basic services, energy, housing, transportation and more.'],
    [12, 'Responsible Production and Consumption', 'Responsible Production and Consumption'],
    [13, 'Climate Action', 'Climate change is a global challenge that affects everyone, everywhere.'],
    [14, 'Life Below Water', 'Careful management of this essential global resource is a key feature of a sustainable future.']
    [15, 'Life On Land', 'Sustainably manage forests, combat desertification, halt and reverse land degradation, halt biodiversity loss'],
    [16, 'Peace, Justice and Strong Institutions', 'Access to justice for all, and building effective, accountable institutions at all levels.'],
    [17, 'Partnerships for the Goals', 'Revitalize the global partnership for sustainable development']
    ];
  }

  public function preDown()
  {
      // add the pre-migration code here
  }

  public function postDown()
  {
      // add the post-migration code here
  }

  /**
   * Return the SQL statements for the Up migration
   *
   * @return string The SQL string to execute for the Up migration.
   */
  public function getUpSQL()
  {
     return "
     ALTER TABLE `category` CHANGE `social_commitment` `social_commitment` INT(10) UNSIGNED NULL COMMENT 'Social commitment', 
        DROP INDEX `id`, 
        ADD FOREIGN KEY (`social_commitment`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE SET NULL; 

    CREATE TABLE `sdg` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `icon` varchar(255) NOT NULL DEFAULT '',
        `description` text NOT NULL,
        `link` varchar(255) NOT NULL DEFAULT '',
        `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    );

    CREATE TABLE `sdg_lang` (
        `id` int(10) unsigned NOT NULL,
        `lang` varchar(2) NOT NULL,
        `name` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `link` varchar(255) NOT NULL,
        `pending` tinyint(1) DEFAULT 0,
        PRIMARY KEY (`id`,`lang`),
        CONSTRAINT `sdg_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `sdg` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      );

     "
     ;
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
     ALTER TABLE `category` DROP INDEX `social_commitment`, ADD UNIQUE INDEX `id` (`id`), DROP FOREIGN KEY `category_ibfk_1`;
     ";
  }

}