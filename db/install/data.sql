/*Data for the table `node` */
INSERT INTO `node` (`id`, `name`, `email`, `url`, `active`)VALUES('goteo', 'Goteo Central', '', '', 1);

/*Data for the table `blog` */
INSERT INTO `blog` VALUES(1, 'node', 'goteo', 1);

/*Data for the table `home` */
INSERT INTO `home` VALUES('promotes', 'main', 'goteo', 1);

/*Data for the table `category` */

INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (2,'Social','Proyectos que promueven el cambio social, la resolución de problemas en las relaciones humanas y/o su fortalecimiento para conseguir un mayor bienestar.',1);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (6,'Comunicativo','Proyectos con el objetivo de informar, denunciar, comunicar (por ejemplo periodismo ciudadano, documentales, blogs, programas de radio).',3);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (7,'Tecnológico','Desarrollos técnicos de software, hardware, herramientas etc. para solucionar problemas o necesidades concretas. ',1);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (9,'Emprendedor','Proyectos que aspiran a convertirse en una iniciativa empresarial o de emprendimiento social, generando beneficios económicos. ',1);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (10,'Educativo','Proyectos donde el objetivo primordial es la formación o el aprendizaje. ',5);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (11,'Cultural','Proyectos con objetivos artísticos y culturales en un sentido amplio.',6);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (13,'Ecológico','Proyectos relacionados con el cuidado del medio ambiente, la sostenibilidad y/o la diversidad biológica.',7);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (14,'Científico','Estudios o investigaciones de alguna materia, proyectos que buscan respuestas, soluciones, explicaciones nuevas.',8);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (15,'','Usuarios para pruebas en entorno real',1);
INSERT  INTO `category`(`id`,`name`,`description`,`order`) VALUES (16,'Diseño','',1);

/*Data for the table `category_lang` */

INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'ca','Social','Projectes que promouen el canvi social, la resolució de problemes en les relacions humanes i/o el seu enfortiment per aconseguir un major benestar.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'de','Gesellschaft','Projekte, die den sozialen Austausch sowie die Problemlösung in zwischenmenschlichen Beziehungen fördern und die eine Stärkung gesellschaftlicher Bindungen zur Förderung des Allgemeinwohls unterstützen.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'el','Social','Projects that promote social change, resolve problems with or strengthen human relationshiops in order to achieve better well-being.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'en','Social','Projects that promote social change, resolve problems with or strengthen human relationshiops in order to achieve better well-being.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'eu','Soziala','Gizarte eraldaketa bultzatzen dituzten proiektuak, ongizate handiagoa lortzeko, giza harremanetan ematen diren arazoak ebatzi edo/eta  giza harremanak indartuz.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'fr','Social','Des projets qui favorisent le changement social, la résolution de problèmes dans les relations humaines et/ou leur renforcement afin d\'atteindre un plus grand bien-être. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'gl','Social','Proxectos que promoven o cambio social, a resolución de problemas nas relacións humanas e/ou o seu fortalecemento para acadar un maior benestar.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'it','Sociale','Progetti che promuovono il cambiamento sociale, la soluzione di problemi nel campo delle relazioni umane e/o il loro rafforzamento per incrementare il benessere collettivo. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (2,'pl','Social','Projects that promote social change, resolve problems with or strengthen human relationshiops in order to achieve better well-being.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'ca','Comunicatiu','Projectes amb l\'objectiu d\'informar, denunciar, comunicar (per exemple periodisme ciutadà, documentals, blogs, programes de ràdio).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'de','Kommunikation','Projekte, deren Ziel es ist zu informieren, Misstände öffentlich zu machen oder die sich um Kommunikation im Allgemeinen drehen (z.B. Bürgerzeitungen, Dokumentarfilme, Blogs, Radioprogramme).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'el','Communications','Projects whose objective is to inform, denounce and/or communicate (for example, civic journalism, documentaries, blogs, radio programs).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'en','Communications','Projects whose objective is to inform, denounce and/or communicate (for example, civic journalism, documentaries, blogs, radio programs).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'eu','Komunikatiboa','Berri ematea, salaketa, komunikazio helburua duten proiektuak (adibidez herri kazetaritza, dokumentalak. blogak, irrati programak).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'fr','Communicatif','Des projets qui ont pour but d\'informer, de dénoncer, de communiquer (par exemple, le journalisme citoyen, des documentaires, des blogs, des programmes de radio).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'gl','Comunicativo','Proxectos cun obxectivo de informar, denunciar, comunicar (por exemplo periodismo cidadán, documentais, blogs, programas de radio).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'it','Comunicativo','Progetti che hanno l\'obiettivo di informare, denunciare, comunicare (giornalismo di cittadinanza, documentari, blog, programmi radio). ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (6,'pl','Communications','Projects whose objective is to inform, denounce and/or communicate (for example, civic journalism, documentaries, blogs, radio programs).',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'ca','Tecnològic','Desenvolupaments tècnics de programari, maquinari, eines etc. per solucionar problemes o necessitats concretes. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'de','Technologie','Technische Entwicklungen im Bereich Software, Hardware, Werkzeuge etc. die der Problemlösung dienen oder die auf konkrete Bedürfnisse eingehen.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'el','Technological','Technical development of software, hardware, tools, etc in order to solve concrete problems or needs.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'en','Technological','Technical development of software, hardware, tools, etc in order to solve concrete problems or needs.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'eu','Teknologikoa','Arazo edo behar konkretuak ebazteko garapen teknikoak, software, hardware, herramintak etar.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'fr','Technologique','Développement de logiciels, des outils, de hardware, etc. afin de résoudre des problèmes ou des besoins spécifiques.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'gl','Tecnolóxico','Desenrolos técnicos de software, hardware, ferramentas etc. para solucionar problemas ou necesidades concretas. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'it','Tecnologico','Sviluppo tecnico di software, hardware, strumenti, ecc. per la soluzione di problemi o necessità concrete. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (7,'pl','Technological','Technical development of software, hardware, tools, etc in order to solve concrete problems or needs.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'ca','Comercial','Projectes que aspiren a convertir-se en una iniciativa empresarial, generant beneficis econòmics. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'de','Kommerziell','Projekte, die eine unternehmerische Initiative darstellen und die die Absicht haben, ökonomischen Gewinn zu generieren.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'el','Commercial','Projects that are business initiatives, and that hope to generate profits.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'en','Commercial','Projects that are business initiatives, and that hope to generate profits.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'eu','Komertziala','Irabazi ekonomikoak sortuz, enpresa-ekimenen bat bihurtzeko asmoa duten propiektuak.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'fr','Commercial','Des projets visant à devenir des initiatives d\'affaires en génerant des bénéfices économiques.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'gl','Comercial','Proxectos que aspiran a converterse nunha iniciativa empresarial, xerando beneficios económicos. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'it','Imprenditoriale ','Progetti che aspirano a convertirsi in un\'iniziativa di impresa o di impresa sociale che generi benefici economici. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (9,'pl','Commercial','Projects that are business initiatives, and that hope to generate profits.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'ca','Educatiu','Projectes on l\'objectiu primordial és la formació o l\'aprenentatge. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'de','Bildung','Projekte, deren primäres Ziel im Bereich Bildung und Lernen liegt.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'el','Educational','Projects whose most important objective is formation or learning. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'en','Educational','Projects whose most important objective is formation or learning. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'eu','Hezigarria','Formakuntza edo ikaskuntza helburu nagusia duten proiektuak. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'fr','Éducatif','Des projets qui ont pour but principal la formation ou l\'apprentissage.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'gl','Educativo','Proxectos onde o obxectivo primordial é a formación ou a aprendizaxe.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'it','Educativo','Progetti che hanno come obiettivo principale la formazione o l\'apprendimento.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (10,'pl','Educational','Projects whose most important objective is formation or learning. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'ca','Cultural','Projectes amb objectius artístics i culturals en un sentit ampli.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'de','Kultur','Projekte mit künstlerischen und kulturellen Zielsetzungen im weiteren Sinne.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'el','Cultural','Projects with artistic or cultural objectives.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'en','Cultural','Projects with artistic or cultural objectives.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'eu','Kulturala','Zentzu zabalean helburu artistiko eta kulturalak dituzten proiektuak. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'fr','Culturel','Des projets ayant des objectifs artistiques et culturels au sens large.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'gl','Cultural','Proxectos con obxectivos artísticos e culturais nun sentido amplo.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'it','Culturale','Progetti con obiettivi artistici e culturale in un senso lato. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (11,'pl','Cultural','Projects with artistic or cultural objectives.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'ca','Ecològic','Projectes relacionats amb la cura del medi ambient, la sostenibilitat i/o la diversitat biològica.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'de','Ökologie','Projekte im Bereich Umweltschutz, Nachhaltigkeit und Biodiversität.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'el','Ecological','Projects that are related to the care of the environment, sustainability, and/or biological diversity.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'en','Ecological','Projects that are related to the care of the environment, sustainability, and/or biological diversity.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'eu','Ekologikoa','Ingurumenaren zainketa, jasangarritasun eta/edo aniztasun biologikoarekin harremanetan dauden proiektuak.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'fr','Écologique','Des projets liés au soin environnemental, à la durabilité et / ou à la diversité biologique.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'gl','Ecolóxico','Proxectos relacionados co coidado do medio ambiente, a sostenibilidade e/ou a diversidade biolóxica.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'it','Ecologico','Progetti relazionati alla tutela dell\'ambiente, la sostenibilità e/o la biodiversità. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (13,'pl','Ecological','Projects that are related to the care of the environment, sustainability, and/or biological diversity.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'ca','Científic','Estudis o investigacions d\'alguna matèria, projectes que busquen respostes, solucions, explicacions noves.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'de','Wissenschaft','Studien und Untersuchungen jeglicher Art, Projekte auf der Suche nach Antworten, Lösungen, und neuen Erklärungen.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'el','Scientific','Studies or research, projects that look for answers, solutions, new explanations.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'en','Scientific','Studies or research, projects that look for answers, solutions, new explanations.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'eu','Zientifikoa','Zenbait gaien ikasketak edo ikerketak, erantzun, azalpen, ebazpen berriak bilatzen dituzten proiektuak.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'fr','Scientifique','Des études ou des recherches dans n\'importe quel domaine, des projets qui cherchent des réponses, des solutions, des nouvelles explications.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'gl','Científico','Estudos ou investigacións dalgunha materia, proxectos que buscan respostas, solucións, explicacións novas.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'it','Scientifico ','Studi o ricerche di qualche disciplina, progetti che cercano risposte, soluzioni, nuove spiegazioni. ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (14,'pl','Scientific','Studies or research, projects that look for answers, solutions, new explanations.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (15,'ca','','Usuaris per a proves en entorn real',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (15,'eu','',' Benetazko ingurunean probak egiteko erabiltzaileak.',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (15,'fr','Nombre:','Des utilisateurs pour des tests en condition réelle',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (15,'gl','','Usuarios para probas en entorno real',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (15,'it','','Utenti per prove in situazioni reali ',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (16,'ca','Disseny','',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (16,'en','Design','',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (16,'gl','Deseño','',0);
INSERT  INTO `category_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES (16,'it','Design','Design ',0);


INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (5,'project','Es original','donde va esta descripción? donde esta el tool tip?Hola, este tooltip ira en el formulario de revision',1);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (6,'project','Es eficaz en su estrategia de comunicación','',2);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (7,'project','Aporta información suficiente del proyecto','',3);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (8,'project','Aporta productos, servicios o valores “deseables” para la comunidad','',4);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (9,'project','Es afín a la cultura abierta','',5);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (10,'project','Puede crecer, es escalable','',6);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (11,'project','Son coherentes los recursos solicitados con los objetivos y el tiempo de desarrollo','',7);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (12,'project','Riesgo proporcional al grado de beneficios (sociales, culturales y/o económicos)','Test descripción de un criterio...',8);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (13,'owner','Posee buena reputación en su sector','',1);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (14,'owner','Ha trabajado con organizaciones y colectivos con buena reputación','',2);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (15,'owner','Aporta información sobre experiencias anteriores (éxitos y fracasos)','',3);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (16,'owner','Tiene capacidades para llevar a cabo el proyecto','',4);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (17,'owner','Cuenta con un equipo formado','',5);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (18,'owner','Cuenta con una comunidad de seguidores','',6);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (19,'owner','Tiene visibilidad en la red','',7);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (20,'reward','Es viable (su coste está incluido en la producción del proyecto)','',1);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (21,'reward','Puede tener efectos positivos, transformadores (sociales, culturales, empresariales)','',2);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (22,'reward','Aporta conocimiento nuevo, de difícil acceso o en proceso de desaparecer','',3);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (23,'reward','Aporta oportunidades de generar economía alrededor','',4);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (24,'reward','Da libertad en el uso de sus resultados (es reproductible)','',5);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (25,'reward','Ofrece un retorno atractivo (por original, por útil, por inspirador... )','',6);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (26,'reward','Cuenta con actualizaciones','',7);
INSERT  INTO `criteria`(`id`,`section`,`title`,`description`,`order`) VALUES (27,'reward','Integra a la comunidad (a los seguidores, cofinanciadores, a un grupo social)','',8);

/*Data for the table `criteria_lang` */

INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (5,'ca','És original',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (5,'fr','C\'est original',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (5,'gl','É orixinal',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (6,'ca','És eficaç en la seva estratègia de comunicació',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (6,'fr','Soyez efficace dans votre stratégie de communication',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (6,'gl','É eficaz na súa estratexia de comunicación',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (7,'ca','Aporta suficient informació del projecte',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (7,'fr','Fournit des informations suffisantes sur le projet',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (7,'gl','Achega información suficiente do proxecto',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (8,'ca','Aporta productes, serveis o valors “desitjables” per a la comunitat',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (8,'fr','Fournit des produits, des services ou des valeurs \"souhaitables\" pour la communauté',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (8,'gl','Achega produtos, servizos ou valores \"desexables\" para a comunidade',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (9,'ca','Es afí a la cultura oberta',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (9,'fr','S\'apparente à la culture libre.',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (9,'gl','É afín á cultura aberta',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (10,'ca','Pot créixer, és escalable',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (10,'fr','Est capable de se développer, peut être mis à l\'échelle',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (11,'ca','Són coherents els recursos sol.licitats amb els objectius i el temps de desenvolupament',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (11,'fr','les ressources demandées sont cohérentes avec les objectifs et le calendrier de développement',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (11,'gl','Son coherentes os recursos solicitados cos obxectivos e o tempo de desenrolo',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (12,'ca','Risc proporcional al grau de benefici (social, cultural i/o econòmic)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (12,'fr','Risque proportionnel aux bénéfices attendus (sociaux, culturels et/ou économiques)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (12,'gl','Risco proporcional ó grado de beneficios (sociais, culturais e/ou económicos)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (13,'ca','Posseeix bona reputació en el seu sector',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (13,'fr','A bonne réputation dans son secteur',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (13,'gl','Posúe unha boa reputación no seu sector',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (14,'ca','Ha treballat amb organitzacions i col·lectius amb bona reputació',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (14,'fr','A travaillé avec des organismes et des collectives ayant bonne réputation',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (14,'gl','Traballou con organizacións e colectivos con boa reputación',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (15,'ca','Aporta informació sobre experiències anteriors (èxits i fracassos)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (15,'fr','Fournit des informations sur des expériences antérieurs (les succès comme les échecs)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (15,'gl','Achega información sobre experiencias anteriores (éxitos ou fracasos)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (16,'ca','Té capacitats per dur a terme el projecte',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (16,'fr','Possède la capacité de mener le projet à son terme',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (16,'gl','Ten capacidades para levar a cabo o proxecto',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (17,'ca','Compta amb un equip format',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (17,'fr','Dispose d\'une équipe formée',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (17,'gl','Conta cun equipo formado',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (18,'ca','Compta amb una comunitat de seguidors',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (18,'fr','Dispose d\'une communauté de suiveurs (followers)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (18,'gl','Conta cunha comunidade de seguidores',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (19,'ca','Té visibilitat a la xarxa',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (19,'fr','Gagne de la visibilité sur le réseau',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (19,'gl','Ten visibilidade na rede',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (20,'ca','És viable (el seu cost està inclòs en la producció del projecte)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (20,'fr','Est viable (vos coûts sont inclus dans la production du projet)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (20,'gl','É viable (o seu custo está incluído na produción do proxecto)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (21,'ca','Pot tenir efectes positius, transformadors (socials, culturals, empresarials)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (21,'fr','Peut avoir des conséquences positives (sociales, culturelles, entreprenariales)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (21,'gl','Pode ter efectos positivos, transformadores (sociais, culturais, empresariais)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (22,'ca','Aporta coneixement nou, de difícil accés o en procés de desaparèixer',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (22,'fr','Donne de nouvelles connaissances, difficile d\'accès ou en voie de disparition',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (22,'gl','Achega coñecemento novo, de difícil acceso ou en proceso de desaparecer',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (23,'ca','Aporta oportunitats de generar economia al voltant',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (23,'fr','Offre la possibilité de générer une économie liée (connexe)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (23,'gl','Achega oportunidades de xerar economía ó redor',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (24,'ca','Dóna llibertat en l\'ús dels seus resultats (és reproduïble)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (24,'fr','Autorise la réutilisation de ses résultats  (reproductibilité)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (24,'gl','Dá liberdade no uso dos seus resultados (é reproducible)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (25,'ca','Ofereix un retorn atractiu (per original, per útil, per inspirador...)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (25,'fr','Propose des retombées séduisantes (par leur originalité, leur utilité, leur inspiration...)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (25,'gl','Ofrece un retorno atractivo (por orixinal, por útil, por inspirador...)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (26,'ca','Compta amb actualitzacions',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (26,'fr','Compte sur les actualisations',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (26,'gl','Conta con actualizacións',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (27,'ca','Integra a la comunitat (a la gent seguidora, cofinançadora, a un grup social)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (27,'fr','S\'intègre à la communauté (des suiveurs, des cofinanceurs, à un groupe social)',NULL,0);
INSERT  INTO `criteria_lang`(`id`,`lang`,`title`,`description`,`pending`) VALUES (27,'gl','Integra á comunidade (os seguidores, cofinanceiros, a un grupo social)',NULL,0);

/*Data for the table `icon` */

INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('code','Código fuente','Por código fuente entendemos programas y software en general.','social',0);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('design','Diseño','Los diseños pueden ser de planos o patrones, esquemas, esbozos, diagramas de flujo, etc.','social',0);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('file','Archivos digitales','Los archivos digitales pueden ser de música, vídeo, documentos de texto, etc.','',0);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('manual','Manuales','Documentos prácticos detallando pasos, materiales formativos, bussiness plans, “how tos”, recetas, etc.','social',0);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('money','Dinero','Retornos económicos proporcionales a la inversión realizada, que se deben detallar en cantidad pero también forma de pago.','individual',50);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('other','Otro','Sorpréndenos con esta nueva tipología, realmente nos interesa :) ','',99);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('product','Producto','Los productos pueden ser los que se han producido, en edición limitada, o fragmentos u obras derivadas del original.','individual',0);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('service','Servicios','Acciones y/o sesiones durante tiempo determinado para satisfacer una necesidad individual o de grupo: una formación, una ayuda técnica, un asesoramiento, etc.','',0);
INSERT  INTO `icon`(`id`,`name`,`description`,`group`,`order`) VALUES ('thanks','Reconocimiento','Agradecimiento o reconocimiento','individual',90);

/*Data for the table `icon_lang` */

INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('code','ca','Codi font','Per codi font entenem programes i programari en general.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('code','el','Source code','By source code, we mean programs and software in general.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('code','en','Source code','By source code, we mean programs and software in general.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('code','eu','Iturri Kodea','Iturri Kode bezala ulertzen dugu  programak eta sofwareak orokorrean',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('code','fr','Code source','Par code source nous entendons tous programmes et logiciels en général.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('code','it','Codice sorgente','Per codice sorgente intendiamo genericamente programmi e software ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('code','pl','Source code','By source code, we mean programs and software in general.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('design','ca','Disseny','Els dissenys poden ser de plànols o patrons, esquemes, esbossos, diagrames de flux, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('design','el','Design','Designs can be drawings, patterns, sketches, rough drafts, flowcharts, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('design','en','Design','Designs can be drawings, patterns, sketches, rough drafts, flowcharts, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('design','eu','Diseinua','Diseinuak, planoak, ereduak, eskemak, ideia orokorrak, fluxu-diagramak etab. izan daitezke',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('design','fr','Plans','Les plans peuvent être des dessins et patrons, des modèles, schémas, croquis, diagrammes de flux etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('design','it','Design','Il design può essere costituito da piani, schemi, bozze, diagrammi di flusso, ecc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('design','pl','Design','Designs can be drawings, patterns, sketches, rough drafts, flowcharts, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('file','ca','Arxius digitals','Els arxius digitals poden ser de música, vídeo, documents de text, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('file','el','Digital files','Digital files may be music, video, text documents, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('file','en','Digital files','Digital files may be music, video, text documents, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('file','eu','Artxibategi digitalak','Artxibategi dilitalak,  musika, bideo, testu dokumentuak etab., izan daitezke',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('file','fr','Archives numériques','Les archives numériques peuvent être de type audio, vidéo, des documents textes, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('file','it','Archivi digitali ','Gli archivi digitali possono essere musicali, video, documenti di testo, ecc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('file','pl','Digital files','Digital files may be music, video, text documents, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('manual','ca','Manuals','Documents pràctics detallant passos, materials formatius, plans de negoci, “how tos”, receptes, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('manual','el','Manuals','Practical documentation that details step-by-step instructions, tutorials, business plans, how-to\'s, code cookbooks, etc. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('manual','en','Manuals','Practical documentation that details step-by-step instructions, tutorials, business plans, how-to\'s, code cookbooks, etc. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('manual','eu','Gidaliburuak','  Eman behar diren pausuak, formaziorako materialak, bussiness plans, “how tos”, errezetak, etab. agertzen diren dokumentu praktikoak',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('manual','fr','Modes d\'emploi','Documents pratiques détaillant les étapes, les matériaux et équipements, les \"business plans\", des manuels et recettes, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('manual','it','Manuali','Documenti pratici che spiegano gli step, materiale informativo, bussiness plans, “how tos”, ricette, ecc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('manual','pl','Manuals','Practical documentation that details step-by-step instructions, tutorials, business plans, how-to\'s, code cookbooks, etc. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('money','ca','Diners','Retorns econòmics proporcionals a la inversió realitzada, que s\'han de detallar en quantitat però també forma de pagament.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('money','el','Money','Economic benefits that are proportional to the investment made, with details about quantity and also form of payment',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('money','en','Money','Economic benefits that are proportional to the investment made, with details about quantity and also form of payment',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('money','eu','Dirua','Egindako inbertsioen itzulera ekonomiko proportzionalak, kantitatean zehaztu beharrekoa, baina baita ordaindu beharreko forman ere',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('money','fr','Argent','Les retombées économiques proportionnelles à l\'investissement, qui doivent être détaillées en quantité comme en mode de paiement.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('money','it','Denaro ','Benefici economici proporzionali all\'investimento realizzato le cui quantità devono essere specificate in forma di pagamento. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('money','pl','Money','Economic benefits that are proportional to the investment made, with details about quantity and also form of payment',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('other','ca','Altres','Sorprèn-nos amb aquesta nova tipologia, realment ens interessa :) ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('other','el','Other','Surprise us with this category, we\'re really interested!',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('other','en','Other','Surprise us with this category, we\'re really interested!',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('other','eu','Beste bat','Harritu gaitzazu tipologi berri honekin, benetan interesatzen gaitu :)',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('other','fr','Divers','Surprenez-nous avec une nouvelle catégorie, cela nous intéresse vraiment ;-)',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('other','it','Altro',' Sorprendici con questa nuova metodologia, ci interessa davvero :)',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('other','pl','Other','Surprise us with this category, we\'re really interested!',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('product','ca','Producte','Els productes poden ser els que s\'han produït, en edició limitada, o fragments o obres derivades de l\'original.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('product','el','Product','Products can be limited editions or prototypes, or pieces or works derived from the original.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('product','en','Product','Products can be limited editions or prototypes, or pieces or works derived from the original.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('product','eu','Produktua','Produktuak edizio mugatuan, edo zati, zein jatorritik deribatuta dauden lanetatik ekoiztutakoak izan daitezke',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('product','fr','Objets','Ces objets peuvent être produits par vos soins, en édition limitée, ou des fragments et œuvres dérivées de l\'\'original.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('product','it','Prodotto','I prodotti possono essere quelli che si sono realizzati, in edizione limitada, frammantaria o opere derivate dall\'originale.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('product','pl','Product','Products can be limited editions or prototypes, or pieces or works derived from the original.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('service','ca','Serveis','Accions i/o sessions durant temps determinat per satisfer una necessitat individual o de grup: una formació, una ajuda tècnica, un assessorament, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('service','el','Services','Actions or sessions during a specific period of time which satisfy an individual or group need: education, technical assistance, advice, etc. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('service','en','Services','Actions or sessions during a specific period of time which satisfy an individual or group need: education, technical assistance, advice, etc. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('service','eu','Zerbitzuak','Taldeko zein bakarkako beharrizanak asetzeko  egindako ekintza edo/eta saioak denbora jakin batean: formazioa, laguntza teknikoa, aholkularitza, etab. ...',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('service','fr','Services','Actions ou sessions à durée déterminée destinées à satisfaire un besoin individuel ou collectif: une formation, une assistance technique, du conseil, etc.',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('service','it','Servizi','Azioni e/o sessioni per un tempo determinato per soddisfare una necessità individuale o di gruppo: formazione, aiuto tecnico, consulenza, ecc. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('service','pl','Services','Actions or sessions during a specific period of time which satisfy an individual or group need: education, technical assistance, advice, etc. ',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('thanks','ca','Reconeixement','Agraïment o reconeixement',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('thanks','el','Acknowledgment','Gratitude or acknowledgment',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('thanks','en','Acknowledgment','Gratitude or acknowledgment',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('thanks','fr','Remerciements','Remerciements',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('thanks','it','Riconoscenza','Ringraziamento o riconoscenza',0);
INSERT  INTO `icon_lang`(`id`,`lang`,`name`,`description`,`pending`) VALUES ('thanks','pl','Acknowledgment','Gratitude or acknowledgment',0);

/*Data for the table `icon_license` */

INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','agpl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','apache');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','balloon');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','bsd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','cernohl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','gpl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','gpl2');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','lgpl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','mit');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','mpl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','odbl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','odcby');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','oshw');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','pd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','php');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','tapr');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('code','xoln');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','balloon');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','cc0');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','ccby');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','ccbync');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','ccbyncnd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','ccbyncsa');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','ccbynd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','ccbysa');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','cernohl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','fal');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','fdl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','gpl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','gpl2');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','oshw');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','pd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('design','tapr');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','cc0');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','ccby');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','ccbync');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','ccbyncnd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','ccbyncsa');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','ccbynd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','ccbysa');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('file','fal');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','cc0');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','ccby');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','ccbync');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','ccbyncnd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','ccbyncsa');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','ccbynd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','ccbysa');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','cernohl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','fal');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','fdl');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','freebsd');
INSERT  INTO `icon_license`(`icon`,`license`) VALUES ('manual','pd');

/*Data for the table `license` */

INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('agpl','Affero General Public License','Licencia pública general de Affero para software libre que corra en servidores de red','','http://www.affero.org/oagf.html',2);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('apache','Apache License','Licencia Apache de software libre, que no exige que las obras derivadas se distribuyan usando la misma licencia ni como software libre','','http://www.apache.org/licenses/LICENSE-2.0',10);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('balloon','Balloon Open Hardware License','Licencia para hardware libre de los procesadores Balloon','','http://balloonboard.org/licence.html',20);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('bsd','Berkeley Software Distribution','Licencia de software libre permisiva, con pocas restricciones y que permite el uso del código fuente en software no libre','open','http://es.wikipedia.org/wiki/Licencia_BSD',5);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('cc0','CC0 Universal (Dominio Público)','Licencia Creative Commons de obra dedicada al dominio público, mediante renuncia a todos los derechos de autoría sobre la misma','','http://creativecommons.org/publicdomain/zero/1.0/deed.es',25);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('ccby','CC - Reconocimiento','Licencia Creative Commons (bienes comunes creativos) con reconocimiento de autoría','open','http://creativecommons.org/licenses/by/4.0/deed.es_ES',12);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('ccbync','CC - Reconocimiento - NoComercial','Licencia Creative Commons (bienes comunes creativos) con reconocimiento de autoría y sin que se pueda hacer uso comercial','','http://creativecommons.org/licenses/by-nc/2.0/deed.es_ES',13);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('ccbyncnd','CC - Reconocimiento - NoComercial - SinObraDerivada','Licencia Creative Commons (bienes comunes creativos) con reconocimiento de autoría, sin que se pueda hacer uso comercial ni otras obras derivadas','','http://creativecommons.org/licenses/by-nc-nd/2.0/deed.es_ES',15);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('ccbyncsa','CC - Reconocimiento - NoComercial - CompartirIgual','Licencia Creative Commons (bienes comunes creativos) con reconocimiento de autoría, sin que se pueda hacer uso comercial y a compartir en idénticas condiciones','','http://creativecommons.org/licenses/by-nc-sa/3.0/deed.es_ES',14);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('ccbynd','CC - Reconocimiento - SinObraDerivada','Licencia Creative Commons (bienes comunes creativos) con reconocimiento de autoría, sin que se puedan hacer obras derivadas ','','http://creativecommons.org/licenses/by-nd/2.0/deed.es_ES',17);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('ccbysa','CC - Reconocimiento - CompartirIgual','Licencia Creative Commons (bienes comunes creativos) con reconocimiento de autoría y a compartir en idénticas condiciones','open','http://creativecommons.org/licenses/by-sa/2.0/deed.es_ES',16);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('cernohl','CERN OHL Open Hardware Licence','Licencia desarollada por el CERN - Laboratorio Europeo de Física de Partículas Elementales para poryectos de Hardware','open','http://www.ohwr.org/projects/cernohl/wiki',98);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('fal','Free Art License','Licencia de arte libre','','http://artlibre.org/lal/es',99);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('fdl','Free Documentation License ','Licencia de documentación libre de GNU, pudiendo ser ésta copiada, redistribuida, modificada e incluso vendida siempre y cuando se mantenga bajo los términos de esa misma licencia','open','http://www.gnu.org/copyleft/fdl.html',4);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('freebsd','FreeBSD Documentation License','Licencia de documentación libre para el sistema operativo FreeBSD','open','http://www.freebsd.org/copyright/freebsd-doc-license.html',6);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('gpl','General Public License','Licencia Pública General de GNU para la libre distribución, modificación y uso de software','open','http://www.gnu.org/licenses/gpl.html',1);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('gpl2','General Public License (v.2)','Licencia Pública General de GNU para la libre distribución, modificación y uso de software','open','http://www.gnu.org/licenses/gpl-2.0.html',1);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('lgpl','Lesser General Public License','Licencia Pública General Reducida de GNU, para software libre que puede ser utilizado por un programa no-GPL, que a su vez puede ser software libre o no','open','http://www.gnu.org/copyleft/lesser.html',3);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('mit','MIT / X11 License','Licencia tanto para software libre como para software no libre, que permite no liberar los cambios realizados sobre el programa original','','http://es.wikipedia.org/wiki/MIT_License',8);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('mpl','Mozilla Public License','Licencia pública de Mozilla de software libre, que posibilita la reutilización no libre del software, sin restringir la reutilización del código ni el relicenciamiento bajo la misma licencia','','http://www.mozilla.org/MPL/',7);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('odbl','Open Database License ','Licencia de base de datos abierta, que permite compartir, modificar y utilizar bases de datos en idénticas condiciones','open','http://www.opendatacommons.org/licenses/odbl/',22);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('odcby','Open Data Commons Attribution License','Licencia de datos abierta, que permite compartir, modificar y utilizar los datos en idénticas condiciones atribuyendo la fuente original','open','http://www.opendatacommons.org/licenses/by/',23);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('oshw','TAPR Open Hardware License','Licencia para obras de hardware libre','open','http://www.tapr.org/OHL',18);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('pd','Dominio público','La obra puede ser libremente reproducida, distribuida, transmitida, usada, modificada, editada u objeto de cualquier otra forma de explotación para el propósito que sea, comercial o no','','http://creativecommons.org/licenses/publicdomain/deed.es',24);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('php','PHP License','Licencia bajo la que se publica el lenguaje de programación PHP','','http://www.php.net/license/',9);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('tapr','TAPR Noncommercial Hardware License','Licencia para obras de hardware libre con limitación en su comercialización ','','http://www.tapr.org/NCL.html',19);
INSERT  INTO `license`(`id`,`name`,`description`,`group`,`url`,`order`) VALUES ('xoln','Procomún de la XOLN','Licencia de red abierta, libre y neutral, como acuerdo de interconexión entre iguales promovido por Guifi.net','open','http://guifi.net/es/ProcomunXOLN',21);

/*Data for the table `license_lang` */

INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('agpl','ca','Affero General Public License','Llicència pública general d\'Affero per a programari lliure que corri en servidors de xarxa','http://www.affero.org/oagf.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('agpl','el','Affero General Public License','Affero General Public License for open networked software','http://www.affero.org/oagf.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('agpl','en','Affero General Public License','Affero General Public License for open networked software','http://www.affero.org/oagf.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('agpl','eu','Affero General Public License',' Afferoren lizentzia publiko orokorra, sare zebitzarietan iragaten den  software libreetzako. ','http://www.affero.org/oagf.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('agpl','gl','Affero General Public License','Licenza pública xeral de Affero para software libre que corran en servidores de rede','http://www.affero.org/oagf.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('agpl','pl','Affero General Public License','Affero General Public License for open networked software','http://www.affero.org/oagf.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('apache','ca','Apache License','Llicencia Apatxe de programari lliure, que no exigeix que les obres derivades es distribueixin usant la mateixa llicència ni com a programari lliure','http://www.apache.org/licenses/LICENSE-2.0',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('apache','el','Apache License','Apache License for open software, that does not require that derivative works be distributed with the same license, or even as open software','http://www.apache.org/licenses/LICENSE-2.0',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('apache','en','Apache License','Apache License for open software, that does not require that derivative works be distributed with the same license, or even as open software','http://www.apache.org/licenses/LICENSE-2.0',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('apache','eu','Apache License','Sofware libredun Apache Lizentzia, ez du eskatzen lan deribatuak lizentzia berdinarekin  edota sofware libre moduan banatzea.','http://www.apache.org/licenses/LICENSE-2.0',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('apache','gl','Apache License','Licenza Apache de software libre, que non esixe que as obras derivadas se distribúan empregando a mesma licenza nin como software libre','http://www.apache.org/licenses/LICENSE-2.0',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('apache','pl','Apache License','Apache License for open software, that does not require that derivative works be distributed with the same license, or even as open software','http://www.apache.org/licenses/LICENSE-2.0',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('balloon','ca','Balloon Open Hardware License','Llicència per a maquinari lliure dels processadors Balloon','http://balloonboard.org/licence.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('balloon','el','Balloon Open Hardware License','License for open Balloon boards','http://balloonboard.org/licence.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('balloon','en','Balloon Open Hardware License','License for open Balloon boards','http://balloonboard.org/licence.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('balloon','eu','Balloon Open Hardware License','Ballon prozesadoreen hardwadre libreen lizentzia','http://balloonboard.org/licence.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('balloon','gl','Balloon Open Hardware License','Licenza para hardware libre dos procesadores Balloon','http://balloonboard.org/licence.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('balloon','pl','Balloon Open Hardware License','License for open Balloon boards','http://balloonboard.org/licence.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('bsd','ca','Berkeley Software Distribution','Llicència de programari lliure permissiva, amb poques restriccions i que permet l\'ús del codi font en programari no lliure','http://es.wikipedia.org/wiki/Licencia_BSD',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('bsd','el','Berkeley Software Distribution Licenses','Permissive free software licenses, with few restrictions, that permit the use of source code in non-free software','http://en.wikipedia.org/wiki/BSD_licenses',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('bsd','en','Berkeley Software Distribution Licenses','Permissive free software licenses, with few restrictions, that permit the use of source code in non-free software','http://en.wikipedia.org/wiki/BSD_licenses',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('bsd','eu','Berkeley Software Distribution','Software aske permisiboaren lizentzia, murrizketa gutxirekin eta iturri kodearen erabilera baimentzen duen librea ez den sofware.','http://es.wikipedia.org/wiki/Licencia_BSD',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('bsd','gl','Berkeley Software Distribution','Licenza de software libre permisiva, con poucas restricións e que permite o emprego do código fonte en software non libre','http://gl.wikipedia.org/wiki/Licenza_BSD',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('bsd','pl','Berkeley Software Distribution Licenses','Permissive free software licenses, with few restrictions, that permit the use of source code in non-free software','http://en.wikipedia.org/wiki/BSD_licenses',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cc0','ca','CC0 Universal (Domini Públic)','Llicència Creative Commons d\'obra dedicada al domini públic, mitjançant renúncia a tots els drets d\'autoria sobre la mateixa','http://creativecommons.org/publicdomain/zero/1.0/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cc0','el','CC0 Universal (Public Domain)','Creative Commons License for works dedicated to the public domain, by which all intellectual property rights over a work are waived','http://creativecommons.org/publicdomain/zero/1.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cc0','en','CC0 Universal (Public Domain)','Creative Commons License for works dedicated to the public domain, by which all intellectual property rights over a work are waived','http://creativecommons.org/publicdomain/zero/1.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cc0','eu','CC0 Universal (Domeinu publikoa)','Creative Commons Lizentzia domeinu publikora eskeinia, beraren gainetik autoretza eskubideari uko egitearen bitartez','http://creativecommons.org/publicdomain/zero/1.0/deed.es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cc0','gl','CC0 Universal (Dominio Público)','Licenza Creative Commons de obra adicada ó dominio público, mediante renuncia a todos os dereitos de autoría sobre a mesma','http://creativecommons.org/publicdomain/zero/1.0/deed.es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cc0','pl','CC0 Universal (Public Domain)','Creative Commons License for works dedicated to the public domain, by which all intellectual property rights over a work are waived','http://creativecommons.org/publicdomain/zero/1.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccby','ca','CC - Reconeixement','Llicència Creative Commons (béns comuns creatius) amb reconeixement d\'autoria','http://creativecommons.org/licenses/by/2.0/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccby','el','CC - Attribution','Creative Commons License with attribution','http://creativecommons.org/licenses/by/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccby','en','CC - Attribution','Creative Commons License with attribution','http://creativecommons.org/licenses/by/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccby','eu','CC - Onarpena ','Creative Commons Lizentzia (ondasun arrunt sortzaileak) egiletzaren onarpenarekin','http://creativecommons.org/licenses/by/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccby','gl','CC - Recoñecemento','Licenza Creative Commons (bens comúns creativos) con recoñecemento de autoría','http://creativecommons.org/licenses/by/4.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccby','pl','CC - Attribution','Creative Commons License with attribution','http://creativecommons.org/licenses/by/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbync','ca','CC - Reconeixement - NoComercial','Llicència Creative Commons (béns comuns creatius) amb reconeixement d\'autoria i sense que es pugui fer ús comercial','http://creativecommons.org/licenses/by-nc/2.0/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbync','el','CC - Attribution-NonCommercial','Creative Commons License with attribution that does not permit commercial use','http://creativecommons.org/licenses/by-nc/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbync','en','CC - Attribution-NonCommercial','Creative Commons License with attribution that does not permit commercial use','http://creativecommons.org/licenses/by-nc/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbync','eu','CC - Onarpena - EzKomertziala','Creative Coomons Lizentzia (ondasun arrunt sortzaileak) egiletzaren onarpenarekin eta ezin daitekeena erabilera komertzialerako erabili','http://creativecommons.org/licenses/by-nc/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbync','gl','CC - Recoñecemento - NonComercial','Licenza Creative Commons (bens comúns creativos) con recoñecemento de autoría e sen que se poida facer uso comercial','http://creativecommons.org/licenses/by-nc/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbync','pl','CC - Attribution-NonCommercial','Creative Commons License with attribution that does not permit commercial use','http://creativecommons.org/licenses/by-nc/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncnd','ca','CC - Reconeixement - NoComercial - SenseObraDerivada','Llicència Creative Commons (béns comuns creatius) amb reconeixement d\'autoria, sense que es pugui fer ús comercial ni altres obres derivades','http://creativecommons.org/licenses/by-nc-nd/2.0/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncnd','el','CC - Attribution  - NonCommercial - NoDerivs','Creative Commons License with attribution, that does not allow commercial use nor derivative works','http://creativecommons.org/licenses/by-nc-nd/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncnd','en','CC - Attribution  - NonCommercial - NoDerivs','Creative Commons License with attribution, that does not allow commercial use nor derivative works','http://creativecommons.org/licenses/by-nc-nd/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncnd','eu','CC - Onarpena - EzKomertziala - LanDeribatuGabea','Creative Commons Lizentzia (ondasun arrunt sortzaileak) egiletzaren onarpenarekin, ezin daitekeena erabilera komertzialetarako erabili ezta beste lan deribatuetarako ere','http://creativecommons.org/licenses/by-nc-nd/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncnd','gl','CC - Recoñecemento - NonComercial - SenObraDerivada','Licenza Creative Commons (bens comúns creativos) con recoñecemento de autoría, sen que se poida facer uso comercial nin outras obras derivadas','http://creativecommons.org/licenses/by-nc-nd/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncnd','pl','CC - Attribution  - NonCommercial - NoDerivs','Creative Commons License with attribution, that does not allow commercial use nor derivative works','http://creativecommons.org/licenses/by-nc-nd/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncsa','ca','CC - Reconeixement - NoComercial - CompartirIgual','Llicència Creative Commons (béns comuns creatius) amb reconeixement d\'autoria, sense que es pugui fer ús comercial i a compartir en idèntiques condicions','http://creativecommons.org/licenses/by-nc-sa/3.0/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncsa','el','CC - Attribution - NonCommercial - ShareAlike','Creative Commons License with attribution, that does not allow commercial use, and only allows sharing under identical licensing conditions','http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncsa','en','CC - Attribution - NonCommercial - ShareAlike','Creative Commons License with attribution, that does not allow commercial use, and only allows sharing under identical licensing conditions','http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncsa','eu','CC - Onarpena - EzKomertziala - BerdinPartekatua','Creative Commons Lizentzia (ondasun arrunt sortzaileak) egiletzaren onarpenarekin, ezin daitekeena erabilera komertzialerako erabili eta baldintza berdinetan partekatua','http://creativecommons.org/licenses/by-nc-sa/3.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncsa','gl','CC - Recoñecemento - NonComercial - PartillarIgual','Licenza Creative Commons (bens comúns creativos) con recoñecemento de autoría, sen que se poida facer uso comercial e a partillar en idénticas condicións','http://creativecommons.org/licenses/by-nc-sa/3.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbyncsa','pl','CC - Attribution - NonCommercial - ShareAlike','Creative Commons License with attribution, that does not allow commercial use, and only allows sharing under identical licensing conditions','http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbynd','ca','CC - Reconeixement - SenseObraDerivada','Llicència Creative Commons (béns comuns creatius) amb reconeixement d\'autoria, sense que s\'en puguin fer obres derivades ','http://creativecommons.org/licenses/by-nd/2.0/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbynd','el','CC - Attribution - NoDerivs','Creative Commons License with attribution that does not allow derivative works','http://creativecommons.org/licenses/by-nd/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbynd','en','CC - Attribution - NoDerivs','Creative Commons License with attribution that does not allow derivative works','http://creativecommons.org/licenses/by-nd/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbynd','eu','CC - Onarpena - LanDeribatuGabea','Creative Commons lizentzia (ondasun arrunt sortzaileak) egiletzaren onarpenarekin, ezin daitekeena lan deribaturik egin','http://creativecommons.org/licenses/by-nd/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbynd','gl','CC - Recoñecemento - SenObraDerivada','Licenza Creative Commons (bens comúns creativos) con recoñecemento de autoría, sen que se poidan facer obras derivadas ','http://creativecommons.org/licenses/by-nd/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbynd','pl','CC - Attribution - NoDerivs','Creative Commons License with attribution that does not allow derivative works','http://creativecommons.org/licenses/by-nd/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbysa','ca','CC - Reconeixement - CompartirIgual','Llicència Creative Commons (béns comuns creatius) amb reconeixement d\'autoria i a compartir en idèntiques condicions','http://creativecommons.org/licenses/by-sa/2.0/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbysa','el','CC - Attribution - ShareAlike','Creative Commons License with attribution that only allows sharing under identical licensing conditions','http://creativecommons.org/licenses/by-sa/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbysa','en','CC - Attribution - ShareAlike','Creative Commons License with attribution that only allows sharing under identical licensing conditions','http://creativecommons.org/licenses/by-sa/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbysa','eu','CC - Onarpena - BerdinPartekatua','Creative Commons Lizentzia (ondasun arrunt sortzailea) egiletzaren onarpenarekin eta baldintza berdinetan partekatua','http://creativecommons.org/licenses/by-sa/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbysa','gl','CC - Recoñecemento - PartillarIgual','Licenza Creative Commons (bens comúns creativos) con recoñecemento de autoría e ó partillar en idénticas condicións','http://creativecommons.org/licenses/by-sa/2.0/deed.es_ES',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('ccbysa','pl','CC - Attribution - ShareAlike','Creative Commons License with attribution that only allows sharing under identical licensing conditions','http://creativecommons.org/licenses/by-sa/2.0/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cernohl','ca','CERN OHL Open Hardware Licence','Llicència desenvolupada pel CERN - Laboratori Europeu de Física de Partícules Elementals per a projectes de Hardware','http://www.ohwr.org/projects/cernohl/wiki',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cernohl','en','CERN OHL Open Hardware Licence','Licenza sviluppata dal CERN - Laboratorio Europeo di Fisica della Particelle Elementari per progetti di Hardware','http://www.ohwr.org/projects/cernohl/wiki',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('cernohl','gl','CERN OHL Open Hardware Licence','Licenza desenrolada polo CERN - Laboratorio Europeo de Física de Partículas Elementais para proxectos de Hardware','http://www.ohwr.org/projects/cernohl/wiki',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fal','ca','Free Art License','Llicència d\'art lliure','http://artlibre.org/licence/lal/es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fal','el','Free Art License','Free art license','http://artlibre.org/licence/lal/en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fal','en','Free Art License','Free art license','http://artlibre.org/licence/lal/en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fal','eu','Free Art License','Arte librerako Lizentzia','http://artlibre.org/licence/lal/es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fal','gl','Free Art License','Licenza de arte libre','http://artlibre.org/licence/lal/es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fal','pl','Free Art License','Free art license','http://artlibre.org/licence/lal/en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fdl','ca','Free Documentation License ','Llicència de documentació lliure de GNU, podent ser aquesta copiada, redistribuïda, modificada i fins i tot venuda sempre que es mantingui sota els termes d\'aquesta mateixa llicència','http://www.gnu.org/copyleft/fdl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fdl','el','Free Documentation License ','GNU free documentation license, which can be copied, redistributed, modified and even sold, as long as the original terms of this same license are maintained.','http://www.gnu.org/copyleft/fdl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fdl','en','Free Documentation License ','GNU free documentation license, which can be copied, redistributed, modified and even sold, as long as the original terms of this same license are maintained.','http://www.gnu.org/copyleft/fdl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fdl','eu','Free Documentation License ','GNUren dokumentazio librerako lizentzia. Hau, kopiatua, birbanatua, eraldatua eta baita ere saldua izan daiteke beti ere lizentzia horren balditzetan oinarritzen bada','http://www.gnu.org/copyleft/fdl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fdl','gl','Free Documentation License ','Licenza de documentación libre de GNU, podendo ser ésta copiada, redistribuída, modificada e incluso vendida sempre e cando se manteña baixo os térmos desa mesma licenza','http://www.gnu.org/copyleft/fdl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('fdl','pl','Free Documentation License ','GNU free documentation license, which can be copied, redistributed, modified and even sold, as long as the original terms of this same license are maintained.','http://www.gnu.org/copyleft/fdl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('freebsd','ca','FreeBSD Documentation License','Llicència de documentació lliure per al sistema operatiu FreeBSD','http://www.freebsd.org/copyright/freebsd-doc-license.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('freebsd','el','FreeBSD Documentation License','Free Documentation License for the FreeBSD operating system','http://www.freebsd.org/copyright/freebsd-doc-license.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('freebsd','en','FreeBSD Documentation License','Free Documentation License for the FreeBSD operating system','http://www.freebsd.org/copyright/freebsd-doc-license.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('freebsd','eu','FreeBSD Documentation License','Dokumentazio libreko FreeBSD sistema eragilearentzako lizentzia','http://www.freebsd.org/copyright/freebsd-doc-license.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('freebsd','gl','FreeBSD Documentation License','Licenza de documentación libre para o sistema operativo FreeBSD','http://www.freebsd.org/copyright/freebsd-doc-license.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('freebsd','pl','FreeBSD Documentation License','Free Documentation License for the FreeBSD operating system','http://www.freebsd.org/copyright/freebsd-doc-license.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl','ca','General Public License','Llicència Pública General de GNU per a la lliure distribució, modificació i ús de programari','http://www.gnu.org/licenses/gpl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl','el','General Public License','GNU General Public License for the free distribution, modification, and use of software','http://www.gnu.org/licenses/gpl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl','en','General Public License','GNU General Public License for the free distribution, modification, and use of software','http://www.gnu.org/licenses/gpl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl','eu','General Public License','GNUren Lizentzia Publiko Orokorra, sofware-aren banaketa, aldaketa eta erabilera libre baterako','http://www.gnu.org/licenses/gpl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl','gl','General Public License','Licenza Pública Xeral de GNU para a libre distribución, modificación e uso de software','http://www.gnu.org/licenses/gpl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl','pl','General Public License','GNU General Public License for the free distribution, modification, and use of software','http://www.gnu.org/licenses/gpl.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl2','ca','General Public License (v.2)','Llicència Pública General de GNU per a la lliure distribució, modificació i ús de programari','http://www.gnu.org/licenses/gpl-2.0.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl2','el','General Public License (v.2)','GNU General Public License for the free distribution, modification, and use of software','http://www.gnu.org/licenses/gpl-2.0.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl2','en','General Public License (v.2)','GNU General Public License for the free distribution, modification, and use of software','http://www.gnu.org/licenses/gpl-2.0.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl2','eu','General Public License (v.2)','GNUren Litzentzia Publico Orokorra, banaketa, aldakera eta sofwarearen erabilera libre baterako ','http://www.gnu.org/licenses/gpl-2.0.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl2','gl','General Public License (v.2)','Licenza Pública General de GNU para a libre distribución, modificación e uso de software','http://www.gnu.org/licenses/gpl-2.0.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('gpl2','pl','General Public License (v.2)','GNU General Public License for the free distribution, modification, and use of software','http://www.gnu.org/licenses/gpl-2.0.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('lgpl','ca','Lesser General Public License','Llicència Pública General Reduïda de GNU, per a programari lliure que pot ser utilitzat per un programa no-GPL, que al seu torn pot ser programari lliure o no','http://www.gnu.org/copyleft/lesser.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('lgpl','el','Lesser General Public License','GNU Lesser General Public License for free software that can be used by a non-GPL program, which in turn can be free software or not. ','http://www.gnu.org/copyleft/lesser.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('lgpl','en','Lesser General Public License','GNU Lesser General Public License for free software that can be used by a non-GPL program, which in turn can be free software or not. ','http://www.gnu.org/copyleft/lesser.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('lgpl','eu','Lesser General Public License','GNUren Lizentzia Publiko Orokorra, no-GPL programa erabili dezakeen software libre baterako, baina honekin batera softwarea librea izan daiteke edo ez','http://www.gnu.org/copyleft/lesser.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('lgpl','gl','Lesser General Public License','Licenza Pública Xeral Reducida de GNU, para software libre que pode ser empregado por un programa non-GPL, que á súa vez pode ser software libre ou non','http://www.gnu.org/copyleft/lesser.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('lgpl','pl','Lesser General Public License','GNU Lesser General Public License for free software that can be used by a non-GPL program, which in turn can be free software or not. ','http://www.gnu.org/copyleft/lesser.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mit','ca','MIT / X11 License','Llicència tant per a programari lliure com per a programari no lliure, que permet no alliberar els canvis realitzats sobre el programa original','http://ca.wikipedia.org/wiki/Llic%C3%A8ncia_X11',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mit','el','MIT / X11 License','License both for open and closed software, that allows changes made to the original program to be protected','http://es.wikipedia.org/wiki/MIT_License',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mit','en','MIT / X11 License','License both for open and closed software, that allows changes made to the original program to be protected','http://es.wikipedia.org/wiki/MIT_License',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mit','eu','MIT / X11 License','Software libreentzako  zein librea ez denarentzako lizentzia, jatorrizko programan egon diren aldaketak ez askatzea onartzen duena','http://es.wikipedia.org/wiki/MIT_License',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mit','gl','MIT / X11 License','Licenza tanto para software libre coma para software non libre, que permite non liberar os cambios feitos sobre o programa orixinal','http://es.wikipedia.org/wiki/MIT_License',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mit','pl','MIT / X11 License','License both for open and closed software, that allows changes made to the original program to be protected','http://es.wikipedia.org/wiki/MIT_License',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mpl','ca','Mozilla Public License','Llicència pública de Mozilla de programari lliure, que possibilita la reutilització no lliure del programari, sense restringir-ne la reutilització del codi ni el rellicenciament sota la mateixa llicència','http://www.mozilla.org/MPL/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mpl','el','Mozilla Public License','Mozilla Public License for open software that makes possible the non-open reuse of software, without restricting the reuse of the code or the relicensing under the same license. ','http://www.mozilla.org/MPL/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mpl','en','Mozilla Public License','Mozilla Public License for open software that makes possible the non-open reuse of software, without restricting the reuse of the code or the relicensing under the same license. ','http://www.mozilla.org/MPL/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mpl','eu','Mozilla Public License','Mozilla software librearen Lizentzia publikoa, softwarearen erabilera ez librea ahalbidetzen duena eta  kodearen bererabilera baita lizentzia berdinaren barruan birlizentziamentua ez duena murrizten','http://www.mozilla.org/MPL/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mpl','gl','Mozilla Public License','Licenza pública de Mozilla de software libre, que posibilita a reutilización non libre do software, sen restrinxir a reutilización do código nin o relicenzamento baixo a mesma licenza','http://www.mozilla.org/MPL/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('mpl','pl','Mozilla Public License','Mozilla Public License for open software that makes possible the non-open reuse of software, without restricting the reuse of the code or the relicensing under the same license. ','http://www.mozilla.org/MPL/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odbl','ca','Open Database License ','Llicència de base de dades oberta, que permet compartir, modificar i utilitzar bases de dades en idèntiques condicions','http://www.opendatacommons.org/licenses/odbl/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odbl','el','Open Database License ','Open Database License that allows for sharing, modifying, and using databases in identical conditions','http://www.opendatacommons.org/licenses/odbl/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odbl','en','Open Database License ','Open Database License that allows for sharing, modifying, and using databases in identical conditions','http://www.opendatacommons.org/licenses/odbl/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odbl','eu','Open Database License ','Datu-base irekiaren Lizentzia, banatzea, aldatzea eta baldintza berdinetan erabili ahal diren base-datuak  baimentzen dituena','http://www.opendatacommons.org/licenses/odbl/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odbl','gl','Open Database License ','Licenza de base de datos aberta, que permite partillar, modificar e empregar bases de datos en idénticas condicións','http://www.opendatacommons.org/licenses/odbl/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odbl','it','Open Database License ','Licenza di dati aperta che permette condividere, modificare e utilizzare base di dati con le stesse condizioni ','http://www.opendatacommons.org/licenses/odbl/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odbl','pl','Open Database License ','Open Database License that allows for sharing, modifying, and using databases in identical conditions','http://www.opendatacommons.org/licenses/odbl/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odcby','ca','Open Data Commons Attribution License','Llicència de dades oberta, que permet compartir, modificar i utilitzar les dades en idèntiques condicions atribuint-hi la font original','http://www.opendatacommons.org/licenses/by/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odcby','el','Open Data Commons Attribution License','Open data license that allows for sharing, modifying and using data under identical conditions, as long as attribution is given for the original source','http://www.opendatacommons.org/licenses/by/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odcby','en','Open Data Commons Attribution License','Open data license that allows for sharing, modifying and using data under identical conditions, as long as attribution is given for the original source','http://www.opendatacommons.org/licenses/by/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odcby','eu','Open Data Commons Attribution License','Datu irekien Lizentzia, banatzea, aldatzen eta datuak baldintza berdinetan erabiliz  jatorrizko iturritik egotziz baimentzen duena','http://www.opendatacommons.org/licenses/by/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odcby','gl','Open Data Commons Attribution License','Licenza de datos aberta, que permite partillar, modificar e empregar os datos en idénticas condicións atribuíndo a fonte orixinal','http://www.opendatacommons.org/licenses/by/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odcby','it','Open Data Commons Attribution License','Licenza di dati aperta che permette condividere, modificare e utilizzare i dati nelle stesse condizioni con l\'attribuzione della source originale ','http://www.opendatacommons.org/licenses/by/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('odcby','pl','Open Data Commons Attribution License','Open data license that allows for sharing, modifying and using data under identical conditions, as long as attribution is given for the original source','http://www.opendatacommons.org/licenses/by/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('oshw','ca','Open Hardware License','Llicència per a obres de maquinari lliure','http://www.tapr.org/OHL',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('oshw','el','Open Hardware License','Open Hardware License','http://www.tapr.org/OHL',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('oshw','en','TAPR Open Hardware License','TAPR Open Hardware License','http://www.tapr.org/OHL',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('oshw','eu','Open Hardware License','Hardware libre dun lanentzako Lizentzia ','http://www.tapr.org/OHL',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('oshw','gl','TAPR Open Hardware License','Licenza para obras de hardware libre','http://www.tapr.org/OHL',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('oshw','it','TAPR Open Hardware License','Licenza per opera di hardware libero','http://www.tapr.org/OHL',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('oshw','pl','Open Hardware License','Open Hardware License','http://www.tapr.org/OHL',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('pd','ca','Domini públic','L\'obra pot ser lliurement reproduïda, distribuïda, transmesa, usada, modificada, editada o objecte de qualsevol altra forma d\'explotació per al propòsit que sigui, comercial o no','http://creativecommons.org/licenses/publicdomain/deed.ca',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('pd','el','Public Domain','The work may be freely reproduced, distributed, transmitted, used, modified, edited, or subject to any other form of exploitation for any commerical or non-commercial use.','http://creativecommons.org/licenses/publicdomain/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('pd','en','Public Domain','The work may be freely reproduced, distributed, transmitted, used, modified, edited, or subject to any other form of exploitation for any commerical or non-commercial use.','http://creativecommons.org/licenses/publicdomain/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('pd','eu','Eremu Publikoa','Lana libreki errepikatua, banatua, igorria, erabilia, eraldatua, argitaratua edo beste edozein explotaziorako objetu bezala izan daiteke edozein helbururekin  komertziala izan zein ez izan','http://creativecommons.org/licenses/publicdomain/deed.es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('pd','gl','Dominio público','A obra pode ser libremente reproducida, distribuída, transmitida, empregada, modificada, editada ou obxecto de calqueira outra forma de explotación para o propósito que sexa, comercial ou non','http://creativecommons.org/licenses/publicdomain/deed.es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('pd','it','Dominio pubblico','L\'opera può essere liberamene prodotta, distribuita, trasmessa, usata, modificata, cosí come oggetto di qualsiasi tipo di utilizzo per qualsiasi finalità commerciale o no ','http://creativecommons.org/licenses/publicdomain/deed.es',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('pd','pl','Public Domain','The work may be freely reproduced, distributed, transmitted, used, modified, edited, or subject to any other form of exploitation for any commerical or non-commercial use.','http://creativecommons.org/licenses/publicdomain/deed.en',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('php','ca','PHP License','Llicència sota la que es publica el llenguatge de programació PHP','http://www.php.net/license/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('php','el','PHP License','License under which the PHP programming language is published','http://www.php.net/license/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('php','en','PHP License','License under which the PHP programming language is published','http://www.php.net/license/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('php','eu','PHP License','PHP hizkuntza programazioaren pean argitaratu den Lizentzia','http://www.php.net/license/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('php','gl','PHP License','Licenza baixo a que se publica a linguaxe de programación PHP','http://www.php.net/license/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('php','it','PHP License','Licenza con cui si pubblica il linguaggio di programmazione PHP','http://www.php.net/license/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('php','pl','PHP License','License under which the PHP programming language is published','http://www.php.net/license/',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('tapr','ca','TAPR Noncommercial Hardware License','Llicència per a obres de maquinari lliure amb limitació en la seva comercialització ','http://www.tapr.org/NCL.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('tapr','el','TAPR Noncommercial Hardware License','TAPR Noncommercial Hardware License','http://www.tapr.org/NCL.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('tapr','en','TAPR Noncommercial Hardware License','TAPR Noncommercial Hardware License','http://www.tapr.org/NCL.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('tapr','eu','TAPR Noncommercial Hardware License','Bere komentzializaziorako limitazioak dituen hardwar libreko obrentzako lizentzia','http://www.tapr.org/NCL.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('tapr','gl','TAPR Noncommercial Hardware License','Licenza para obras de hardware libre con limitación na súa comercialización ','http://www.tapr.org/NCL.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('tapr','it','TAPR Noncommercial Hardware License','Licenza per opere con hardware libero con limitazioni alla sua commercializzazione ','http://www.tapr.org/NCL.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('tapr','pl','TAPR Noncommercial Hardware License','TAPR Noncommercial Hardware License','http://www.tapr.org/NCL.html',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('xoln','ca','Procomú de la XOLN','Llicència de xarxa oberta, lliure i neutral, com a acord d\'interconnexió entre iguals promogut per Guifi.net','http://guifi.net/es/ProcomunXOLN',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('xoln','el','XOLN Common Good License','License for an open, free, neutral network, as an agreement of interconnection among equals, promoted by Guifi.net ','http://guifi.net/es/ProcomunXOLN',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('xoln','en','XOLN Common Good License','License for an open, free, neutral network, as an agreement of interconnection among equals, promoted by Guifi.net ','http://guifi.net/es/ProcomunXOLN',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('xoln','eu','Procomún de la XOLN','Sare irekiaren Lizentzia, libre eta neutrala, berdinen arteko elkar-lotzea Guifi.net-ek sustatutako akordioa bezala','http://guifi.net/es/ProcomunXOLN',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('xoln','gl','Procomún da XOLN','Licenza de rede aberta, libre e neutral, coma acordo de interconexión entre iguais promovido por Guifi.net','http://guifi.net/es/ProcomunXOLN',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('xoln','it','Common della XOLN','Licenza di rete aperta, libera e neutrale, dall\'accordo di connessioni tra uguali promosso da Guifi.net','http://guifi.net/es/ProcomunXOLN',0);
INSERT  INTO `license_lang`(`id`,`lang`,`name`,`description`,`url`,`pending`) VALUES ('xoln','pl','XOLN Common Good License','License for an open, free, neutral network, as an agreement of interconnection among equals, promoted by Guifi.net ','http://guifi.net/es/ProcomunXOLN',0);

/*Data for the table `role` */

INSERT  INTO `role`(`id`,`name`) VALUES ('admin','Administrador');
INSERT  INTO `role`(`id`,`name`) VALUES ('caller','Convocador');
INSERT  INTO `role`(`id`,`name`) VALUES ('checker','Revisor de proyectos');
INSERT  INTO `role`(`id`,`name`) VALUES ('manager','Gestor de contratos');
INSERT  INTO `role`(`id`,`name`) VALUES ('root','ROOT');
INSERT  INTO `role`(`id`,`name`) VALUES ('superadmin','Super administrador');
INSERT  INTO `role`(`id`,`name`) VALUES ('translator','Traductor de contenidos');
INSERT  INTO `role`(`id`,`name`) VALUES ('vip','Padrino');


/*Data for the table `user` */
INSERT INTO `user`(`id`,`name`,`password`,`email`,`active`,`worth`,`created`,`token`,`hide`,`confirmed`,`lang`,`node`) VALUES('root', 'Sysadmin', SHA1('root'),'', 1, 0, NOW(),'',1,1, 'en', 'goteo');

/*Data for the table `user_translang` */
INSERT INTO `user_translang` (`user`, `lang`) VALUES
('root', 'ca'),
('root', 'de'),
('root', 'el'),
('root', 'en'),
('root', 'es'),
('root', 'eu'),
('root', 'fr'),
('root', 'gl'),
('root', 'it'),
('root', 'nl'),
('root', 'pt');

/*Data for the table `user_role` */

INSERT  INTO `user_role`(`user_id`,`role_id`,`node_id`,`datetime`) VALUES ('root','checker',NULL,NULL);
INSERT  INTO `user_role`(`user_id`,`role_id`,`node_id`,`datetime`) VALUES ('root','manager',NULL,NULL);
INSERT  INTO `user_role`(`user_id`,`role_id`,`node_id`,`datetime`) VALUES ('root','root',NULL,NULL);
INSERT  INTO `user_role`(`user_id`,`role_id`,`node_id`,`datetime`) VALUES ('root','superadmin',NULL,NULL);
INSERT  INTO `user_role`(`user_id`,`role_id`,`node_id`,`datetime`) VALUES ('root','translator',NULL,NULL);

/*Data for the table `worthcracy` */

INSERT  INTO `worthcracy`(`id`,`name`,`amount`) VALUES (1,'Fan',25);
INSERT  INTO `worthcracy`(`id`,`name`,`amount`) VALUES (2,'Patrocinador/a',100);
INSERT  INTO `worthcracy`(`id`,`name`,`amount`) VALUES (3,'Apostador/a',500);
INSERT  INTO `worthcracy`(`id`,`name`,`amount`) VALUES (4,'Abonado/a',1000);
INSERT  INTO `worthcracy`(`id`,`name`,`amount`) VALUES (5,'Visionario/a',3000);

/*Data for the table `worthcracy_lang` */

INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (1,'ca','Fan',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (1,'en','Fan',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (1,'eu','Zalea',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (1,'fr','Fan',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (1,'gl','Fan',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (2,'ca','Patrocinador/a',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (2,'en','Member',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (2,'eu','Babeslea',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (2,'fr','Sponsor',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (2,'gl','Patrocinador/a',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (3,'ca','Apostador/a',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (3,'en','Supporter',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (3,'eu','Apostularia',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (3,'fr','Contributeurs',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (3,'gl','Xogador/a',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (4,'ca','Abonat/da',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (4,'en','Patron',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (4,'eu','Abonatua',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (4,'fr','Abonné/e',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (4,'gl','Abonado/a',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (5,'ca','Visionari/a',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (5,'en','Visionary',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (5,'eu','Irudikorra',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (5,'fr','Pionner',0);
INSERT  INTO `worthcracy_lang`(`id`,`lang`,`name`,`pending`) VALUES (5,'gl','Visionario/a',0);

/* PAGES */

-- pages
INSERT INTO `page`(`id`,`name`,`description`,`type`,`url`,`content`) VALUES ('about','',NULL,'html','/about','<p>    SiteName es una red social de financiaci&oacute;n colectiva (aportaciones monetarias) y colaboraci&oacute;n distribuida (servicios, infraestructuras, microtareas y otros recursos). Una plataforma para la inversi&oacute;n de &ldquo;capital riego&rdquo; en proyectos que contribuyan al desarrollo del procom&uacute;n, el c&oacute;digo abierto y/o el conocimiento libre. Una comunidad para apoyar el desarrollo aut&oacute;nomo de iniciativas creativas e innovadoras cuyos fines sean de car&aacute;cter social, cultural, cient&iacute;fico, educativo, tecnol&oacute;gico o ecol&oacute;gico, que generen nuevas oportunidades para la mejora constante de la sociedad.</p><p>  &nbsp;</p>');
insert  into `page`(`id`,`name`,`description`,`type`,`url`,`content`) values ('big-error','Ha ocurrido algo gordo','Contenido genérico para petadas gordas del sistema.','html','/about/fail','<p>  <em><span style=\"font-size:72px;color:red;\"><strong>Argh!!!</strong></span></em></p><p>   <span style=\"font-size:16px;\">&iquest;Pero qu&eacute; ha pasado?</span><br /> Convendr&iacute;a que te pongas en contacto con nosotros y nos lo expliques <a href=\"/contact\">Aqu&iacute;</a></p>');
INSERT INTO `page`(`id`,`name`,`description`,`type`,`url`,`content`) VALUES ('contact','',NULL,'html','/contact','<div class=\"contact-info\" style=\"color: #58595b; width: 360px; font-size: 12px;  padding: 5px; line-height: 16px;\">  <span class=\"intro-tit\" style=\"font-size: 21px; font-weight: bold; line-height: 24px;\">Quiz&aacute;s estos links resuelvan r&aacute;pidamente lo que buscas: </span>    <ul style=\"margin-left: 0;  padding-left: 0;\">      <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/faq\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">FAQ - Preguntas frecuentes</a></li>       <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/glossary\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">El Glosario de la microfinanciaci&oacute;n</a></li>      <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/press\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">Kit de prensa SiteName</a></li>     <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/service/workshop\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">Talleres</a></li>    </ul>   SiteName es una plataforma digital para la financiaci&oacute;n colectiva, colaboraci&oacute;n y distribuci&oacute;n de recursos para el desarrollo de proyectos sociales, culturales, educativos, tecnol&oacute;gicos... que contribuyan al fortalecimiento del procom&uacute;n, el c&oacute;digo abierto y/o el conocimiento libre.</div>');
insert  into `page`(`id`,`name`,`description`,`type`,`url`,`content`) values ('error','La página que buscas no existe','Contenido genérico para las páginas que no existen o que dan error interno.','html','/about/error','<p> <em><span style=\"font-size:72px;color:red;\"><strong>Uops...!</strong></span></em></p><p>  <span style=\"font-size:16px;\">&iquest;Seguro que la url es correcta?</span><br /> Si est&aacute;s seguro, ponte en contacto con nosotros <a href=\"/contact\">Aqu&iacute;</a></p>');
INSERT INTO `page`(`id`,`name`,`description`,`type`,`url`,`content`) VALUES ('howto','Instrucciones para ser productor/a',"4 condiciones y 2 requisitos para proponer un proyecto",'md','/about/howto','Goteo es una plataforma para apoyar proyectos cívicos, éticos y abiertos de ciudadanos, emprendedores, innovadores sociales y creativos cuyos objetivos, formato y/o resultado final ofrezcan, de forma total o significativa, algún tipo de retorno colectivo.

 **Condiciones**

*  Cuando mi proyecto ofrezca recompensas individuales a cambio de aportaciones económicas determinadas, deberé cumplir con el compromiso establecido con la plataforma y mis cofinanciadores en caso de obtener la financiación mínima solicitada.
*  Deberé cumplir igualmente con el compromiso de publicar los retornos colectivos prometidos, enlazándolos posteriormente desde la plataforma Goteo bajo la licencia elegida en el momento de solicitar la financiación, en cumplimiento de un contrato legal con la Fundación Goteo que firmaré en cuanto obtenga la financiación mínima.
*  Solicitaré una cofinanciación mínima para llevar a cabo el proyecto y otra óptima. En ambos casos desglosando una estimación aproximada del tipo de costes previstos y su cuantía.
*  La finalidad del proyecto no es la venta encubierta de productos o servicios ya producidos, ni la financiación de campañas delictivas o que atenten contra la dignidad de las personas.

**Requisitos**

* Soy mayor de 18 años.
*  Dispongo de una cuenta bancaria.

Para obtener más información sobre cualquiera de los puntos anteriores es importante que leas [nuestras FAQ](/faq).');
INSERT INTO `page`(`id`,`name`,`description`,`type`,`url`,`content`) VALUES ('legal','Legal','Legal','html','/about/legal',NULL);
INSERT INTO `page`(`id`,`name`,`description`,`type`,`url`,`content`) VALUES ('maintenance','Maintenance','Maintenance','html','/about/maintenance',NULL);
INSERT INTO `page`(`id`,`name`,`description`,`type`,`url`,`content`) VALUES ('privacy','',NULL,'html','/legal/privacy','');
INSERT INTO `page`(`id`,`name`,`description`,`type`,`url`,`content`) VALUES ('terms','',NULL,'html','/legal/terms','');
insert  into `page`(`id`,`name`,`description`,`type`,`url`,`content`) values ('apikey','Clave api en dashboard','Contenido para que el usuario obtenga una clave api','html','/dashboard/activity/apikey','<h3 style=\"color: red;\">No tienes una clave API</h3><p>Si quieres una, click <a class=\"button\" href=\"/dashboard/activity/apikey/generate\">AQUÍ</a>.</p><hr /><h3>Ya tienes una clave API: %APIKEY%</h3><p>Si quieres cambiarla, click <a class=\"button\" href=\"/dashboard/activity/apikey/generate\">AQUÍ</a>.</p>');
insert  into `page`(`id`,`name`,`description`,`type`,`url`,`content`) values ('dashboard','Bienvenida','Texto de bienvenida en el dashboard','html','/dashboard','<p>   Hola %USER_NAME%,<br /> bienvenido/a a tu panel de usuario/a en Goteo!</p><p>   Desde aqu&iacute; podr&aacute;s administrar la informaci&oacute;n p&uacute;blica de tu perfil y de tu proyecto, a modo de centro de operaciones para dinamizarlo y gestionarlo. Se pueden publicar novedades sobre los avances del proyecto, a&ntilde;adir fotos y v&iacute;deos, clasificar las aportaciones de los/as cofinanciadores/as y gestionar los env&iacute;os de las recompensas individuales.<br /> <br />  Este panel de control tambi&eacute;n permite ver toda la informaci&oacute;n detallada sobre c&oacute;mo evoluciona la recaudaci&oacute;n y los apoyos recibidos, y posteriormente comunicarse con las personas usuarias que hayan cofinanciado el proyecto.</p>');
insert  into `page`(`id`,`name`,`description`,`type`,`url`,`content`) values ('denied','Acceso denegado','Mensaje de error para páginas a las que no se tiene permiso para acceder','html','/about/denied','<p> <em><span style=\"font-size:72px;color:red;\"><strong>Uops...!</strong></span></em></p><p>  <span style=\"font-size:16px;\">&iexcl;No tienes permiso para acceder a esta p&aacute;gina!</span><br />    Si crees que esto es un error, ponte en contacto con nosotros <a href=\"/contact\">Aqu&iacute;</a></p>');

/*Data for the table `page_lang` */
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('big-error','ca','Ha passat alguna cosa grossa','Contingut genèric per petades grosses del sistema.','<p>    <em><span style=\"font-size:72px;color:red;\"><strong>Argh!!!</strong></span></em></p><p>   <span style=\"font-size:16px;\">Qu&egrave; ha passat?</span><br />  Seria convenient que et posis en contacte amb nosaltres i ens ho expliquis <a href=\"/contact\">a</a><a href=\"/contact\">qu&iacute;</a></p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('big-error','en','Something serious has happened.','Generic content for serious system failures.','<p>   <em><span style=\"font-size:72px;color:red;\"><strong>Argh!!!</strong></span></em></p><p>   <span style=\"font-size:16px;\">But... What happened? </span><br /> You should get in touch with us and tell us about it <a href=\"/contact\">Here</a>.</p>',0);

INSERT INTO `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) VALUES ('about','en','',NULL,'<p>    SiteName is a social network for collective financing (monetary donations) and distributed cooperations (services, infrastructures, etc). A platform for investments in projects that cointribute to the common good and are open source and open knowledge. A community for the development of autonomous, creative and innovative projects in the socia, cultural, technical or educational area, and that create new opportunities for the whole of society.</p><p>  &nbsp;</p>',0);
INSERT INTO `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) VALUES ('contact','en','',NULL,'<div class=\"contact-info\" style=\"color: #58595b; width: 360px; font-size: 12px;  padding: 5px; line-height: 16px;\">  <span class=\"intro-tit\" style=\"font-size: 21px; font-weight: bold; line-height: 24px;\">Use these links to quickly find what you are looking for: </span>    <ul style=\"margin-left: 0;  padding-left: 0;\">      <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/faq\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">FAQ - Preguntas frecuentes</a></li>       <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/glossary\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">El Glosario de la microfinanciaci&oacute;n</a></li>      <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/press\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">Kit de prensa SiteName</a></li>     <li style=\"color: #38b5b1;  margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\">          <a href=\"/service/workshop\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">Talleres</a></li>    </ul>   SiteName is a social network for collective financing (monetary donations) and distributed cooperations (services, infrastructures, etc). A platform for investments in projects that cointribute to the common good and are open source and open content. </div>',0);
INSERT INTO `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) VALUES ('howto','en','Instructions for project owners',"4 conditions and 2 requirements before submitting a project","Goteo is a platform for supporting projects created by entrepreneurs, social innovators, and creatives whose goals, format, or final result include, either wholly or in a significant amount, some type of collective benefit.

 **Terms**

*  When my project offers individual rewards in exchange for specific economic contributions, I will fulfill my obligations established with the platform and with my co-financiers after having received the minimum financing requested.
*  I will also fulfill my obligation to publish the promised collective benefits, linking them from the Goteo platform under the license chosen when financing was requested, as a fulfillment of my legal contract with Fundación Fuentes Abiertas.
*  I will request a minimum amount of co-financing to carry out my project, as well as an optimum amount. When I receive the minimum co-financing amount, I will begin production, about which I will file periodic reports, which will allow me to undertake a second round of co-financing en route to the optimum amount.
*  The end goal for the project is not the hidden sale of existing products or services, nor to finance criminal activity or to violate any person's dignity.

**Requirements**

*  I am at least 18 years of age.
*  I have a bank account in Spain or a PayPal account
Note: Those who do not have a bank account in Spain will only be able to use PayPal's payment system.

If you need more information about any of the following points, we recommend that you begin by reading [our FAQ](/faq).",0);
INSERT INTO `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) VALUES ('privacy','en','',NULL,'',0);
INSERT INTO `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) VALUES ('terms','en','',NULL,'',0);

insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('apikey','en','API key on dashboard','Content for users to get an API key','<h3 style=\"color: red;\">   You do not have an API key</h3><p>  If you would like one, click <a class=\"button\" href=\"/dashboard/activity/apikey/generate\">HERE.</a></p><hr /><h3>   You already have an API key: %APIKEY%</h3><p>   If you would like to change it, click <a class=\"button\" href=\"/dashboard/activity/apikey/generate\">HERE.</a></p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('apikey','it','Chiave api nel dashboard','Contenuto per l\'ottenimento di chiave api da parte dell\'utente ','<h3 style=\"color: red;\"> Non hai chiave API</h3><p>  Se hai bisogno di una, click&nbsp;<a class=\"button\" href=\"/dashboard/activity/apikey/generate\">QU&Iacute;</a>.</p><hr /><h3>    Hai gi&agrave; una chiave API: %APIKEY%</h3><p> Se vuoi &nbsp;cambiarla, click <a class=\"button\" href=\"/dashboard/activity/apikey/generate\">QU&Iacute;</a>.</p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('dashboard','ca','Benvinguda','Text de benvinguda al dashboard','<p> Hola %USER_NAME%,</p><div>  benvingut/da al teu panell d&#39;usuari/a a Goteo!&nbsp;</div><div> &nbsp;</div><div>   Des d&#39;aqu&iacute; podr&agrave;s administrar la informaci&oacute; p&uacute;blica del teu perfil i del teu projecte, a manera de centre d&#39;operacions per a dinamitzar i gestionar-lo. Es poden publicar novetats sobre els aven&ccedil;os del projecte, afegir fotos i v&iacute;deos, classificar les aportacions de la gent que et cofinan&ccedil;a i gestionar els enviaments de les recompenses individuals.&nbsp;</div><div>  &nbsp;</div><div>   Aquest panell de control tamb&eacute; permet veure tota la informaci&oacute; detallada sobre com evoluciona la recaptaci&oacute; i els suports rebuts, i posteriorment comunicar-se amb la gent usu&agrave;ria que hagi cofinan&ccedil;at el projecte.</div>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('dashboard','en','Welcome','Texto de bienvenida en el dashboard','<p>    Hello %USER_NAME%,<br />    welcome to your dashboard.</p><p>   This is your center of operations, where you manage your public information in your profile and your project. You can publish news about how your project is progressing, you can add photos and videos, and you can organize the contributions of your co-financiers and then manage the shipments of the individual rewards.</p><p>   This dashboard also lets you see detailed information about how your financing and support is progressing, and later communicate with those who are co-financing your project.</p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('dashboard','eu','Ongietorria','Ongietorriko hitzak dashboard-en','<p>   Kaixo %USER_NAME%,<br />    ongi etorri Goteoko zure erabiltzaile panelera!</p><p>  Hemendik zure profilari eta zure proiektuari buruzko informazio publikoa administratu ahalko duzu, hura dinamizatzeko ea kudeatzeko eragiketa-zentro gisa. Proiektuaren aurrerapenei buruzko berritasunak argitara daitezke, argazki eta bideoak erants daitezke, kofinantziatzaileen ekarpenak sailka daitezke eta banakako sarien bidalketak kudea daitezke.<br />    <br />  Kontroleko panel honek aukera ematen du, halaber, diru-bilketaren bilakaerari eta jasotako laguntzei buruzko informazio xehea edukitzeko eta gero proiektua kofinantziatu duten erabiltzaileekin komunikatzeko.</p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('dashboard','fr','Bienvenue','Texte de bienvenue sur le tableau de bord','<p>    Bonjour %USER_NAME%,<br />  Bienvenu(e) sur ton tableau de bord de Goteo</p><p> Depuis cette interface, vous pourrez administrer les infos publiques de votre profil et de votre projet, ainsi que dynamiser votre activit&eacute;. Vous pouvez publier des nouveaut&eacute;s sur l&#39;avancement du projet, ajouter des photos et vid&eacute;os, classer et g&eacute;rer les apports des cofinanceurs et g&eacute;rer les envois des r&eacute;compenses individuelles.</p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('dashboard','it','Accoglienza','Messaggio di benvenuto nel dashboard','<p>   Ciao %USER_NAME%,<br /> Benvenuto al panel utente di Goteo!</p><p>  Questo &egrave; il centro operativo da cui potrai gestire le informazioni relative al tuo profilo e al tuo progetto. Si possono pubblicare novit&agrave; sugli sviluppi del progetto, aggiungere foto e video, classificare i contributi dei co-finanziatori e gestire le spedizioni delle ricompense individuali.</p><p>   Questo panel di controllo permette anche visualizzare tutta l&#39;informazione dettagliata sulle evoluzione della raccolta, degli aiuti ricevuti e quindi poter comunicare, in un secondo momento, con gli utenti che hanno co-finanziato il progetto.&nbsp;</p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('denied','en','Access denied','Error message for pages that users do not have permission to access','<p> <em><span style=\"font-size:72px;color:red;\"><strong>Oops...!</strong></span></em></p><p>  <span style=\"font-size:16px;\">You don&#39;t have permission to access this page!</span><br /> If you think there&#39;s been an error, get in touch with us <a href=\"/contact\">here</a></p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('error','ca','La pàgina que estàs cercant no existeix','Contingut genèric per a les pàgines que no existeixen o que donen error intern.','<p>    <em><span style=\"font-size:72px;color:red;\"><strong>Uops...!</strong></span></em></p><p>  <span style=\"font-size:16px;\">Segur que l&#39;url &eacute;s correcta?</span><br />    Si est&agrave;s segur/a, posa&#39;t en contacte amb nosaltres <a href=\"/contact\">a</a><a href=\"/contact\">qu&iacute;</a></p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('error','en','The page you\'re looking for doesn\'t exist','Generic content for pages that don\'t exist or return an internal error.','<p>   <em><span style=\"font-size:72px;color:red;\"><strong>Ooops...!</strong></span></em></p><p> <span style=\"font-size:16px;\">Are you sure this is the correct URL? </span><br /> If this is the case, get in touch with us <a href=\"/contact\">Here</a>.</p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('error','eu','Bilatzen zaude web orrialdea ez da existitzen','Ez diren existitzen web orrialde edo barne akatzak ematen dutenentzako eduki generikoa','<p>   <em><span style=\"font-size:72px;color:red;\"><strong>Uops...!</strong></span></em></p><p>  <span style=\"font-size:16px;\">Zihur zaude url-a zuzena dela?</span><br /> Zihur bazaude, <a href=\"/contact\">hemen</a> jar zaitez gurekin kontaktuan.</p>',0);
insert  into `page_lang`(`id`,`lang`,`name`,`description`,`content`,`pending`) values ('error','fr','La page que vous cherchez n\'existe pas','Contenu générique pour les pages qui n\'existent pas ou qui génèrent une erreur interne.','<p>   <em><span style=\"font-size:72px;color:red;\"><strong>Oops...!</strong></span></em></p><p>  <span style=\"font-size:16px;\">Etes vous sur que l&#39;URL est correcte?</span><br />  Si vous ne trouvez pas ce que vous cherchez, contactez-nous <a href=\"/contact\">ici</a></p>',0);

/* Project test */


-- Project passing first today


-- Owner

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`, `token`) VALUES
('owner-project-passing', 'Owner project passing', 'owner-project-passing@example.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Owner project passing', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW(), '');


-- Backers

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`, `token`) VALUES
('backer-1-passing-project', 'Backer 1 passing project', 'backer-1-passing-project@example.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 1 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW(), '');

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`, `token`) VALUES
('backer-2-passing-project', 'Backer 2 passing project', 'backer-2-passing-project@example.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 2 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW(), '');

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`, `token`) VALUES
('backer-3-passing-project', 'Backer 3 passing project', 'backer-3-passing-project@example.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 3 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW(), '');

INSERT INTO `user` (`id`, `name`, `email`, `password`, `about`, `keywords`, `active`, `avatar`, `contribution`, `twitter`, `facebook`, `linkedin`, `worth`, `created`, `modified`, `token`) VALUES
('backer-4-passing-project', 'Backer 4 passing project', 'backer-4-passing-project@example.org', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Backer 4 passing project', NULL, 1, 0, 'mucho arte', '@owner', 'feisbuc.com', 'ein?', NULL, NOW() , NOW(), '');



-- Project
INSERT INTO `project` (`id`,
  `name`,
  `subtitle`,
  `lang`,
  `currency`,
  `currency_rate`,
  `status`,
  `translate`,
  `progress`,
  `owner`,
  `node`,
  `amount`,
  `mincost`,
  `maxcost`,
  `days`,
  `num_investors`,
  `popularity`,
  `num_messengers`,
  `num_posts`,
  `created`,
  `updated`,
  `published`,
  `success`,
  `closed`,
  `passed`,
  `contract_name`,
  `contract_nif`,
  `phone`,
  `contract_email`,
  `address`,
  `zipcode`,
  `location`,
  `country`,
  `image`,
  `description`,
  `video`,
  `project_location`
 ) VALUES (
 'project-passing-today',
 'Project passing today',
 'Description Project passing today',
 'es',
 'EUR',
 1.00000,
 3,
 1,
 110,
 'owner-project-passing',
 'goteo',
 220,
 200,
 400,
 41,
 2,
 0,
 0,
 0,
 NOW()-INTERVAL 45 DAY,
 NOW()-INTERVAL 39 DAY,
 NOW()-INTERVAL 39 DAY,
 NULL,
 NULL,
 NULL,
 'User testing',
 '00000000-N',
 '00340000000000',
 'tester@example.org',
 'Dir tester',
 '00000',
 'Barcelona',
 'España',
 '7_10.jpg',
 'Testing project',
 'https://www.youtube.com/watch?v=3On4rAJdeKg',
 'City, country');


-- Invests

INSERT INTO `invest` (`user`, `project`, `account`, `amount`, `amount_original`, `currency`, `currency_rate`, `status`, `anonymous`, `resign`, `invested`, `charged`, `returned`, `preapproval`, `payment`, `transaction`, `method`, `admin`, `campaign`, `datetime`, `drops`, `droped`, `call`, `issue`, `pool`) VALUES
('backer-1-passing-project', 'project-passing-today', '', 200, 200, 'EUR', 1.00000, 1, NULL, 1, NOW()-INTERVAL 35 DAY, NOW()-INTERVAL 35 DAY, NULL, NULL, '', NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 60 DAY, NULL, NULL, NULL, NULL, 1),
('backer-2-passing-project', 'project-passing-today', '', 40, 40, 'EUR', 1.00000, 1, NULL, NULL, NOW()-INTERVAL 30 DAY, NOW()-INTERVAL 30 DAY, NULL, '', NULL, NULL, 'dummy', NULL, NULL, NOW()-INTERVAL 70 DAY, NULL, NULL, NULL, NULL, NULL);


INSERT INTO `promote` (`node`, `project`, `active`) VALUES ('goteo', 'project-passing-today', 1);

