INSERT INTO purpose VALUES ('step 1', 'Paso 1, información del usuario');
INSERT INTO purpose VALUES ('step 2', 'Paso 2, información del responsable');
INSERT INTO purpose VALUES ('step 3', 'Paso 3, descripción del proyecto');
INSERT INTO purpose VALUES ('step 4', 'Paso 4, desglose de costes');
INSERT INTO purpose VALUES ('step 5', 'Paso 5, retornos');
INSERT INTO purpose VALUES ('step 6', 'Paso 6, colaboraciones');
INSERT INTO purpose VALUES ('step 7', 'paso 7, previsualización');

INSERT INTO purpose VALUES ('regular mandatory', 'Texto genérico para indicar campo obligatorio');
INSERT INTO purpose VALUES ('explain project progress', 'Texto bajo el título Estado global de la información');

INSERT INTO purpose VALUES ('guide user register', 'Texto guía en el registro de usuario');
INSERT INTO purpose VALUES ('guide user data', 'Texto guía en la edición de datos sensibles del usuario');
INSERT INTO purpose VALUES ('guide user information', 'Texto guía en el paso PERFIL del formulario de proyecto');
INSERT INTO purpose VALUES ('guide project contract information', 'Texto guía en el paso DATOS PERSONALES del formulario de proyecto');
INSERT INTO purpose VALUES ('guide project description', 'Texto guía en el paso DESCRIPCIÓN del formulario de proyecto');
INSERT INTO purpose VALUES ('guide project costs', 'Texto guía en el paso COSTES del formulario de proyecto');
INSERT INTO purpose VALUES ('guide project rewards', 'Texto guía en el paso RETORNO del formulario de proyecto');
INSERT INTO purpose VALUES ('guide project support', 'Texto guía en el paso COLABORACIONES del formulario de proyecto');
INSERT INTO purpose VALUES ('guide project overview', 'Texto guía en el paso PREVISUALIZACIÓN del formulario de proyecto');

INSERT INTO purpose VALUES ('guide project success noerrors', 'Todos los campos obligatorios estan rellenados');
INSERT INTO purpose VALUES ('guide project success minprogress', 'Ha llegado al porcentaje mínimo');
INSERT INTO purpose VALUES ('guide project success okfinish', 'Puede enviar para valoración');
INSERT INTO purpose VALUES ('guide project error mandatories', 'Faltan campos obligatorios');

