Entidades de la persistencia de datos en MySQL

Los nombres de las tablas y campos se ponen en español para que cualquier persona que hable español lo pueda entender.
Los nombres de los campos también son más fáciles en español por el lingo legal.

Nombres de campos "flags" si se usarán en inglés para el flujo de la lectura del código, y son populares en software.
Ejémplos de esto serían campos como 'id', 'status', 'created', 'updated',

Diccionario de términos
 - GTC: GuateCompras

La estructura de la base de datos se genera a partir del módulo de Doctrine desde la raiz con el comando:

    $ vendor/bin/doctrine-module orm:schema-tool:create

Esto se conecta a la base de datos vacía y genera las tablas y sus relaciones a partir de las anotiaciones en los
docblocks. Esto ayuda a no tener des-sincronizada la estructura y el modelo en PHP.

Es muy importante documentar lo más posible la estructura de datos pues es la base para entender un sistema.

                "Show me your data structures, and I won't usually need your code; it'll be obvious."
                                                                                     -- Eric Raymond


// @todo mover las tablas restantes a entidades Doctrine

DROP TABLE IF EXISTS tipo_comprador;
CREATE TABLE tipo_comprador (
    id            int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
    , name        varchar(128) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO tipo_comprador VALUES
-- sector público
  ( 4, 'Administración Central')
, ( 5, 'Entidades Descentralizadas, Autónomas y de Seguridad Social')
, ( 6, 'Gobiernos Locales (Municipalidades, Mancomunidades, etc.)')
, ( 7, 'Empresas Públicas (Nacionales y Municipales)')
-- fideicomisos y otras entidades
, ( 8, 'Fideicomisos con fondos públicos')
, ( 9, 'ONG''s, patronatos, comités, asociaciones y fundaciones')
, (10, 'Consejos de desarrollo')
, (11, 'Cooperativas')
, (12, 'Entidades privadas')
, (13, 'Otro tipo')
-- Organizaciones Internacionales
, (14, 'Organizaciones Internacionales')
;
*/

/*
comprador
- nombre (entidad)
- nit
- id_tipo
- origen_fondos
- id_domicilio
- url
- email

concurso
- id (nog)
- categoría
- descripción
- modalidad
- tipo de concurso
*/
