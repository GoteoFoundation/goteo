-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 02-06-2011 a las 01:38:10
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

INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-image-type-not-allowed', 'Texto tipos de imagen permitidos', NULL, 'general');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-email', 'La direcciÃ³n de correo es obligatoria.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-email-confirm', 'La comprobaciÃ³n de email no coincide.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-email-exists', 'El direcciÃ³n de correo ya corresponde a un usuario registrado.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-invalid-password', 'La contraseÃ±a no es valida.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-password-confirm', 'La comprobaciÃ³n de contraseÃ±a no coincide.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-pasword', 'La contraseÃ±a no puede estar vacÃ­a.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-short-password', 'La contraseÃ±a debe contener un mÃ­nimo de 8 caracteres.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-user-exists', 'El usuario ya existe.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('error-register-username', 'El nombre de usuario usuario es obligatorio.', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('explain-project-progress', 'Texto bajo el tÃ­tulo Estado global de la informaciÃ³n', NULL, 'general');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-contract-information', 'Texto guÃ­a en el paso DATOS PERSONALES del formulario de proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-costs', 'Texto guÃ­a en el paso COSTES del formulario de proyecto', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-error-mandatories', 'Faltan campos obligatorios', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-overview', 'Texto guÃ­a en el paso DESCRIPCIÃ“N del formulario de proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-preview', 'Texto guÃ­a en el paso PREVISUALIZACIÃ“N del formulario de proyecto', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-rewards', 'Texto guÃ­a en el paso RETORNO del formulario de proyecto', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-success-minprogress', 'Ha llegado al porcentaje mÃ­nimo', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-success-noerrors', 'Todos los campos obligatorios estan rellenados', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-success-okfinish', 'Puede enviar para valoraciÃ³n', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-supports', 'Texto guÃ­a en el paso COLABORACIONES del formulario de proyecto', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-project-user-information', 'Texto guÃ­a en el paso PERFIL del formulario de proyecto', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-user-data', 'Texto guÃ­a en la ediciÃ³n de datos sensibles del usuario', NULL, 'dashboard');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-user-information', 'Texto guÃ­a en la ediciÃ³n de informaciÃ³n del usuario', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('guide-user-register', 'Texto guÃ­a en el registro de usuario', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-cost-field-amount', 'Texto obligatorio cantidad', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-cost-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-cost-field-name', 'Es obligatorio ponerle un nombre al coste', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-individual_reward-field-amount', 'Es obligatorio indicar el importe que otorga la recompensa', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-individual_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-individual_reward-field-name', 'Es obligatorio poner la recompensa', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-costs', 'MÃ­nimo de costes a desglosar en un proyecto', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-about', 'Es obligatorio explicar quÃ© es en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-address', 'La direcciÃ³n del responsable del proyecto es obligatoria', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-category', 'La categorÃ­a del proyecto es obligatoria', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-contract-email', 'El email del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-contract-name', 'El nombre del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-contract-nif', 'El nif del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-contract-surname', 'El apellido del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-country', 'El paÃ­s del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-description', 'La descripciÃ³n del proyecto es obligatorio', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-goal', 'Es obligatorio explicar los objetivos en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-image', 'Es obligatorio poner una imagen al proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-location', 'La localizaciÃ³n del proyecto es obligatoria', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-media', 'Poner un vÃ­deo para mejorar la puntuaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-motivation', 'Es obligatorio explicar la motivaciÃ³n en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-name', 'El nombre del proyecto es obligatorio', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-phone', 'El telÃ©fono del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-related', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-residence', 'El lugar de residencia del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-resource', 'Es obligatorio especificar si cuentas con otros recursos', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-project-field-zipcode', 'El cÃ³digo postal del responsable del proyecto es obligatorio', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-social_reward-field-description', 'Es obligatorio poner alguna descripciÃ³n al retorno', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-social_reward-field-name', 'Es obligatorio poner el retorno', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-support-field-description', 'Es obligatorio poner alguna descripciÃ³n', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('mandatory-support-field-name', 'Es obligatorio ponerle un nombre a la colaboraciÃ³n', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('regular-mandatory', 'Texto genÃ©rico para indicar campo obligatorio', NULL, 'general');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-1', 'Perfil', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-2', 'Datos personales', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-3', 'DescripciÃ³n', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-4', 'Costes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-5', 'Retorno', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-6', 'Colaboraciones', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-7', 'PrevisualizaciÃ³n', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-costs', 'Paso 4, desglose de costes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-overview', 'Paso 3, descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-preview', 'paso 7, previsualizaciÃ³n', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-rewards', 'Paso 5, retornos', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-supports', 'Paso 6, colaboraciones', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-userPersonal', 'Paso 2, informaciÃ³n del responsable', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('step-userProfile', 'Paso 1, informaciÃ³n del usuario', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('subject-change-email', 'Asunto del mail al cambiar el email', NULL, 'dashboard');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('subject-register', 'Asunto del email al registrarse', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-individual_reward-social_reward-icon', 'Texto tooltip tipo de recompensa', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-about', 'Consejo para rellenar el campo quÃ© es', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-address', 'Consejo para rellenar el address del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-category', 'Consejo para seleccionar la categorÃ­a del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-comment', 'Tooltip campo comentario', NULL, 'preview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-contract_email', 'Consejo para rellenar el email del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-contract_name', 'Consejo para rellenar el nombre del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-contract_nif', 'Consejo para rellenar el nif del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-contract_surname', 'Consejo para rellenar el apellido del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost', 'Consejo para editar desgloses existentes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-amount', 'Texto tooltip cantidad coste', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-cost', 'Texto tooltip nombre coste', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-dates', 'Texto tooltip fechas costes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-description', 'Texto tooltip descripcion costes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-from', 'Texto tooltip fecha desde costes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-required', 'Texto tooltip algun coste requerido', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-type', 'Texto tooltip tipo de coste', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-cost-until', 'Texto tooltip fecha coste hasta', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-costs', 'Texto tooltip desglose de costes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-country', 'Consejo para rellenar el paÃ­s del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-currently', 'Consejo para rellenar el estado de desarrollo del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-description', 'Consejo para rellenar la descripciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-goal', 'Consejo para rellenar el campo objetivos', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-image', 'Consejo para rellenar la imagen del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-image_upload', 'Texto tooltip subir imagen proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-individual_reward', 'Consejo para editar retornos individuales existentes', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-individual_reward-amount', 'Texto tooltip cantidad para recompensa', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-individual_reward-description', 'Texto tooltip descripcion recompensa', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-individual_reward-reward', 'Texto tooltip nombre recompensa', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-individual_reward-units', 'Texto tooltip unidades de recompensa', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-individual_rewards', 'Texto tooltip recompensas individuales', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-keywords', 'Consejo para rellenar las palabras clave del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-location', 'Consejo para rellenar el lugar de residencia del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-media', 'Consejo para rellenar el media del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-motivation', 'Consejo para rellenar el campo motivaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-name', 'Consejo para rellenar el nombre del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-ncost', 'Consejo para rellenar un nuevo desglose de costes', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-nindividual_reward', 'Consejo para rellenar un nuevo retorno individual', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-nsocial_reward', 'Consejo para rellenar un nuevo retorno colectivo', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-nsupport', 'Consejo para rellenar una nueva colaboraciÃ³n', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-phone', 'Consejo para rellenar el telÃ©fono del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-project_location', 'Consejo para rellenar la localizaciÃ³n del proyecto', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-related', 'Consejo para rellenar el campo experiencia relacionada y equipo', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-resource', 'Consejo para rellenar el campo Cuenta con otros recursos?', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-schedule', 'Texto tooltip agenda del proyeecto', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-social_reward', 'Consejo para editar retornos colectivos existentes', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-social_reward-description', 'Texto tooltip descripcion retorno', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-social_reward-icon', 'Texto tooltip tipo retorno', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-social_reward-license', 'Texto tooltip licencia retorno', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-social_reward-reward', 'Texto tooltip nombre retorno', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-social_rewards', 'Texto tooltip retornos colectivos', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-support', 'Consejo para editar colaboraciones existentes', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-support-description', 'Texto tooltip descripcion colaboracion', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-support-support', 'Texto tooltip nombre colaboracion', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-support-type', 'Texto tooltip tipo colaboracion', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-supports', 'Texto tooltip colaboraciones', NULL, 'supports');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-totals', 'Texto tooltip costes totales', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-project-zipcode', 'Consejo para rellenar el zipcode del responsable del proyecto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-about', 'Consejo para rellenar el cuÃ©ntanos algo sobre tÃ­', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-avatar_upload', 'Texto tooltip subir imagen usuario', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-blog', 'Consejo para rellenar la web', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-contribution', 'Consejo para rellenar el quÃ© podrÃ­as aportar en goteo', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-email', 'Consejo para rellenar el email de registro de usuario', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-facebook', 'Consejo para rellenar el facebook', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-image', 'Consejo para rellenar la imagen del usuario', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-interests', 'Consejo para seleccionar tus intereses', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-keywords', 'Consejo para rellenar tus palabras clave', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-linkedin', 'Consejo para rellenar el linkedin', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-location', 'Texto tooltip lugar de residencia del usuario', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-name', 'Consejo para rellenar el nombre completo del usuario', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-twitter', 'Consejo para rellenar el twitter', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-user', 'Consejo para rellenar el nombre de usuario para login', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('tooltip-user-webs', 'Texto tooltip webs del usuario', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('user-account-inactive', 'La cuenta esta desactivada', NULL, 'general');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('user-changeemail-success', 'El email se ha cambiado con exito', NULL, 'dashboard');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-cost-field-dates', 'Indicar las fechas de inicio y final de este coste para mejorar la puntuaciÃ³n', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-field-about', 'La explicacion del proyecto es demasiado corta', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-field-costs', 'Desglosar hasta 5 costes para mejorar la puntuaciÃ³n', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-field-currently', 'Indicar el estado del proyecto para mejorar la puntuaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-field-description', 'La descripcion del proyecto es demasiado corta', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-individual_rewards', 'Indicar hasta 5 recompensas individuales para mejorar la puntuaciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-social_rewards', 'Indicar hasta 5 retornos colectivos para mejorar la puntuaciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-total-costs', 'El coste Ã³ptimo no puede exceder demasiado al coste mÃ­nimo', NULL, 'costs');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-value-contract-email', 'El email no es correcto', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-value-contract-nif', 'El nif del responsable del proyecto debe ser correcto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-value-description', 'La descripciÃ³n del proyecto debe se suficientemente extensa', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-value-keywords', 'Indicar hasta 5 palabras clave del proyecto para mejorar la puntuaciÃ³n', NULL, 'overview');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-project-value-phone', 'El telÃ©fono debe ser correcto', NULL, 'personal');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-register-value-email', 'El email introducido no es valido', NULL, 'register');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-social_reward-license', 'Indicar una licencia para mejorar la puntuaciÃ³n', NULL, 'rewards');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-about', 'Si no ha puesto nada sobre el/ella ', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-avatar', 'Si no ha puesto una imagen de perfil', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-contribution', 'Si no ha puesto quÃ© puede aportar a Goteo', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-facebook', 'Si no ha puesto su cuenta de facebook', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-interests', 'Si no ha seleccionado ningÃºn interÃ©s', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-keywords', 'Si no ha puesto ninguna palabra clave', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-linkedin', 'El campo linkedin no es valido', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-location', 'El lugar de residencia del usuario no es valido', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-name', 'Si no ha puesto el nombre completo', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-twitter', 'El twitter del usuario no es valido', NULL, 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `html`, `group`) VALUES('validate-user-field-webs', 'Si no ha puesto ninguna web', NULL, 'profile');
