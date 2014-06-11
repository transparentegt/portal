/**
 * Estructura de la DB en MySQL para guardar los datos scrappeados de Guatecompras
 *
 *
 * Los nombres de las tablas y campos se ponen en español para que cualquier guatemalteco lo pueda entender. Los nombres
 * de los campos también son mas fáciles en español por el lingo legal
 *
 * Nombres de campos "flags" si se usarán en inglés para el flujo de la lectura del código, y son populares en software.
 * Ejémplos de esto serían campos como 'id', 'status', 'created', 'updated',
 *
 * Diccionario de términos
 *      - GTC : GuateCompras
 */


DROP TABLE IF EXISTS geo_departamentos;
CREATE TABLE geo_departamentos (
    id       int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
    , nombre varchar(128) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS geo_municipios;
CREATE TABLE geo_municipios (
    id                     int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
    , nombre               varchar(128) NOT NULL DEFAULT ''
    , id_geo_departamento  INT UNSIGNED NOT NULL
    , FOREIGN KEY (id_geo_departamento) REFERENCES geo_departamentos(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS domicilios;
CREATE TABLE domicilios (
    id             int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
    , id_municipio INT UNSIGNED NOT NULL
    , FOREIGN KEY (id_municipio) REFERENCES geo_municipios(id) ON DELETE CASCADE ON UPDATE CASCADE
    , dirección    varchar(255) NOT NULL DEFAULT ''
    , telefonos    varchar(255) DEFAULT NULL -- meter todos en un solo campo, porque pelan
    , fax          varchar(255) DEFAULT NULL
    , updated      datetime -- última fecha de actualización
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/**
 * Proveedor
 *
 * Datos parseables que no se guardan en la base de datos
 * - adjudicado:
 *      bool si tiene o no aprobado un proyecto, valor calculado por JOIN a otra tabla
 * - participa en contrato abierto:
 *      bool si tiene o no registros en la tabla (no existente) de productos, valor calculado por JOIN a otra tabla
 *
 * @aka Vendedor, entidad vendedora
 *
 * @link http://guatecompras.gt/info/preguntasFrecProv.aspx#_lbl10
 */
DROP TABLE IF EXISTS proveedores;
CREATE TABLE proveedores (
    id                       int UNSIGNED NOT NULL PRIMARY KEY -- no es autoincrement para usaar el mismo ID que en GTC
    , nombre                 varchar(128) NOT NULL DEFAULT ''
    , nit                    varchar(16)  NOT NULL DEFAULT ''
    , nombre_comercial_1     varchar(128) DEFAULT NULL
    , nombre_comercial_2     varchar(128) DEFAULT NULL  -- no estoy seguros se se pueden obtener más de 2 nombres comerciales por esta vía
    , status                 bool DEFAULT true          -- GTC: HABILITADO / INHABILITADO
    , tiene_acceso_sistema   bool NOT NULL              -- En GTC se muestra como CON/SIN CONTRASEÑA
    , id_domicilio_fiscal    int UNSIGNED NOT NULL
    , FOREIGN KEY (id_domicilio_fiscal) REFERENCES domicilios(id) ON DELETE CASCADE ON UPDATE CASCADE
    , id_domicilio_comercial  int UNSIGNED NOT NULL
    , FOREIGN KEY (id_domicilio_comercial) REFERENCES domicilios(id) ON DELETE CASCADE ON UPDATE CASCADE
    , url                     varchar(255) DEFAULT NULL -- está en domicilio comercial, pero no queremos meter eso en la tabla domicilios
    , email                   varchar(255) DEFAULT NULL -- está en domicilio comercial, pero no queremos meter eso en la tabla domicilios
    , rep_legales_updated     datetime                  -- última fecha que se actualizaron los representantes legales
    , tipo_organización       varchar(64)  DEFAULT NULL -- sociedad anónima, tiene que ser un listado limitado
    , const_num_escritura     int UNSIGNED NOT NULL DEFAULT 0    -- número de escritura de constitucioń (WTF? será número entero)
    , const_fecha             date
    , inscripción_provisional date
    , inscripción_definitiva  date
    , inscripción_sat         date
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
 * Representante Legal
 *
 * La estructura encontrada en GTC mostró que se usa la misma vista y la misma data tanto para repslegales como para
 * los proveedores
 *
 * @aka rep legal
 *
 * @todo el ID debería de ser el NIT?
 */
DROP TABLE IF EXISTS representante_legal;
CREATE TABLE representante_legal (
    id            int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
    , nombre        varchar(128) NOT NULL DEFAULT ''
    , nit                 varchar(32)  NOT NULL DEFAULT ''
    , nombre_comercial_1
    , nombre_comercial_2
    , statsus
    , tiene_acceso_sistema bool NOT NULL, -- En GTC se muestra como CON/SIN CONTRASEÑA
    , id_domicilio_fiscal
    , FOREIGN KEY (id_domicilo_fiscal) REFERENCES domicilios(id) ON DELETE CASCADE ON UPDATE CASCADE
    , id_domicilio_comercial
    , FOREIGN KEY (id_domicilo_comercial) REFERENCES domicilios(id) ON DELETE CASCADE ON UPDATE CASCADE
    , url                 varchar(255) NOT NULL DEFAULT '',
    , email               varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/**
 * Relación de muchos a muchos. Un representante legal puede representar muchas empresas. Una empresa puede estar
 * representada por muchos representantes.
 */
DROP TABLE IF EXISTS provevedor_representado_por;
CREATE TABLE provevedor_representado_por (
    , id_proveedor INT UNSIGNED NOT NULL
    , FOREIGN KEY (proveedor_id)           REFERENCES foreign(id) ON DELETE CASCADE ON UPDATE CASCADE
    , id_representante_legal  INT UNSIGNED NOT NULL
    , FOREIGN KEY (id_representante_legal) REFERENCES foreign(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;