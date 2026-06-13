CREATE DATABASE IF NOT EXISTS db_blog_personal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'franco'@'%' IDENTIFIED BY 'Franco36339372!';
GRANT ALL PRIVILEGES ON db_blog_personal.* TO 'franco'@'%';
FLUSH PRIVILEGES;

USE db_blog_personal;

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO posts (titulo, contenido)
SELECT 'Mi Primer Post', 'Bienvenidos a mi blog personal. Aquí voy a compartir mis avances del Trabajo Final Integrador.'
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE titulo = 'Mi Primer Post');
