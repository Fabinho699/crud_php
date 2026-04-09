CREATE DATABASE mi_app;

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    edad INT
);

DROP DATABASE mi_app;

DROP TABLE usuarios;

SELECT * FROM usuarios;