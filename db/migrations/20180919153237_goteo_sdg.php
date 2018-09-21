<?php

use Goteo\Model\Sdg;
use Goteo\Application\Config;

/**
 * Migration Task class.
 */
class GoteoSdg
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
    $seed = [];
    // add the post-migration code here
    $seed['es'] = [
      '1' => ['Fin de la pobreza', 'Poner fin a la pobreza en todas sus formas en todo el mundo', 'https://www.un.org/sustainabledevelopment/es/poverty/'],
      '2' => ['Hambre cero', 'Poner fin al hambre, lograr la seguridad alimentaria y la mejora de la nutrición y promover la agricultura sostenible', 'https://www.un.org/sustainabledevelopment/es/hunger/'],
      '3' => ['Salud y bienestar', 'Garantizar una vida sana y promover el bienestar para todos en todas las edades', 'https://www.un.org/sustainabledevelopment/es/health/'],
      '4' => ['Educación de calidad', 'Garantizar una educación inclusiva, equitativa y de calidad y promover oportunidades de aprendizaje durante toda la vida para todos', 'https://www.un.org/sustainabledevelopment/es/education/'],
      '5' => ['Igualdad de género', 'Lograr la igualdad entre los géneros y empoderar a todas las mujeres y las niñas', 'https://www.un.org/sustainabledevelopment/es/gender-equality/'],
      '6' => ['Agua limpia y saneamiento', 'Garantizar la disponibilidad de agua y su gestión sostenible y el saneamiento para todos', 'https://www.un.org/sustainabledevelopment/es/water-and-sanitation/'],
      '7' => ['Energía asequible y no contaminante', 'Garantizar el acceso a una energía asequible, segura, sostenible y moderna para todos', 'https://www.un.org/sustainabledevelopment/es/energy/'],
      '8' => ['Trabajo decente y crecimiento económico', 'Promover el crecimiento económico sostenido, inclusivo y sostenible, el empleo pleno y productivo y el trabajo decente para todos', 'https://www.un.org/sustainabledevelopment/es/economic-growth/'],
      '9' => ['Industria, innovación e infraestructuras', 'Las inversiones en infraestructura son cruciales para lograr un desarrollo sostenible.', 'https://www.un.org/sustainabledevelopment/es/infrastructure/'],
      '10' => ['Reducción de la desigualdad', 'Reducir la desigualdad en y entre los países', 'https://www.un.org/sustainabledevelopment/es/inequality/'],
      '11' => ['Ciudades y comunidades sostenibles', 'Lograr que las ciudades y los asentamientos humanos sean inclusivos, seguros, resilientes y sostenibles', 'https://www.un.org/sustainabledevelopment/es/cities/'],
      '12' => ['Producción y consumo responsables', 'Garantizar modalidades de consumo y producción sostenibles', 'https://www.un.org/sustainabledevelopment/es/sustainable-consumption-production/'],
      '13' => ['Acción por el clima', 'Adoptar medidas urgentes para combatir el cambio climático y sus efectos', 'https://www.un.org/sustainabledevelopment/es/climate-change-2/'],
      '14' => ['Vida submarina', 'Conservar y utilizar en forma sostenible los océanos, los mares y los recursos marinos para el desarrollo sostenible', 'https://www.un.org/sustainabledevelopment/es/oceans/'],
      '15' => ['Vida de ecosistemas terrestres', 'Gestionar sosteniblemente los bosques, luchar contra la desertificación, detener e invertir la degradación de las tierras y detener la pérdida de biodiversidad', 'https://www.un.org/sustainabledevelopment/es/biodiversity/'],
      '16' => ['Paz, justicia e instituciones sólidas', 'Promover sociedades, justas, pacíficas e inclusivas', 'https://www.un.org/sustainabledevelopment/es/peace-justice/'],
      '17' => ['Alianzas para lograr los objetivos', 'Revitalizar la Alianza Mundial para el Desarrollo Sostenible', 'https://www.un.org/sustainabledevelopment/es/globalpartnerships/']
    ];
    $seed['ca'] = [
      '1' => ['Fi de la pobresa', 'Posar fi a la pobresa en totes les seves formes a tot el món'],
      '2' => ['Fam zero', 'Posar fi a la fam, aconseguir seguretat alimentària, la millora de la nutrició i promoure l\'agricultura sostenible'],
      '3' => ['Salut i benestar', 'Garantir una vida sana i promoure el benestar per a tothom en totes les edats'],
      '4' => ['Educació de qualitat', 'Garantir una educació inclusiva, equitativa i de qualitat i promoure oportunitats d\'aprenentatge durant tota la vida per a tots'],
      '5' => ['Igualtat de gènere', 'Aconseguir la igualtat entre els gèneres i donar poder a totes les dones i nenes'],
      '6' => ['Aigua neta i sanejament', 'Garantir la disponibilitat d\'aigua i la seva gestió sostenible i el sanejament per a tots'],
      '7' => ['Energia assequible i no contaminant', 'Garantir l\'accés a una energia assequible, segura, sostenible i moderna per a tots'],
      '8' => ['Treball decent i creixement econòmic', 'Promoure el creixement econòmic sostingut, inclusiu i sostenible, l\'ocupació plena i productiva i el treball decent per a tots'],
      '9' => ['Indústria, innovació i infraestructures', 'Les inversions en infraestructura són crucials per aconseguir un desenvolupament sostenible.'],
      '10' => ['Reducció de la desigualtat', 'Reduir la desigualtat en i entre els països'],
      '11' => ['Ciutats i comunitats sostenibles', 'Aconseguir que les ciutats i els assentaments humans siguin inclusius, segurs, resilients i sostenibles'],
      '12' => ['Producció i consum responsables', 'Garantir modalitats de consum i producció sostenibles'],
      '13' => ['Acció pel clima', 'Adoptar mesures urgents per combatre el canvi climàtic i els seus efectes'],
      '14' => ['Vida submarina', 'Conservar i utilitzar en forma sostenible els oceans, els mars i els recursos marins per al desenvolupament sostenible'],
      '15' => ['Vida d\'ecosistemes terrestres', 'Gestionar sosteniblement els boscos, lluitar contra la desertificació, aturar i invertir la degradació de les terres i aturar la pèrdua de biodiversitat'],
      '16' => ['Pau, justícia i institucions sòlides', 'Promoure societats, justes, pacífiques i inclusives'],
      '17' => ['Aliances per assolir els objectius', 'Revitalitzar l\'Aliança Mundial per al Desenvolupament Sostenible']
    ];
    $seed['en'] = [
      '1' => ['No poverty', 'Economic growth must be inclusive to provide sustainable jobs and promote equality.', 'https://www.un.org/sustainabledevelopment/poverty/'],
      '2' => ['Zero Hunger', 'The food and agriculture sector offers key solutions for development, and is central for hunger and poverty eradication.', 'https://www.un.org/sustainabledevelopment/hunger/'],
      '3' => ['Good Health and Well-Being', 'Ensuring healthy lives and promoting the well-being for all at all ages is essential to sustainable development.', 'https://www.un.org/sustainabledevelopment/health/'],
      '4' => ['Quality Education', 'Obtaining a quality education is the foundation to improving people’s lives and sustainable development.', 'https://www.un.org/sustainabledevelopment/education/'],
      '5' => ['Gender Equality', 'Gender equality is not only a fundamental human right, but a necessary foundation for a peaceful, prosperous and sustainable world.', 'https://www.un.org/sustainabledevelopment/gender-equality/'],
      '6' => ['Clean Water and Sanitation', 'Clean, accessible water for all is an essential part of the world we want to live in.', 'https://www.un.org/sustainabledevelopment/water-and-sanitation/'],
      '7' => ['Affordable and Clean Energy', 'Energy is central to nearly every major challenge and opportunity.', 'https://www.un.org/sustainabledevelopment/energy/'],
      '8' => ['Decent Work and Economic Growth', 'Sustainable economic growth will require societies to create the conditions that allow people to have quality jobs.', 'https://www.un.org/sustainabledevelopment/economic-growth/'],
      '9' => ['Industry, Innovation and Infrastructure', 'Investments in infrastructure are crucial to achieving sustainable development.', 'https://www.un.org/sustainabledevelopment/infrastructure-industrialization/'],
      '10' => ['Reduced Inequalities', 'To reduce inequalities, policies should be universal in principle, paying attention to the needs of disadvantaged and marginalized populations.', 'https://www.un.org/sustainabledevelopment/inequality/'],
      '11' => ['Sustainable Cities and Communities', 'There needs to be a future in which cities provide opportunities for all, with access to basic services, energy, housing, transportation and more.', 'https://www.un.org/sustainabledevelopment/cities/'],
      '12' => ['Responsible Production and Consumption', 'Responsible Production and Consumption', 'https://www.un.org/sustainabledevelopment/sustainable-consumption-production/'],
      '13' => ['Climate Action', 'Climate change is a global challenge that affects everyone, everywhere.', 'https://www.un.org/sustainabledevelopment/climate-change-2/'],
      '14' => ['Life Below Water', 'Careful management of this essential global resource is a key feature of a sustainable future.', 'https://www.un.org/sustainabledevelopment/oceans/'],
      '15' => ['Life On Land', 'Sustainably manage forests, combat desertification, halt and reverse land degradation, halt biodiversity loss', 'https://www.un.org/sustainabledevelopment/biodiversity/'],
      '16' => ['Peace, Justice and Strong Institutions', 'Access to justice for all, and building effective, accountable institutions at all levels.', 'https://www.un.org/sustainabledevelopment/peace-justice/'],
      '17' => ['Partnerships for the Goals', 'Revitalize the global partnership for sustainable development', 'https://www.un.org/sustainabledevelopment/globalpartnerships/']
    ];

    $sql_lang = Config::get('sql_lang');
    if(!$seed[$sql_lang]) throw new \RunException("[$sql_lang] not in the seed data!");

    foreach($seed[$sql_lang] as $id => $line) {
      $sdg = new Sdg(['id' => (int)$id, 'name' => $line[0], 'description' => $line[1], 'link' => $line[2] ? $line[2] : '']);
      $errors = [];
      if(!$sdg->save($errors)) {
        throw new \RuntimeException("Error saving main object [$id] " . implode("\n", $errors));
      }
      foreach($seed as $lang => $trans) {
        if($lang == $sql_lang) continue;
        $errors = [];
        $line = $trans[$id];
        if(!$sdg->setLang($lang, ['name' => $line[0], 'description' => $line[1], 'link' => $line[2] ? $line[2] : ''], $errors)) {
          throw new \RuntimeException("Error saving translation [$lang] for id [{$sdg->id}] ({$line[0]})" . implode("\n", $errors));
        }
      }
    }

    // Built sdg relationships
    $relations = [

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
    UPDATE category SET social_commitment=NULL WHERE social_commitment='';

    ALTER TABLE `category` CHANGE `social_commitment` `social_commitment` INT(10) UNSIGNED NULL COMMENT 'Social commitment',
        DROP INDEX `id`,
        ADD FOREIGN KEY (`social_commitment`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE SET NULL;

    CREATE TABLE `sdg` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `icon` varchar(255) NULL,
      `description` text NOT NULL,
      `link` varchar(255) NOT NULL DEFAULT '',
      `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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

    CREATE TABLE `footprint` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `icon` varchar(255) NULL,
      `description` text NOT NULL,
      `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    );

    CREATE TABLE `footprint_lang` (
      `id` int(10) unsigned NOT NULL,
      `lang` varchar(2) NOT NULL,
      `name` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `pending` tinyint(1) DEFAULT 0,
      PRIMARY KEY (`id`,`lang`),
      CONSTRAINT `footprint_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `footprint` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    );

    CREATE TABLE `sdg_category`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `category_id` INT(10) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );

    CREATE TABLE `sdg_social_commitment`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `social_commitment_id` INT(10) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`social_commitment_id`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );

    CREATE TABLE `sdg_sphere`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `sphere_id` BIGINT(20) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`sphere_id`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );

    CREATE TABLE `sdg_footprint`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `footprint_id` INT(10) UNSIGNED NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`footprint_id`) REFERENCES `footprint`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
       ALTER TABLE `category`
          CHANGE `social_commitment` `social_commitment` CHAR(50) NULL COMMENT 'Social commitment',
          DROP INDEX `social_commitment`, ADD UNIQUE INDEX `id` (`id`), DROP FOREIGN KEY `category_ibfk_1`;

     DROP TABLE sdg_category;
     DROP TABLE sdg_social_commitment;
     DROP TABLE sdg_sphere;
     DROP TABLE sdg_footprint;
     DROP TABLE footprint_lang;
     DROP TABLE footprint;
     DROP TABLE sdg_lang;
     DROP TABLE sdg;
     ";
  }

}
