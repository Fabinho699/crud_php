-- =========================================================
--  Base de datos del proyecto CRUD de usuarios (PostgreSQL)
-- =========================================================
--
-- Paso 1: conectado a la base "postgres", crea la base de datos:

CREATE DATABASE actividad;

-- Paso 2: conéctate a la base "actividad" y ejecuta lo siguiente.
-- (La aplicación también crea la tabla automáticamente si no existe.)

CREATE TABLE IF NOT EXISTS usuarios (
    id        SERIAL PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    correo    VARCHAR(150),
    edad      INT CHECK (edad BETWEEN 1 AND 120),
    creado_en TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Datos de ejemplo (opcional)
INSERT INTO usuarios (nombre, correo, edad) VALUES
    ('Juliana Rojas',   'juliana.rojas@correo.com',   23),
    ('Andrés Castillo', 'andres.castillo@correo.com', 31),
    ('Valentina Gómez', NULL,                         19);
