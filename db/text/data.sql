-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 06-05-2011 a las 13:18:24
-- Versión del servidor: 5.1.49
-- Versión de PHP: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `goteo`
--

--
-- Volcar la base de datos para la tabla `text`
--

INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-email', 'es', 'La direcciÃ³n de correo es obligatoria.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-email-confirm', 'es', 'La comprobaciÃ³n de email no coincide.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-email-exists', 'es', 'El direcciÃ³n de correo ya corresponde a un usuario registrado.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-password-confirm', 'es', 'La comprobaciÃ³n de contraseÃ±a no coincide.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-pasword', 'es', 'La contraseÃ±a no puede estar vacÃ­a.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-short-password', 'es', 'La contraseÃ±a debe contener un mÃ­nimo de 8 caracteres.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-user-exists', 'es', 'El usuario ya existe.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('error-register-username', 'es', 'El nombre de usuario usuario es obligatorio.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('explain-project-progress', 'es', 'Texto bajo el tÃ­tulo Estado global de la informaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-comment', 'es', 'Escribe aquÃ­ tus comentarios para el revisor de proyectos.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-contract-information', 'es', 'Texto guÃ­a en el paso DATOS PERSONALES del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-costs', 'es', 'Texto guÃ­a en el paso COSTES del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-description', 'es', 'Texto guÃ­a en el paso DESCRIPCIÃ“N del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-error-mandatories', 'es', 'Faltan campos obligatorios');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-overview', 'es', 'Texto guÃ­a en el paso PREVISUALIZACIÃ“N del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-preview', 'es', 'Puedes repasar los puntos marcados en rojo y mejorar el porcentaje o enviar el\r\ndefinitivamente el proyecto para ser valorado por el equipo Goteo.\r\nRecibirÃ¡s una comunicaciÃ³n con toda la informaciÃ³n e indicarÃ¡ los pasos a seguir y\r\nrecomendaciones para que tu proyecto pueda alcanzar exitosamente la meta\r\npropuesta.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-rewards', 'es', 'Texto guÃ­a en el paso RETORNO del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-success-minprogress', 'es', 'Ha llegado al porcentaje mÃ­nimo');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-success-noerrors', 'es', 'Todos los campos obligatorios estan rellenados');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-success-okfinish', 'es', 'Puede enviar para revisiÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-support', 'es', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-supports', 'es', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-project-user-information', 'es', 'Texto guÃ­a en el paso PERFIL del formulario de proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-user-data', 'es', 'Texto guÃ­a en la ediciÃ³n de campos sensibles.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-user-information', 'es', 'Texto guÃ­a en la ediciÃ³n de informaciÃ³n del usuario.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('guide-user-register', 'es', 'Texto guÃ­a en el registro de un nuevo usuario.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-cost-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-cost-field-name', 'es', 'Es obligatorio ponerle un nombre al coste');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-individual_reward-field-amount', 'es', 'Es obligatorio indicar el importe que otorga la recompensa');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-individual_reward-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-individual_reward-field-name', 'es', 'Es obligatorio poner la recompensa');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-costs', 'es', 'Debe desglosar en al menos dos costes.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-about', 'es', 'Es obligatorio explicar quÃ© es en la descripciÃ³n del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-address', 'es', 'La direcciÃ³n del responsable del proyecto es obligatoria');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-category', 'es', 'Es obligatorio elegir al menos una categoria para el proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-contract-email', 'es', 'Es obligatorio poner el email del responsable del proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-contract-name', 'es', 'Es obligatorio poner el nombre del responsable del proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-contract-nif', 'es', 'Es obligatorio poner el documento de identificacciÃ³n del responsable del proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-contract-surname', 'es', 'Es obligatorio poner los apellidos del responsable del proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-country', 'es', 'El paÃ­s del responsable del proyecto es obligatorio');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-description', 'es', 'Es obligatorio poner una descripciÃ³n al proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-goal', 'es', 'Es obligatorio explicar los objetivos en la descripciÃ³n del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-image', 'es', 'Es obligatorio poner una imagen al proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-location', 'es', 'Es obligatorio poner la localizaciÃ³n donde se llevarÃ¡ a cabo el proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-media', 'es', 'Poner un vÃ­deo para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-motivation', 'es', 'Es obligatorio explicar la motivaciÃ³n en la descripciÃ³n del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-name', 'es', 'Es obligatorio poner un NOMBRE al proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-phone', 'es', 'El telÃ©fono del responsable del proyecto es obligatorio');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-related', 'es', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripciÃ³n del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-residence', 'es', 'Es obligatorio poner el lugar de residencia del responsable del proyecto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-resource', 'es', 'Es obligatorio especificar si cuentas con otros recursos');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-project-field-zipcode', 'es', 'El cÃ³digo postal del responsable del proyecto es obligatorio');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-social_reward-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n al retorno');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-social_reward-field-name', 'es', 'Es obligatorio poner el retorno');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-support-field-description', 'es', 'Es obligatorio poner alguna descripciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('mandatory-support-field-name', 'es', 'Es obligatorio ponerle un nombre a la colaboraciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('regular-mandatory', 'es', 'Campo obligatorio!');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-1', 'es', 'Perfil');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-2', 'es', 'Datos personales');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-3', 'es', 'DescripciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-4', 'es', 'Costes');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-5', 'es', 'Retorno');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-6', 'es', 'Colaboraciones');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-7', 'es', 'PrevisualizaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-costs', 'es', 'Proyecto / Costes');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-overview', 'es', 'Proyecto / DescripciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-preview', 'es', 'Proyecto / PrevisualizaciÃ­on');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-rewards', 'es', 'Proyecto / Retornos');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-supports', 'es', 'Proyecto / Colaboraciones');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-userPersonal', 'es', 'Usuario / Datos personales');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('step-userProfile', 'es', 'Usuario / Perfil');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('subject-change-email', 'es', 'subject-change-email');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-individual_reward-social_reward-icon', 'es', 'tooltip-individual_reward-social_reward-icon');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-about', 'es', 'Consejo para rellenar el campo quÃ© es');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-address', 'es', 'Consejo para rellenar el address del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-category', 'es', 'Consejo para seleccionar la categorÃ­a del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-contract_email', 'es', 'Consejo para rellenar el email del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-contract_name', 'es', 'Consejo para rellenar el nombre del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-contract_nif', 'es', 'Consejo para rellenar el nif del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-contract_surname', 'es', 'Consejo para rellenar el apellido del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost', 'es', 'Consejo para editar desgloses existentes');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-amount', 'es', 'tooltip-project-cost-amount');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-cost', 'es', 'tooltip-project-cost-cost');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-dates', 'es', 'tooltip-project-cost-dates');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-description', 'es', 'tooltip-project-cost-description');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-from', 'es', 'tooltip-project-cost-from');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-required', 'es', 'tooltip-project-cost-required');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-type', 'es', 'tooltip-project-cost-type');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-cost-until', 'es', 'tooltip-project-cost-until');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-costs', 'es', 'tooltip-project-costs');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-country', 'es', 'Consejo para rellenar el paÃ­s del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-currently', 'es', 'Consejo para rellenar el estado de desarrollo del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-description', 'es', 'Describe el proyecto con un mÃ­nimo de 150 palabras, menos palabras te marcara error.\r\nDescribelo de manera que sea facil de entender para cualquier persona. Intenta darle un enfoque atractivo y social. No escribas un texto demasiado largo en este campo, si lo haces la gente no leerÃ¡ el resto de informaciÃ³n.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-goal', 'es', 'Consejo para rellenar el campo objetivos');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-image', 'es', 'Consejo para rellenar la imagen del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-individual_reward', 'es', 'Consejo para editar retornos individuales existentes');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-individual_reward-amount', 'es', 'tooltip-project-individual_reward-amount');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-individual_reward-description', 'es', 'tooltip-project-individual_reward-description');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-individual_reward-reward', 'es', 'tooltip-project-individual_reward-reward');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-individual_reward-units', 'es', 'tooltip-project-individual_reward-units');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-individual_rewards', 'es', 'tooltip-project-individual_rewards');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-keywords', 'es', 'Consejo para rellenar las palabras clave del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-location', 'es', 'Consejo para rellenar el lugar de residencia del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-media', 'es', 'Consejo para rellenar el media del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-motivation', 'es', 'Consejo para rellenar el campo motivaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-name', 'es', 'Consejo para rellenar el nombre del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-ncost', 'es', 'Consejo para rellenar un nuevo desglose de costes');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-nindividual_reward', 'es', 'Consejo para rellenar un nuevo retorno individual');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-nsocial_reward', 'es', 'Consejo para rellenar un nuevo retorno colectivo');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-nsupport', 'es', 'Consejo para rellenar una nueva colaboraciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-phone', 'es', 'Consejo para rellenar el telÃ©fono del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-project_location', 'es', 'Consejo para rellenar la localizaciÃ³n del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-related', 'es', 'Consejo para rellenar el campo experiencia relacionada y equipo');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-resource', 'es', 'Indica si tienes otras fuentes de financiaciÃ³n , recursos propios o ya has hecho acopio de materiales. Si no cuentas absolutamente con ningÃºn recurso, indica como conseguirÃ¡s el resto de la financiaciÃ³n.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-schedule', 'es', 'tooltip-project-schedule');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-social_reward', 'es', 'Consejo para editar retornos colectivos existentes');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-social_reward-description', 'es', 'tooltip-project-social_reward-description');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-social_reward-icon', 'es', 'tooltip-project-social_reward-icon');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-social_reward-license', 'es', 'tooltip-project-social_reward-license');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-social_reward-reward', 'es', 'tooltip-project-social_reward-reward');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-social_rewards', 'es', 'tooltip-project-social_rewards');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-support', 'es', 'Consejo para editar colaboraciones existentes');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-support-description', 'es', 'tooltip-project-support-description');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-support-support', 'es', 'tooltip-project-support-support');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-support-type', 'es', 'tooltip-project-support-type');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-supports', 'es', 'tooltip-project-supports');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-totals', 'es', 'tooltip-project-totals');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-project-zipcode', 'es', 'Consejo para rellenar el zipcode del responsable del proyecto');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-about', 'es', 'Consejo para rellenar el cuÃ©ntanos algo sobre tÃ­');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-avatar_upload', 'es', 'tooltip-user-avatar_upload');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-blog', 'es', 'Consejo para rellenar la web');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-contribution', 'es', 'Consejo para rellenar el quÃ© podrÃ­as aportar en goteo');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-email', 'es', 'Consejo para rellenar el email de registro de usuario');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-facebook', 'es', 'Consejo para rellenar el facebook');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-image', 'es', 'Consejo para rellenar la imagen del usuario');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-interests', 'es', 'Consejo para seleccionar tus intereses');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-keywords', 'es', 'Consejo para rellenar tus palabras clave');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-linkedin', 'es', 'Consejo para rellenar el linkedin');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-name', 'es', 'Consejo para rellenar el nombre completo del usuario');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-twitter', 'es', 'Consejo para rellenar el twitter');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-user', 'es', 'Consejo para rellenar el nombre de usuario para login');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('tooltip-user-webs', 'es', 'tooltip-user-webs');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-cost-field-dates', 'es', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-field-costs', 'es', 'Desglosar hasta 5 costes para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-field-currently', 'es', 'Indicar el estado del proyecto para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-individual_rewards', 'es', 'Indicar hasta 5 recompensas individuales para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-social_rewards', 'es', 'Indicar hasta 5 retornos colectivos para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-total-costs', 'es', 'El coste Ã³ptimo no puede superar en mÃ¡s de un 40% al coste mÃ­nimo. Revisar el DESGLOSE DE COSTES.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-value-contract-email', 'es', 'El EMAIL no es correcto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-value-contract-nif', 'es', 'El NIF no es correcto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-value-description', 'es', 'La DESCRIPCIÃ“N del proyecto es demasiado corta.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-value-keywords', 'es', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-project-value-phone', 'es', 'El TELÃ‰FONO no es correcto.');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-social_reward-license', 'es', 'Indicar una licencia para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-about', 'es', 'Cuenta algo sobre ti para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-avatar', 'es', 'Pon una imagen de perfil para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-contribution', 'es', 'Explica que podrias aportar en Goteo para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-facebook', 'es', 'Pon tu cuenta de facebook para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-interests', 'es', 'Selecciona algÃºn interÃ©s para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-keywords', 'es', 'Indica hasta 5 palabras clave que te definan para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-linkedin', 'es', 'validate-user-field-linkedin');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-name', 'es', 'Pon tu nombre completo para mejorar la puntuaciÃ³n');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-twitter', 'es', 'validate-user-field-twitter');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES('validate-user-field-webs', 'es', 'Pon tu pÃ¡gina web para mejorar la puntuaciÃ³n');
