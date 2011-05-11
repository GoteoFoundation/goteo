-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 06-05-2011 a las 13:17:51
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
-- Volcar la base de datos para la tabla `purpose`
--

INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-email', 'La direcciÃ³n de correo es obligatoria.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-email-confirm', 'La comprobaciÃ³n de email no coincide.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-email-exists', 'El direcciÃ³n de correo ya corresponde a un usuario registrado.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-password-confirm', 'La comprobaciÃ³n de contraseÃ±a no coincide.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-pasword', 'La contraseÃ±a no puede estar vacÃ­a.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-short-password', 'La contraseÃ±a debe contener un mÃ­nimo de 8 caracteres.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-user-exists', 'El usuario ya existe.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('error-register-username', 'El nombre de usuario usuario es obligatorio.', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('explain-project-progress', 'Texto bajo el tÃ­tulo Estado global de la informaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-comment', 'Texto guide-project-comment', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-contract-information', 'Texto guÃ­a en el paso DATOS PERSONALES del formulario de proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-costs', 'Texto guÃ­a en el paso COSTES del formulario de proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-description', 'Texto guÃ­a en el paso DESCRIPCIÃ“N del formulario de proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-error-mandatories', 'Faltan campos obligatorios', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-overview', 'Texto guÃ­a en el paso PREVISUALIZACIÃ“N del formulario de proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-preview', 'Texto guide-project-preview', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-rewards', 'Texto guÃ­a en el paso RETORNO del formulario de proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-success-minprogress', 'Ha llegado al porcentaje mÃ­nimo', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-success-noerrors', 'Todos los campos obligatorios estan rellenados', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-success-okfinish', 'Puede enviar para valoraciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-support', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-supports', 'Texto guide-project-supports', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-project-user-information', 'Texto guÃ­a en el paso PERFIL del formulario de proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-user-data', 'Texto guÃ­a en la ediciÃ³n de datos sensibles del usuario', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-user-information', 'Texto guÃ­a en la ediciÃ³n de informaciÃ³n del usuario', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('guide-user-register', 'Texto guÃ­a en el registro de usuario', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-cost-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-cost-field-name', 'Es obligatorio ponerle un nombre al coste', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-individual_reward-field-amount', 'Es obligatorio indicar el importe que otorga la recompensa', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-individual_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-individual_reward-field-name', 'Es obligatorio poner la recompensa', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-costs', 'MÃ­nimo de costes a desglosar en un proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-about', 'Es obligatorio explicar quÃ© es en la descripciÃ³n del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-address', 'La direcciÃ³n del responsable del proyecto es obligatoria', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-category', 'La categorÃ­a del proyecto es obligatoria', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-contract-email', 'El email del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-contract-name', 'El nombre del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-contract-nif', 'El nif del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-contract-surname', 'El apellido del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-country', 'El paÃ­s del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-description', 'La descripciÃ³n del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-goal', 'Es obligatorio explicar los objetivos en la descripciÃ³n del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-image', 'Es obligatorio poner una imagen al proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-location', 'La localizaciÃ³n del proyecto es obligatoria', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-media', 'Poner un vÃ­deo para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-motivation', 'Es obligatorio explicar la motivaciÃ³n en la descripciÃ³n del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-name', 'El nombre del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-phone', 'El telÃ©fono del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-related', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripciÃ³n del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-residence', 'El lugar de residencia del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-resource', 'Es obligatorio especificar si cuentas con otros recursos', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-project-field-zipcode', 'El cÃ³digo postal del responsable del proyecto es obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-social_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n al retorno', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-social_reward-field-name', 'Es obligatorio poner el retorno', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-support-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('mandatory-support-field-name', 'Es obligatorio ponerle un nombre a la colaboraciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('regular-mandatory', 'Texto genÃ©rico para indicar campo obligatorio', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-1', 'Perfil', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-2', 'Datos personales', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-3', 'DescripciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-4', 'Costes', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-5', 'Retorno', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-6', 'Colaboraciones', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-7', 'PrevisualizaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-costs', 'Paso 4, desglose de costes', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-overview', 'Paso 3, descripciÃ³n del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-preview', 'paso 7, previsualizaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-rewards', 'Paso 5, retornos', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-supports', 'Paso 6, colaboraciones', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-userPersonal', 'Paso 2, informaciÃ³n del responsable', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('step-userProfile', 'Paso 1, informaciÃ³n del usuario', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('subject-change-email', 'Texto subject-change-email', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-individual_reward-social_reward-icon', 'Texto tooltip-individual_reward-social_reward-icon', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-about', 'Consejo para rellenar el campo quÃ© es', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-address', 'Consejo para rellenar el address del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-category', 'Consejo para seleccionar la categorÃ­a del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-contract_email', 'Consejo para rellenar el email del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-contract_name', 'Consejo para rellenar el nombre del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-contract_nif', 'Consejo para rellenar el nif del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-contract_surname', 'Consejo para rellenar el apellido del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost', 'Consejo para editar desgloses existentes', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-amount', 'Texto tooltip-project-cost-amount', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-cost', 'Texto tooltip-project-cost-cost', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-dates', 'Texto tooltip-project-cost-dates', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-description', 'Texto tooltip-project-cost-description', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-from', 'Texto tooltip-project-cost-from', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-required', 'Texto tooltip-project-cost-required', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-type', 'Texto tooltip-project-cost-type', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-cost-until', 'Texto tooltip-project-cost-until', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-costs', 'Texto tooltip-project-costs', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-country', 'Consejo para rellenar el paÃ­s del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-currently', 'Consejo para rellenar el estado de desarrollo del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-description', 'Consejo para rellenar la descripciÃ³n del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-goal', 'Consejo para rellenar el campo objetivos', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-image', 'Consejo para rellenar la imagen del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-individual_reward', 'Consejo para editar retornos individuales existentes', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-individual_reward-amount', 'Texto tooltip-project-individual_reward-amount', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-individual_reward-description', 'Texto tooltip-project-individual_reward-description', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-individual_reward-reward', 'Texto tooltip-project-individual_reward-reward', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-individual_reward-units', 'Texto tooltip-project-individual_reward-units', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-individual_rewards', 'Texto tooltip-project-individual_rewards', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-keywords', 'Consejo para rellenar las palabras clave del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-location', 'Consejo para rellenar el lugar de residencia del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-media', 'Consejo para rellenar el media del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-motivation', 'Consejo para rellenar el campo motivaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-name', 'Consejo para rellenar el nombre del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-ncost', 'Consejo para rellenar un nuevo desglose de costes', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-nindividual_reward', 'Consejo para rellenar un nuevo retorno individual', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-nsocial_reward', 'Consejo para rellenar un nuevo retorno colectivo', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-nsupport', 'Consejo para rellenar una nueva colaboraciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-phone', 'Consejo para rellenar el telÃ©fono del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-project_location', 'Consejo para rellenar la localizaciÃ³n del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-related', 'Consejo para rellenar el campo experiencia relacionada y equipo', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-resource', 'Consejo para rellenar el campo Cuenta con otros recursos?', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-schedule', 'Texto tooltip-project-schedule', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-social_reward', 'Consejo para editar retornos colectivos existentes', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-social_reward-description', 'Texto tooltip-project-social_reward-description', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-social_reward-icon', 'Texto tooltip-project-social_reward-icon', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-social_reward-license', 'Texto tooltip-project-social_reward-license', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-social_reward-reward', 'Texto tooltip-project-social_reward-reward', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-social_rewards', 'Texto tooltip-project-social_rewards', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-support', 'Consejo para editar colaboraciones existentes', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-support-description', 'Texto tooltip-project-support-description', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-support-support', 'Texto tooltip-project-support-support', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-support-type', 'Texto tooltip-project-support-type', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-supports', 'Texto tooltip-project-supports', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-totals', 'Texto tooltip-project-totals', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-project-zipcode', 'Consejo para rellenar el zipcode del responsable del proyecto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-about', 'Consejo para rellenar el cuÃ©ntanos algo sobre tÃ­', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-avatar_upload', 'Texto tooltip-user-avatar_upload', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-blog', 'Consejo para rellenar la web', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-contribution', 'Consejo para rellenar el quÃ© podrÃ­as aportar en goteo', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-email', 'Consejo para rellenar el email de registro de usuario', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-facebook', 'Consejo para rellenar el facebook', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-image', 'Consejo para rellenar la imagen del usuario', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-interests', 'Consejo para seleccionar tus intereses', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-keywords', 'Consejo para rellenar tus palabras clave', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-linkedin', 'Consejo para rellenar el linkedin', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-name', 'Consejo para rellenar el nombre completo del usuario', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-twitter', 'Consejo para rellenar el twitter', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-user', 'Consejo para rellenar el nombre de usuario para login', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('tooltip-user-webs', 'Texto tooltip-user-webs', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-cost-field-dates', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-field-costs', 'Desglosar hasta 5 costes para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-field-currently', 'Indicar el estado del proyecto para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-individual_rewards', 'Indicar hasta 5 recompensas individuales para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-social_rewards', 'Indicar hasta 5 retornos colectivos para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-total-costs', 'El coste Ã³ptimo no puede exceder demasiado al coste mÃ­nimo', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-value-contract-email', 'El email no es correcto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-value-contract-nif', 'El nif del responsable del proyecto debe ser correcto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-value-description', 'La descripciÃ³n del proyecto debe se suficientemente extensa', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-value-keywords', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-project-value-phone', 'El telÃ©fono debe ser correcto', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-social_reward-license', 'Indicar una licencia para mejorar la puntuaciÃ³n', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-about', 'Si no ha puesto nada sobre el/ella ', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-avatar', 'Si no ha puesto una imagen de perfil', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-contribution', 'Si no ha puesto quÃ© puede aportar a Goteo', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-facebook', 'Si no ha puesto su cuenta de facebook', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-interests', 'Si no ha seleccionado ningÃºn interÃ©s', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-keywords', 'Si no ha puesto ninguna palabra clave', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-linkedin', 'Texto validate-user-field-linkedin', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-name', 'Si no ha puesto el nombre completo', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-twitter', 'Texto validate-user-field-twitter', NULL);
INSERT INTO `purpose` (`text`, `purpose`, `html`) VALUES('validate-user-field-webs', 'Si no ha puesto ninguna web', NULL);
