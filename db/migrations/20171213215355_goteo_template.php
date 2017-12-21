<?php
/**
 * Migration Task class.
 */
class GoteoTemplate
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here
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
     UPDATE `template` SET `text` = 'El impulsor **%USERNAME%** ha enviado a valoración por primera vez un proyecto:\r\n\r\nIr al proyecto **[%PROJECTNAME%](%PROJECTURL%)**.\r\n\r\n%HELP%\r\n\r\n**Comentario del Impulsor:**\r\n%COMMENT%\r\n\r\n**Descripción del proyecto:**\r\n%PROJECTDESCRIPTION%\r\n\r\n**Mínimo solicitado:**\r\n%PROJECTMIN% €\r\n\r\n**Difusión:**\r\n%SPREAD%\r\n\r\n**Fecha de publicación prevista:**\r\n%PUBLISHINGESTIMATION%' , `type` = 'md' WHERE `id` = '63';

    UPDATE `template_lang` SET `text` = 'L\'impulsor **%USERNAME%** ha enviat a valoració per primera vegada un projecte:\r\n\r\nVés al projecte **[%PROJECTNAME%](%PROJECTURL%)**.\r\n\r\n%HELP%\r\n\r\n**Comentari de l\'impulsor:**\r\n%COMMENT%\r\n\r\n**Descripció del projecte:**\r\n%PROJECTDESCRIPTION%\r\n\r\n**Mínim sol·licitat:**\r\n%PROJECTMIN% €\r\n\r\n**Difusió:**\r\n%SPREAD%\r\n\r\n**Data de publicació prevista:**\r\n%PUBLISHINGESTIMATION%' WHERE `id` = '63' AND `lang` = 'ca';

    UPDATE `template_lang` SET `text` = 'The promoter **%USERNAME%** has sent to review a new project for the first time:\r\n\r\nGo to the project **[%PROJECTNAME%](%PROJECTURL%)**.\r\n\r\n%HELP%\r\n\r\n**Promoter\'s comment: **\r\n%COMMENT%\r\n\r\n**Project description:**\r\n%PROJECTDESCRIPTION%\r\n\r\n**Minimum requested:**\r\n%PROJECTMIN% €\r\n\r\n**Diffusion:**\r\n%SPREAD%\r\n\r\n**Expected publication date:**\r\n%PUBLISHINGESTIMATION%' WHERE `id` = '63' AND `lang` = 'en';

    UPDATE `template` SET `text` = 'El impulsor **%USERNAME%** ha vuelto a enviar a valoración un proyecto en el que constas como asesor/a:\r\n\r\nIr al proyecto **[%PROJECTNAME%](%PROJECTURL%)**.\r\n\r\n**Comentario del Impulsor:**\r\n%COMMENT%' , `type` = 'md' WHERE `id` = '59';

    UPDATE `template_lang` SET `text` = 'L\'impulsor **%USERNAME%** ha tornat a enviar a valoració un projecte en el que hi constes com a assessor/a:\r\n\r\nAnar al projecte **[%PROJECTNAME%](%PROJECTURL%)**.\r\n\r\n**Comentari de l\'Impulsor:**\r\n%COMMENT%' WHERE `id` = '59' AND `lang` = 'ca';

     UPDATE `template_lang` SET `text` = 'The promoter **%USERNAME%** has sent its project for evaluation. You as consultant should review it:\r\n\r\nOpen the project **[%PROJECTNAME%](%PROJECTURL%)**.\r\n\r\n**Promoter\'s comment:**\r\n%COMMENT%' WHERE `id` = '59' AND `lang` = 'en';
";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
     UPDATE `template` SET `text` = '<p>El impulsor <strong>%USERNAME%</strong> ha enviado a valoración por primera vez un proyecto:</p> <p>Ir al proyecto <strong><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></strong>.</p> <p>%HELP%</p>\r\n<p><strong>Comentario del Impulsor:</strong><br />%COMMENT%</p>\r\n<p><strong>Descripción del proyecto:</strong><br />%PROJECTDESCRIPTION%</p>\r\n<p><strong>Mínimo solicitado:</strong><br />%PROJECTMIN% €</p>\r\n<p><strong>Difusión:</strong><br />%SPREAD%</p>\r\n<p><strong>Fecha de publicación prevista:</strong><br />%PUBLISHINGESTIMATION%</p>' , `type` = 'html' WHERE `id` = '63';

     UPDATE `template_lang` SET `text` = '<p>L\'impulsor <strong>%USERNAME%</strong> ha enviat a valoració per primer cop un nou projecte:</p> <p>Anar al projecte <strong><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></strong>.</p> %HELP%\r\n' WHERE `id` = '63' AND `lang` = 'ca';

     UPDATE `template_lang` SET `text` = '<p>The promoter <strong>%USERNAME%</strong> has sent for review a new project:</p> <p>Go to the project <strong><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></strong>.</p> %HELP%\r\n' WHERE `id` = '63' AND `lang` = 'en';

    UPDATE `template` SET `text` = '<p>El impulsor <strong>%USERNAME%</strong> ha vuelto a enviar a valoración un proyecto en el que constas como asesor/a:</p>\r\n\r\n<p>Ir al proyecto <strong><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></strong>.</p>\r\n\r\n<p><strong>Comentario del Impulsor:</strong><br />%COMMENT%</p>' WHERE `id` = '59';

    UPDATE `template_lang` SET `text` = '<p>L\'impulsor <strong>%USERNAME%</strong> ha tornat a enviar a valoració un projecte en el que hi constes com a assessor/a:</p>\r\n\r\n<p>Anar al projecte <strong><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></strong>.</p>\r\n\r\n<p><strong>Comentari de l\'Impulsor:</strong><br/>%COMMENT%</p>' WHERE `id` = '59' AND `lang` = 'ca';

    UPDATE `template_lang` SET `text` = '<p>The promoter <strong>%USERNAME%</strong> has sent its project for evaluation. You as consultant should review it:</p>\r\n\r\n<p>Open the project <strong><a href=\"%PROJECTURL%\">%PROJECTNAME%</a></strong>.</p>\r\n\r\n<p><strong>Promoter\'s comment:</strong><br/>%COMMENT%</p>' WHERE `id` = '59' AND `lang` = 'en';

";
  }

}
