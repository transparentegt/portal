/**
 * Poblado de datos
 */
SET FOREIGN_KEY_CHECKS = 0;
SET AUTOCOMMIT         = 0;
SET SQL_MODE           = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone          = "+00:00";
START TRANSACTION;

    INSERT INTO geo_departamentos VALUES (null, 'Alta Verapaz');
    INSERT INTO geo_departamentos VALUES (null, 'Baja Verapaz');
    INSERT INTO geo_departamentos VALUES (null, 'Chimaltenango');
    INSERT INTO geo_departamentos VALUES (null, 'Chiquimula');
    INSERT INTO geo_departamentos VALUES (null, 'El Progreso');
    INSERT INTO geo_departamentos VALUES (null, 'Escuintla');
    INSERT INTO geo_departamentos VALUES (null, 'Guatemala');
    INSERT INTO geo_departamentos VALUES (null, 'Huehuetenango');
    INSERT INTO geo_departamentos VALUES (null, 'Izabal');
    INSERT INTO geo_departamentos VALUES (null, 'Jalapa');
    INSERT INTO geo_departamentos VALUES (null, 'Jutiapa');
    INSERT INTO geo_departamentos VALUES (null, 'Petén');
    INSERT INTO geo_departamentos VALUES (null, 'Quetzaltenango');
    INSERT INTO geo_departamentos VALUES (null, 'Quiché');
    INSERT INTO geo_departamentos VALUES (null, 'Retalhuleu');
    INSERT INTO geo_departamentos VALUES (null, 'Sacatepéquez');
    INSERT INTO geo_departamentos VALUES (null, 'San Marcos');
    INSERT INTO geo_departamentos VALUES (null, 'Santa Rosa');
    INSERT INTO geo_departamentos VALUES (null, 'Solola');
    INSERT INTO geo_departamentos VALUES (null, 'Suchitepéquez');
    INSERT INTO geo_departamentos VALUES (null, 'Totonicapán');
    INSERT INTO geo_departamentos VALUES (null, 'Zacapa');

    INSERT INTO `geo_municipios`
    (`id`, `nombre`, `id_geo_departamento`, `nombre_guatecompras`)
    VALUES

      -- Alta Verapaz
    (NULL, 'Chahal', 1, 'Chahal'),
    (NULL, 'Chisec', 1, 'Chisec'),
    (NULL, 'Cobán', 1, 'Cobán'),
    (NULL, 'Fray Bartolomé de las Casas', 1, 'Bartolome de la casas'),
    (NULL, 'La Tinta', 1, 'La Tinta'),
    (NULL, 'Lanquín', 1, 'Lanquín'),
    (NULL, 'Panzós', 1, 'Panzós'),
    (NULL, 'Raxruhá', 1, 'Raxruhá'),
    (NULL, 'San Cristóbal Verapaz', 1, 'San Cristóbal Verapaz'),
    (NULL, 'San Juan Chamelco', 1, 'San Juan Chamelco'),
    (NULL, 'San Pedro Carchá', 1, 'San Pedro Carchá'),
    (NULL, 'Santa Cruz Verapaz', 1, 'Santa Cruz Verapaz'),
    (NULL, 'Santa María Cahabón', 1, 'Santa MAR!A Cahabon'),
    (NULL, 'Senahú', 1, 'Senahú'),
    (NULL, 'Tamahú', 1, 'Tamahú'),
    (NULL, 'Tactic', 1, 'Tactic'),
    (NULL, 'Tucurú', 1, 'Tucurú'),

      -- Baja Verapaz
    (NULL, 'Cubulco', 2, 'Cubulco'),
    (NULL, 'Granados', 2, 'Granados'),
    (NULL, 'Purulhá', 2, 'Purulhá'),
    (NULL, 'Rabinal', 2, 'Rabinal'),
    (NULL, 'Salamá', 2, 'Salamá'),
    (NULL, 'San Jerónimo', 2, 'San Jerónimo'),
    (NULL, 'San Miguel Chicaj', 2, 'San Miguel Chicaj'),
    (NULL, 'Santa Cruz el Chol', 2, 'Santa Cruz el Chol'),

      -- Chimaltenango
    (NULL, 'Acatenango', 3, 'Acatenango'),
    (NULL, 'Chimaltenango', 3, 'Chimaltenango'),
    (NULL, 'El Tejar', 3, 'El Tejar'),
    (NULL, 'Parramos', 3, 'Parramos'),
    (NULL, 'Patzicía', 3, 'Patzicía'),
    (NULL, 'Patzún', 3, 'Patzún'),
    (NULL, 'Pochuta', 3, 'Pochuta'),
    (NULL, 'San Andrés Itzapa', 3, 'San Andrés Itzapa'),
    (NULL, 'San José Poaquíl', 3, 'San José Poaquíl'),
    (NULL, 'San Juan Comalapa', 3, 'San Juan Comalapa'),
    (NULL, 'San Martín Jilotepeque', 3, 'San Martín Jilotepeque'),
    (NULL, 'Santa Apolonia', 3, 'Santa Apolonia'),
    (NULL, 'Santa Cruz Balanyá', 3, 'Santa Cruz Balanyá'),
    (NULL, 'Tecpán', 3, 'Tecpan Guatemala'),
    (NULL, 'Yepocapa', 3, 'Yepocapa'),
    (NULL, 'Zaragoza', 3, 'Zaragoza'),

      -- Chiquimula
    (NULL, 'Camotán', 4, 'Camotán'),
    (NULL, 'Chiquimula', 4, 'Chiquimula'),
    (NULL, 'Concepción Las Minas', 4, 'Concepción Las Minas'),
    (NULL, 'Esquipulas', 4, 'Esquipulas'),
    (NULL, 'Ipala', 4, 'Ipala'),
    (NULL, 'Jocotán', 4, 'Jocotán'),
    (NULL, 'Olopa', 4, 'Olopa'),
    (NULL, 'Quezaltepeque', 4, 'Quetzaltepeque'),
    (NULL, 'San Jacinto', 4, 'San Jacinto'),
    (NULL, 'San José la Arada', 4, 'San José la Arada'),
    (NULL, 'San Juan Ermita', 4, 'San Juan Ermita'),

      -- El Progreso
    (NULL, 'El Jícaro', 5, 'El Jícaro'),
    (NULL, 'Guastatoya', 5, 'Guastatoya'),
    (NULL, 'Morazán', 5, 'Morazán'),
    (NULL, 'San Agustín Acasaguastlán', 5, 'San Agustín Acasaguastlán'),
    (NULL, 'San Antonio La Paz', 5, 'San Antonio La Paz'),
    (NULL, 'San Cristóbal Acasaguastlán', 5, 'San Cristóbal Acasaguastlán'),
    (NULL, 'Sanarate', 5, 'Sanarate'),
    (NULL, 'Sansare', 5, 'Sansare'),

      -- Escuintla
    (NULL, 'Escuintla', 6, 'Escuintla'),
    (NULL, 'Guanagazapa', 6, 'Guanagazapa'),
    (NULL, 'Iztapa', 6, 'Iztapa'),
    (NULL, 'La Democracia', 6, 'La Democracia'),
    (NULL, 'La Gomera', 6, 'La Gomera'),
    (NULL, 'Masagua', 6, 'Masagua'),
    (NULL, 'Nueva Concepción', 6, 'Nueva Concepción'),
    (NULL, 'Palín', 6, 'Palín'),
    (NULL, 'San José', 6, 'Puerto San Jose'),
    (NULL, 'San Vicente Pacaya', 6, 'San Vicente Pacaya'),
    (NULL, 'Santa Lucía Cotzumalguapa', 6, 'Santa luc!a cotzumalguapa'),
    (NULL, 'Siquinalá', 6, 'Siquinal'),
    (NULL, 'Tiquisate', 6, 'Pueblo Nuevo Tiquisate'),

      -- Guatemala
    (NULL, 'Amatitlán', 7, 'Amatitlán'),
    (NULL, 'Chinautla', 7, 'Chinautla'),
    (NULL, 'Chuarrancho', 7, 'Chuarrancho'),
    (NULL, 'Guatemala', 7, 'Guatemala'),
    (NULL, 'Fraijanes', 7, 'Fraijanes'),
    (NULL, 'Mixco', 7, 'Mixco'),
    (NULL, 'Palencia', 7, 'Palencia'),
    (NULL, 'San José del Golfo', 7, 'San José del Golfo'),
    (NULL, 'San José Pinula', 7, 'San José Pinula'),
    (NULL, 'San Juan Sacatepéquez', 7, 'San Juan Sacatepéquez'),
    (NULL, 'San Miguel Petapa', 7, 'San Miguel Petapa'),
    (NULL, 'San Pedro Ayampuc', 7, 'San Pedro Ayampuc'),
    (NULL, 'San Pedro Sacatepéquez', 7, 'San Pedro Sacatepéquez'),
    (NULL, 'San Raymundo', 7, 'San Raymundo'),
    (NULL, 'Santa Catarina Pinula', 7, 'Santa Catarina Pinula'),
    (NULL, 'Villa Canales', 7, 'Villa Canales'),
    (NULL, 'Villa Nueva', 7, 'Villa Nueva'),

      -- Huehuetenango
    (NULL, 'Aguacatán', 8, 'Aguacatán'),
    (NULL, 'Chiantla', 8, 'Chiantla'),
    (NULL, 'Colotenango', 8, 'Colotenango'),
    (NULL, 'Concepción Huista', 8, 'Concepción Huista'),
    (NULL, 'Cuilco', 8, 'Cuilco'),
    (NULL, 'Huehuetenango', 8, 'Huehuetenango'),
    (NULL, 'Jacaltenango', 8, 'Jacaltenango'),
    (NULL, 'La Democracia', 8, 'La Democracia'),
    (NULL, 'La Libertad', 8, 'La Libertad'),
    (NULL, 'Malacatancito', 8, 'Malacatancito'),
    (NULL, 'Nentón', 8, 'Nentón'),
    (NULL, 'San Antonio Huista', 8, 'San Antonio Huista'),
    (NULL, 'San Gaspar Ixchil', 8, 'San Gaspar Ixchil'),
    (NULL, 'San Ildefonso Ixtahuacán', 8, 'San Ildefonso Ixtahuacán'),
    (NULL, 'San Juan Atitán', 8, 'San Juan Atitán'),
    (NULL, 'San Juan Ixcoy', 8, 'San Juan Ixcoy'),
    (NULL, 'San Mateo Ixtatán', 8, 'San Mateo Ixtatán'),
    (NULL, 'San Miguel Acatán', 8, 'San Miguel Acatán'),
    (NULL, 'San Pedro Nécta', 8, 'San Pedro Nécta'),
    (NULL, 'San Pedro Soloma', 8, 'San Pedro Soloma'),
    (NULL, 'San Rafael La Independencia', 8, 'San Rafael La Independencia'),
    (NULL, 'San Rafael Pétzal', 8, 'San Rafael Pétzal'),
    (NULL, 'San Sebastián Coatán', 8, 'San Sebastián Coatán'),
    (NULL, 'San Sebastián Huehuetenango', 8, 'San Sebastián Huehuetenango'),
    (NULL, 'Santa Ana Huista', 8, 'Santa Ana Huista'),
    (NULL, 'Santa Bárbara', 8, 'Santa Bárbara'),
    (NULL, 'Santa Cruz Barillas', 8, 'Santa Cruz Barillas'),
    (NULL, 'Santa Eulalia', 8, 'Santa Eulalia'),
    (NULL, 'Santiago Chimaltenango', 8, 'Santiago Chimaltenango'),
    (NULL, 'Tectitán', 8, 'Tectitán'),
    (NULL, 'Todos Santos Cuchumatán', 8, 'Todos Santos Cuchumatán'),
    (NULL, 'Unión Cantinil', 8, 'Unión Cantinil'),

      -- Izabal
    (NULL, 'El Estor', 9, 'El Estor'),
    (NULL, 'Livingston', 9, 'Livingston'),
    (NULL, 'Los Amates', 9, 'Los Amates'),
    (NULL, 'Morales', 9, 'Morales'),
    (NULL, 'Puerto Barrios', 9, 'Puerto Barrios'),

      -- Jalapa
    (NULL, 'Jalapa', 10, 'Jalapa'),
    (NULL, 'Mataquescuintla', 10, 'Mataquescuintla'),
    (NULL, 'Monjas', 10, 'Monjas'),
    (NULL, 'San Carlos Alzatate', 10, 'San Carlos Alzatate'),
    (NULL, 'San Luis Jilotepeque', 10, 'San Luis Jilotepeque'),
    (NULL, 'San Manuel Chaparrón', 10, 'San Manuel Chaparrón'),
    (NULL, 'San Pedro Pinula', 10, 'San Pedro Pinula'),

      -- Jutiapa
    (NULL, 'Agua Blanca', 11, 'Agua Blanca'),
    (NULL, 'Asunción Mita', 11, 'Asunción Mita'),
    (NULL, 'Atescatempa', 11, 'Atescatempa'),
    (NULL, 'Comapa', 11, 'Comapa'),
    (NULL, 'Conguaco', 11, 'Conguaco'),
    (NULL, 'El Adelanto', 11, 'El Adelanto'),
    (NULL, 'El Progreso', 11, 'El Progreso'),
    (NULL, 'Jalpatagua', 11, 'Jalpatagua'),
    (NULL, 'Jerez', 11, 'Jerez'),
    (NULL, 'Jutiapa', 11, 'Jutiapa'),
    (NULL, 'Moyuta', 11, 'Moyuta'),
    (NULL, 'Pasaco', 11, 'Pasaco'),
    (NULL, 'Quesada', 11, 'Quezada'),
    (NULL, 'San José Acatempa', 11, 'San José Acatempa'),
    (NULL, 'Santa Catarina Mita', 11, 'Santa Catarina Mita'),
    (NULL, 'Yupiltepeque', 11, 'Yupiltepeque'),
    (NULL, 'Zapotitlán', 11, 'Zapotitlán'),

      -- Petén
    (NULL, 'Dolores', 12, 'Dolores'),
    (NULL, 'El Chal', 12, 'El Chal'),
    (NULL, 'Ciudad Flores', 12, 'Ciudad Flores'),
    (NULL, 'La Libertad', 12, 'La Libertad'),
    (NULL, 'Las Cruces', 12, 'Las Cruces'),
    (NULL, 'Melchor de Mencos', 12, 'Melchor de Mencos'),
    (NULL, 'Poptún', 12, 'Poptún'),
    (NULL, 'San Andrés', 12, 'San Andrés'),
    (NULL, 'San Benito', 12, 'San Benito'),
    (NULL, 'San Francisco', 12, 'San Francisco'),
    (NULL, 'San José', 12, 'San José'),
    (NULL, 'San Luis', 12, 'San Luis'),
    (NULL, 'Santa Ana', 12, 'Santa Ana'),
    (NULL, 'Sayaxché', 12, 'Sayaxché'),

      -- Quetzaltenango
    (NULL, 'Almolonga', 13, 'Almolonga'),
    (NULL, 'Cabricán', 13, 'Cabricán'),
    (NULL, 'Cajolá', 13, 'Cajol'),
    (NULL, 'Cantel', 13, 'Cantel'),
    (NULL, 'Coatepeque', 13, 'Coatepeque'),
    (NULL, 'Colomba Costa Cuca', 13, 'Colomba'),
    (NULL, 'Concepción Chiquirichapa', 13, 'Concepción Chiquirichapa'),
    (NULL, 'El Palmar', 13, 'El Palmar'),
    (NULL, 'Flores Costa Cuca', 13, 'Flores Costa Cuca'),
    (NULL, 'Génova', 13, 'Génova'),
    (NULL, 'Huitán', 13, 'Huitán'),
    (NULL, 'La Esperanza', 13, 'La Esperanza'),
    (NULL, 'Olintepeque', 13, 'Olintepeque'),
    (NULL, 'Palestina de Los Altos', 13, 'Palestina de Los Altos'),
    (NULL, 'Quetzaltenango', 13, 'Quetzaltenango'),
    (NULL, 'Salcajá', 13, 'Salcajá'),
    (NULL, 'San Carlos Sija', 13, 'San Carlos Sija'),
    (NULL, 'San Francisco La Unión', 13, 'San Francisco La Unión'),
    (NULL, 'San Juan Ostuncalco', 13, 'San Juan Ostuncalco'),
    (NULL, 'San Martín Sacatepéquez', 13, 'San Martín Sacatepéquez'),
    (NULL, 'San Mateo', 13, 'San Mateo'),
    (NULL, 'San Miguel Sigüilá', 13, 'San Miguel Sigüilá'),
    (NULL, 'Sibilia', 13, 'Sibilia'),
    (NULL, 'Zunil', 13, 'Zunil'),

      -- Quiché
    (NULL, 'Canillá', 14, 'Canillá'),
    (NULL, 'Chajul', 14, 'Chajul'),
    (NULL, 'Chicamán', 14, 'Chicamán'),
    (NULL, 'Chiché', 14, 'Chiché'),
    (NULL, 'Chichicastenango', 14, 'Chichicastenango'),
    (NULL, 'Chinique', 14, 'Chinique'),
    (NULL, 'Cunén', 14, 'Cunén'),
    (NULL, 'Ixcán Playa Grande', 14, 'Ixcán Playa Grande'),
    (NULL, 'Joyabaj', 14, 'Joyabaj'),
    (NULL, 'Nebaj', 14, 'Nebaj'),
    (NULL, 'Pachalum', 14, 'Pachalum'),
    (NULL, 'Patzité', 14, 'Patzité'),
    (NULL, 'Sacapulas', 14, 'Sacapulas'),
    (NULL, 'San Andrés Sajcabajá', 14, 'San Andrés Sajcabajá'),
    (NULL, 'San Antonio Ilotenango', 14, 'San Antonio Alotenango'),
    (NULL, 'San Bartolomé Jocotenango', 14, 'San Bartolomé Jocotenango'),
    (NULL, 'San Juan Cotzal', 14, 'San Juan Cotzal'),
    (NULL, 'San Pedro Jocopilas', 14, 'San Pedro Jocopilas'),
    (NULL, 'Santa Cruz del Quiché', 14, 'Santa Cruz del Quiché'),
    (NULL, 'Uspantán', 14, 'Uspantán'),
    (NULL, 'Zacualpa', 14, 'Zacualpa'),

      -- Retalhuleu
    (NULL, 'Champerico', 15, 'Champerico'),
    (NULL, 'El Asintal', 15, 'El Asintal'),
    (NULL, 'Nuevo San Carlos', 15, 'Nuevo San Carlos'),
    (NULL, 'Retalhuleu', 15, 'Retalhuleu'),
    (NULL, 'San Andrés Villa Seca', 15, 'San Andrés Villa Seca'),
    (NULL, 'San Felipe Reu', 15, 'San Felipe Reu'),
    (NULL, 'San Martín Zapotitlán', 15, 'San Martín Zapotitlán'),
    (NULL, 'San Sebastián', 15, 'San Sebastián'),
    (NULL, 'Santa Cruz Muluá', 15, 'Santa Cruz Muluá'),

      -- Sacatepéquez
    (NULL, 'Alotenango', 16, 'Alotenango'),
    (NULL, 'Ciudad Vieja', 16, 'Ciudad Vieja'),
    (NULL, 'Jocotenango', 16, 'Jocotenango'),
    (NULL, 'Antigua Guatemala', 16, 'Antigua Guatemala'),
    (NULL, 'Magdalena Milpas Altas', 16, 'Magdalena Milpas Altas'),
    (NULL, 'Pastores', 16, 'Pastores'),
    (NULL, 'San Antonio Aguas Calientes', 16, 'San Antonio Aguas Calientes'),
    (NULL, 'San Bartolomé Milpas Altas', 16, 'San Bartolomé Milpas Altas'),
    (NULL, 'San Lucas Sacatepéquez', 16, 'San Lucas Sacatepéquez'),
    (NULL, 'San Miguel Dueñas', 16, 'San Miguel DUE?AS'),
    (NULL, 'Santa Catarina Barahona', 16, 'Santa Catarina Barahona'),
    (NULL, 'Santa Lucía Milpas Altas', 16, 'Santa Lucía Milpas Altas'),
    (NULL, 'Santa María de Jesús', 16, 'Santa María de Jesús'),
    (NULL, 'Santiago Sacatepéquez', 16, 'Santiago Sacatepéquez'),
    (NULL, 'Santo Domingo Xenacoj', 16, 'Santo Domingo Xenacoj'),
    (NULL, 'Sumpango', 16, 'Sumpango'),

      -- San Marcos
    (NULL, 'Ayutla', 17, 'Ciudad Tecun Human Ayutla'),
    (NULL, 'Catarina', 17, 'Catarina'),
    (NULL, 'Comitancillo', 17, 'Comintacillo'),
    (NULL, 'Concepción Tutuapa', 17, 'Concepción Tutuapa'),
    (NULL, 'El Quetzal', 17, 'El Quetzal'),
    (NULL, 'El Tumbador', 17, 'El Tumbador'),
    (NULL, 'Esquipulas Palo Gordo', 17, 'Esquipulas Palo Gordo'),
    (NULL, 'Ixchiguán', 17, 'Ixchiguán'),
    (NULL, 'La Blanca', 17, 'La Blanca'),
    (NULL, 'La Reforma', 17, 'La Reforma'),
    (NULL, 'Malacatán', 17, 'Malacatán'),
    (NULL, 'Nuevo Progreso', 17, 'Nuevo Progreso'),
    (NULL, 'Ocós', 17, 'Ocós'),
    (NULL, 'Pajapita', 17, 'Pajapita'),
    (NULL, 'Río Blanco', 17, 'Río Blanco'),
    (NULL, 'San Antonio Sacatepéquez', 17, 'San Antonio Sacatepéquez'),
    (NULL, 'San Cristóbal Cucho', 17, 'San Cristóbal Cucho'),
    (NULL, 'San José El Rodeo', 17, 'San José El Rodeo'),
    (NULL, 'San José Ojetenam', 17, 'San José Ojetenam'),
    (NULL, 'San Lorenzo', 17, 'San Lorenzo'),
    (NULL, 'San Marcos', 17, 'San Marcos'),
    (NULL, 'San Miguel Ixtahuacán', 17, 'San Miguel Ixtahuacán'),
    (NULL, 'San Pablo', 17, 'San Pablo'),
    (NULL, 'San Pedro Sacatepéquez', 17, 'San Pedro Sacatepéquez'),
    (NULL, 'San Rafael Pie de la Cuesta', 17, 'San Rafael Pie de la Cuesta'),
    (NULL, 'Sibinal', 17, 'Sibinal'),
    (NULL, 'Sipacapa', 17, 'Sipacapa'),
    (NULL, 'Tacaná', 17, 'Tacaná'),
    (NULL, 'Tajumulco', 17, 'Tajumulco'),
    (NULL, 'Tejutla', 17, 'Tejutla'),

    -- Santa Rosa
    (NULL, 'Barberena', 18, 'Barberena'),
    (NULL, 'Casillas', 18, 'Casillas'),
    (NULL, 'Chiquimulilla', 18, 'Chiquimulilla'),
    (NULL, 'Cuilapa', 18, 'Cuilapa'),
    (NULL, 'Guazacapán', 18, 'Guazacapán'),
    (NULL, 'Nueva Santa Rosa', 18, 'Nueva Santa Rosa'),
    (NULL, 'Oratorio', 18, 'Oratorio'),
    (NULL, 'Pueblo Nuevo Viñas', 18, 'Pueblo Nuevo Viñas'),
    (NULL, 'San Juan Tecuaco', 18, 'San Juan Tecuaco'),
    (NULL, 'San Rafael las Flores', 18, 'San Rafael las Flores'),
    (NULL, 'Santa Cruz Naranjo', 18, 'Santa Cruz Naranjo'),
    (NULL, 'Santa María Ixhuatán', 18, 'Santa María Ixhuatán'),
    (NULL, 'Santa Rosa de Lima', 18, 'Santa Rosa de Lima'),
    (NULL, 'Taxisco', 18, 'Taxisco'),

    -- Sololá
    (NULL, 'Concepción', 19, 'Concepción'),
    (NULL, 'Nahualá', 19, 'Nahual'),
    (NULL, 'Panajachel', 19, 'Panajachel'),
    (NULL, 'San Andrés Semetabaj', 19, 'San Andrés Semetabaj'),
    (NULL, 'San Antonio Palopó', 19, 'San Antonio Palopó'),
    (NULL, 'San José Chacayá', 19, 'San José Chacayá'),
    (NULL, 'San Juan La Laguna', 19, 'San Juan Laguna'),
    (NULL, 'San Lucas Tolimán', 19, 'San Lucas Tolimán'),
    (NULL, 'San Marcos La Laguna', 19, 'San Marcos Laguna'),
    (NULL, 'San Pablo La Laguna', 19, 'San Pablo La Laguna'),
    (NULL, 'San Pedro La Laguna', 19, 'San Pedro Laguna'),
    (NULL, 'Santa Catarina Ixtahuacán', 19, 'Santa Catarina Ixtahuatan'),
    (NULL, 'Santa Catarina Palopó', 19, 'Santa Catarina Palopó'),
    (NULL, 'Santa Clara La Laguna', 19, 'Santa Clara La Laguna'),
    (NULL, 'Santa Cruz La Laguna', 19, 'Santa Cruz La Laguna'),
    (NULL, 'Santa Lucía Utatlán', 19, 'Santa Lucía Utatlán'),
    (NULL, 'Santa María Visitación', 19, 'Santa María Visitación'),
    (NULL, 'Santiago Atitlán', 19, 'SANTIAGO ATITL AN'),
    (NULL, 'Sololá', 19, 'Sololá'),

    -- Suchitepéquez
    (NULL, 'Chicacao', 20, 'Chicacao'),
    (NULL, 'Cuyotenango', 20, 'Cuyotenango'),
    (NULL, 'Mazatenango', 20, 'Mazatenango'),
    (NULL, 'Patulul', 20, 'Patulul'),
    (NULL, 'Pueblo Nuevo', 20, 'Pueblo Nuevo'),
    (NULL, 'Río Bravo', 20, 'Río Bravo'),
    (NULL, 'Samayac', 20, 'Samayac'),
    (NULL, 'San Antonio Suchitepéquez', 20, 'San Antonio Suchitepéquez'),
    (NULL, 'San Bernardino', 20, 'San Bernardino'),
    (NULL, 'San Francisco Zapotitlán', 20, 'San Francisco Zapotitlán'),
    (NULL, 'San Gabriel', 20, 'San Gabriel'),
    (NULL, 'San José El Idolo', 20, 'San José El Idolo'),
    (NULL, 'San José La Maquina', 20, 'San José La Maquina'),
    (NULL, 'San Juan Bautista', 20, 'San Juan Bautista'),
    (NULL, 'San Lorenzo', 20, 'San Lorenzo'),
    (NULL, 'San Miguel Panán', 20, 'San Miguel Panán'),
    (NULL, 'San Pablo Jocopilas', 20, 'San Pablo Jocopilas'),
    (NULL, 'Santa Bárbara', 20, 'Santa Bárbara'),
    (NULL, 'Santo Domingo Suchitepéquez', 20, 'Santo Domingo Suchitepéquez'),
    (NULL, 'Santo Tomás La Unión', 20, 'Santo Tomás La Unión'),
    (NULL, 'Zunilito', 20, 'Zunilito'),

    -- Totonicapán
    (NULL, 'Momostenango', 21, 'Momostenango'),
    (NULL, 'San Andrés Xecul', 21, 'San Andrés Xecul'),
    (NULL, 'San Bartolo', 21, 'San Bartolo'),
    (NULL, 'San Cristóbal Totonicapán', 21, 'San Cristóbal Totonicapán'),
    (NULL, 'San Francisco El Alto', 21, 'San Francisco El Alto'),
    (NULL, 'Santa Lucía La Reforma', 21, 'Santa Lucía La Reforma'),
    (NULL, 'Santa María Chiquimula', 21, 'Santa María Chiquimula'),
    (NULL, 'Totonicapán', 21, 'Totonicapán'),

    -- Zacapa
    (NULL, 'Cabañas', 22, 'CABA?AS'),
    (NULL, 'Estanzuela', 22, 'Estanzuela'),
    (NULL, 'Gualán', 22, 'Gualán'),
    (NULL, 'Huité', 22, 'Huité'),
    (NULL, 'La Unión', 22, 'La Unión'),
    (NULL, 'Río Hondo', 22, 'Río Hondo'),
    (NULL, 'San Diego', 22, 'San Diego'),
    (NULL, 'San Jorge', 22, 'San Jorge'),
    (NULL, 'Teculután', 22, 'Teculután'),
    (NULL, 'Usumatlán', 22, 'Usumatlán'),
    (NULL, 'Zacapa', 22, 'Zacapa');

    /**
     * @todo Poner bien los nombres de los partidos políticos
     */
    INSERT INTO partido_político VALUES
      (NULL, 'Acción de Desarrollo Nacional', 'ADN')
    , (NULL, 'Alternativa Nueva Nación', 'ANN')
    , (NULL, 'Bienestar Nacional', 'BIEN')
    , (NULL, 'BLOCK', 'BLOCK')
    , (NULL, 'Centro de Acción Social', 'CASA')
    , (NULL, 'CCA', 'CCA')
    , (NULL, 'C.C.A', 'C.C.A')
    , (NULL, 'CCAM', 'CCAM')
    , (NULL, 'C.C.E.A', 'C.C.E.A')
    , (NULL, 'CCEC', 'CCEC')
    , (NULL, 'C.C.E.C', 'C.C.E.C')
    , (NULL, 'CCEEA', 'CCEEA')
    , (NULL, 'CCEGC', 'CCEGC')
    , (NULL, 'CCELLC', 'CCELLC')
    , (NULL, 'CCET', 'CCET')
    , (NULL, 'CCFU', 'CCFU')
    , (NULL, 'CCJK', 'CCJK')
    , (NULL, 'C.C.M.T.', 'C.C.M.T.')
    , (NULL, 'CCP', 'CCP')
    , (NULL, 'CCPP', 'CCPP')
    , (NULL, 'CCS', 'CCS')
    , (NULL, 'CCT', 'CCT')
    , (NULL, 'CCTU', 'CCTU')
    , (NULL, 'CCYK', 'CCYK')
    , (NULL, 'Ciudadanos Activos de Formación Electoral', 'CAFÉ')
    , (NULL, 'CHICH', 'CHICH')
    , (NULL, 'CH´OOLEJ', 'CH´OOLEJ')
    , (NULL, 'CINCO', 'CINCO')
    , (NULL, 'C. I. P.', 'C. I. P.')
    , (NULL, 'Corazón Nueva Nación', 'CNN')
    , (NULL, 'CNS', 'CNS')
    , (NULL, 'COCI', 'COCI')
    , (NULL, 'COCICA', 'COCICA')
    , (NULL, 'COCIEA', 'COCIEA')
    , (NULL, 'COCIEC', 'COCIEC')
    , (NULL, 'COCIM', 'COCIM')
    , (NULL, 'COCINA', 'COCINA')
    , (NULL, 'COCIPROG', 'COCIPROG')
    , (NULL, 'COCISANJ', 'COCISANJ')
    , (NULL, 'COCT', 'COCT')
    , (NULL, 'Compromiso Renovación y Orden', 'CREO')
    , (NULL, 'EL VENADO', 'EL VENADO')
    , (NULL, 'EL ZAPATO', 'EL ZAPATO')
    , (NULL, 'Encuentro por Guatemala' , 'EG')
    , (NULL, 'Frente de Convergencia Nacional', 'FCN')
    , (NULL, 'Frente Repúblicano Guatemalteco', 'F R G')
    , (NULL, 'FUERZA', 'FUERZA')
    , (NULL, 'Gran Alianza Nacional', 'GANA')
    , (NULL, 'IKOM', 'IKOM')
    , (NULL, 'LA MATITA DE CAFÉ', 'LA MATITA DE CAFÉ')
    , (NULL, 'Libertad Democrática Renovada', 'LIDER')
    , (NULL, 'LUNA', 'LUNA')
    , (NULL, 'MASAT', 'MASAT')
    , (NULL, 'Movimiento Nueva República', 'MNR')
    , (NULL, 'Movimiento Reformador', 'MR')
    , (NULL, 'M.U.P.', 'M.U.P.')
    , (NULL, 'Partido de Avanzada Nacional', 'P A N')
    , (NULL, 'partido', 'partido')
    , (NULL, 'PO´T', 'PO´T')
    , (NULL, 'PP', 'PP')
    , (NULL, 'Partido Patriota', 'P P')
    , (NULL, 'Partido Republicano Institucional', 'PRI')
    , (NULL, 'Partido Socialdemócrata Guatemalteco', 'PSG')
    , (NULL, 'PUNEET', 'PUNEET')
    , (NULL, 'SUD', 'SUD')
    , (NULL, 'TODOS', 'TODOS')
    , (NULL, 'Unión del Cambio Nacional', 'UCN')
    , (NULL, 'Unión Democrática', 'UD')
    , (NULL, 'Unidad Nacional de la Esperanza', 'UNE')
    , (NULL, 'UNE-GANA', 'UNE-GANA')
    , (NULL, 'Partido Libertador Progresista', 'PLP')
    , (NULL, 'Parido Unionista', 'UNIONISTA')
    , (NULL, 'Unidad Revolucionaria Nacional Guatemalteca', 'U R N G')
    , (NULL, 'URNG-ANN', 'URNG-ANN')
    , (NULL, 'VICTORIA', 'VICTORIA')
    , (NULL, 'Partido Político Visión con Valores', 'VIVA')
    , (NULL, 'VIVA-EG', 'VIVA-EG')
    , (NULL, 'Movimiento Político WINAQ', 'WINAQ')
    , (NULL, 'WINAQ-ANN', 'WINAQ-ANN');

    ALTER TABLE `empleado_municipal` ADD UNIQUE (`id_municipio` ,`nombre1` , `apellido1` , `apellido2` , `cargo`);

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
