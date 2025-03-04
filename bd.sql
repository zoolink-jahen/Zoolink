CREATE DATABASE IF NOT EXISTS zoologico_final;
USE zoologico_final;

CREATE TABLE IF NOT EXISTS zoologico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    logo BLOB NOT NULL,
    descripcion TEXT NOT NULL,
    horarios VARCHAR(255) NOT NULL,
    ubicacion TEXT NOT NULL,
    contacto VARCHAR(50) NOT NULL,
    reglas TEXT NOT NULL
);

-- Insertar un registro por defecto (opcional)
INSERT INTO zoologico (nombre, logo, descripcion, horarios, ubicacion, contacto, reglas) VALUES
('Parque del Pueblo', '', 'Un hermoso zoológico con muchas especies.', '9:00 AM - 6:00 PM', 'Dirección del zoológico', '123-456-7890', 'No alimentar a los animales, no correr, seguir las instrucciones del personal.');
