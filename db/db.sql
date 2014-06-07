/**
 * Estructura de la DB en MySQL para guardar los datos scrappeados de Guatecompras
 *
 *
 * Los nombres de las tablas y campos se ponen en español para que cualquier guatemalteco lo pueda entender. Los nombres
 * de los campos también son mas fáciles en español por el lingo legal
 *
 * Nombres de campos "flags" si se usarán en inglés para el flujo de la lectura del código, y son populares en software.
 * Ejémplos de esto serían campos como 'id,'status', 'created', 'updated',
 */

/**
 * Proveedor
 *
 * @aka Vendedor, entidad vendedora
 *
 * @todo el ID debería de ser el NIT?
 */
DROP TABLE IF EXISTS proveedor;
CREATE TABLE proveedor (
    id            int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
    , nombre        varchar(128) NOT NULL DEFAULT ''
    , nit
    , nombre_comercial_1
    , nombre_comercial_2
    , statsus
    , adjudicado  bool NOT NULL,
    , participa_contrato_abierto

    , description varchar(256) NOT NULL DEFAULT ''
    , created     datetime      NOT NULL
    , updated     datetime     NOT NULL
    , foreign_id  INT UNSIGNED NOT NULL
    , FOREIGN KEY (foreign_id) REFERENCES foreign(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;