INSERT INTO purpose VALUES ('error sql guardar proyecto', 'La sentencia UPDATE para grabar los datos de un proyecto en la base de datos falla.');
INSERT INTO purpose VALUES ('mandatory project field contract name', 'El nombre del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('mandatory project field contract surname', 'El apellido del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('mandatory project field contract nif', 'El nif del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('validate project value contract nif', 'El nif del responsable del proyecto debe ser correcto');
INSERT INTO purpose VALUES ('mandatory project field contract email', 'El email del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('validate project field contract email', 'El email del responsable del proyecto debe ser correcto');
INSERT INTO purpose VALUES ('validate project value phone', 'El teléfono debe ser correcto');
INSERT INTO purpose VALUES ('mandatory project field residence', 'El lugar de residencia del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('mandatory project field name', 'El nombre del proyecto es obligatorio');
INSERT INTO purpose VALUES ('mandatory project field description', 'La descripción del proyecto es obligatorio');
INSERT INTO purpose VALUES ('validate project value description', 'La descripción del proyecto debe se suficientemente extensa');
INSERT INTO purpose VALUES ('mandatory project field category', 'La categoría del proyecto es obligatoria');
INSERT INTO purpose VALUES ('mandatory project field location', 'La localización del proyecto es obligatoria');
INSERT INTO purpose VALUES ('validation project min costs', 'Mínimo de costes a desglosar en un proyecto');
INSERT INTO purpose VALUES ('validation project total costs', 'El coste óptimo no puede exceder demasiado al coste mínimo');
INSERT INTO purpose VALUES ('mandatory project field phone', 'El teléfono del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('mandatory project field address', 'La dirección del responsable del proyecto es obligatoria');
INSERT INTO purpose VALUES ('mandatory project field zipcode', 'El código postal del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('mandatory project field country', 'El país del responsable del proyecto es obligatorio');
INSERT INTO purpose VALUES ('mandatory project field image', 'Es obligatorio poner una imagen al proyecto');
INSERT INTO purpose VALUES ('mandatory project field motivation', 'Es obligatorio explicar la motivación en la descripción del proyecto');
INSERT INTO purpose VALUES ('mandatory project field about', 'Es obligatorio explicar qué es en la descripción del proyecto');
INSERT INTO purpose VALUES ('mandatory project field goal', 'Es obligatorio explicar los objetivos en la descripción del proyecto');
INSERT INTO purpose VALUES ('mandatory project field related', 'Es obligatorio explicar la experiencia relacionada y el equipo en la descripción del proyecto');

INSERT INTO purpose VALUES ('tooltip user user', 'Consejo para rellenar el nombre de usuario para login');
INSERT INTO purpose VALUES ('tooltip user email', 'Consejo para rellenar el email de registro de usuario');
INSERT INTO purpose VALUES ('tooltip user name', 'Consejo para rellenar el nombre completo del usuario');
INSERT INTO purpose VALUES ('tooltip user image', 'Consejo para rellenar la imagen del usuario');
INSERT INTO purpose VALUES ('tooltip user about', 'Consejo para rellenar el cuéntanos algo sobre tí');
INSERT INTO purpose VALUES ('tooltip user interests', 'Consejo para rellenar tus intereses');
INSERT INTO purpose VALUES ('tooltip user contribution', 'Consejo para rellenar el qué podrías aportar en goteo');
INSERT INTO purpose VALUES ('tooltip user blog', 'Consejo para rellenar la web');
INSERT INTO purpose VALUES ('tooltip user twitter', 'Consejo para rellenar el twitter');
INSERT INTO purpose VALUES ('tooltip user facebook', 'Consejo para rellenar el facebook');
INSERT INTO purpose VALUES ('tooltip user linkedin', 'Consejo para rellenar el linkedin');

INSERT INTO purpose VALUES ('tooltip project contract_name', 'Consejo para rellenar el nombre del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project contract_surname', 'Consejo para rellenar el apellido del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project contract_nif', 'Consejo para rellenar el nif del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project contract_email', 'Consejo para rellenar el email del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project phone', 'Consejo para rellenar el teléfono del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project address', 'Consejo para rellenar el address del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project zipcode', 'Consejo para rellenar el zipcode del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project location', 'Consejo para rellenar el lugar de residencia del responsable del proyecto');
INSERT INTO purpose VALUES ('tooltip project country', 'Consejo para rellenar el país del responsable del proyecto');

INSERT INTO purpose VALUES ('tooltip project name', 'Consejo para rellenar el nombre del proyecto');
INSERT INTO purpose VALUES ('tooltip project image', 'Consejo para rellenar la imagen del proyecto');
INSERT INTO purpose VALUES ('tooltip project description', 'Consejo para rellenar la descripción del proyecto');
INSERT INTO purpose VALUES ('tooltip project motivation', 'Consejo para rellenar el campo motivación');
INSERT INTO purpose VALUES ('tooltip project about', 'Consejo para rellenar el campo qué es');
INSERT INTO purpose VALUES ('tooltip project goal', 'Consejo para rellenar el campo objetivos');
INSERT INTO purpose VALUES ('tooltip project related', 'Consejo para rellenar el campo experiencia relacionada y equipo');
INSERT INTO purpose VALUES ('tooltip project category', 'Consejo para rellenar la categoría del proyecto');
INSERT INTO purpose VALUES ('tooltip project media', 'Consejo para rellenar el media del proyecto');
INSERT INTO purpose VALUES ('tooltip project keywords', 'Consejo para rellenar las palabras clave del proyecto');
INSERT INTO purpose VALUES ('tooltip project currently', 'Consejo para rellenar el estado de desarrollo del proyecto');
INSERT INTO purpose VALUES ('tooltip project project_location', 'Consejo para rellenar la localización del proyecto');

INSERT INTO purpose VALUES ('tooltip project ncost', 'Consejo para rellenar un nuevo desglose de costes');
INSERT INTO purpose VALUES ('tooltip project cost', 'Consejo para editar desgloses existentes');
INSERT INTO purpose VALUES ('tooltip project resource', 'Consejo para rellenar el campo Cuenta con otros recursos?');

INSERT INTO purpose VALUES ('tooltip project nsocial_reward', 'Consejo para rellenar un nuevo retorno colectivo');
INSERT INTO purpose VALUES ('tooltip project social_reward', 'Consejo para editar retornos colectivos existentes');
INSERT INTO purpose VALUES ('tooltip project nindividual_reward', 'Consejo para rellenar un nuevo retorno individual');
INSERT INTO purpose VALUES ('tooltip project individual_reward', 'Consejo para editar retornos individuales existentes');

INSERT INTO purpose VALUES ('tooltip project nsupport', 'Consejo para rellenar una nueva colaboración');
INSERT INTO purpose VALUES ('tooltip project support', 'Consejo para editar colaboraciones existentes');

-- INSERT INTO purpose VALUES ('------- id max lenght --------------------------', '');
