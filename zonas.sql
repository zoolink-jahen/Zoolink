-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 25-02-2025 a las 19:52:54
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `zoo_parque_del_pueblo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonas`
--

CREATE TABLE `zonas` (
  `ZonasID` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Descripción` text DEFAULT NULL,
  `Fotografia` varchar(255) DEFAULT NULL,
  `Coordenadas` varchar(100) DEFAULT NULL,
  `Tooltip` varchar(255) DEFAULT NULL,
  `Enlace` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `zonas`
--

INSERT INTO `zonas` (`ZonasID`, `Nombre`, `Descripción`, `Fotografia`, `Coordenadas`, `Tooltip`, `Enlace`) VALUES
(1, 'Zona África', 'Esta zona está dedicada a uno de los felinos más majestuosos del mundo, el tigre. En este hábitat, los tigres tienen amplias áreas donde pueden moverse con libertad, y los visitantes pueden observarlos de cerca en un entorno que simula su hábitat natural. Además, se ofrece información sobre los esfuerzos de conservación de esta especie en peligro de extinción.', 'uploads/67b4dbd234b7e_LANCHAS.jpg', '100,200', 'Visita la Zona África', 'https://www.ifaw.org/journal/20-most-fascinating-animals-asia'),
(2, 'Zona Asia', 'En el Zoológico del v', 'uploads/67b4dc18d7269_aves-boris.jpg', '338,199', 'Visita la Zona Asia', 'https://www.ifaw.org/journal/20-most-fascinating-animals-asia'),
(3, 'Zona América', 'Zona donde los visitantes pueden observar diferentes especies de osos en un hábitat que simula su entorno natural, con bosques, zonas rocosas y pequeños lagos. Este espacio está enfocado en la conservación y educación sobre estos animales.', 'uploads/67b4dc2a0a387_AVIARIO.jpg', '347,431', 'Visita la Zona América', 'https://www.ifaw.org/journal/20-most-fascinating-animals-asia'),
(17, 'Zona de Aviario', 'Un lugar diseñado para albergar una gran variedad de aves, desde especies locales hasta exóticas. Los visitantes pueden caminar entre los árboles y observar aves en su hábitat natural, disfrutando de su colorido plumaje y canto.', 'uploads/67b26c3f1f923_AVIARIO.jpg', '800,200', 'Visita nuestra zona de aves totalmente gratis', 'https://es.wikipedia.org/wiki/Aviario'),
(18, 'Zona de Acuario', 'Espacio destinado a la vida marina, donde se pueden observar diversas especies acuáticas como peces tropicales, tiburones, rayas y corales. Ofrece una experiencia educativa sobre los ecosistemas marinos y su conservación.', 'uploads/67b41cb3f3fde_ACUARIO.jpg', '191,483', 'Visita Nuestra zona de Acuario', 'https://www.ifaw.org/regierjgoeoirj/20-most-fascinating-animals-asia'),
(19, 'Zona de Lanchas', 'Zona donde los visitantes pueden disfrutar de paseos en lancha por los lagos del parque. Un recorrido tranquilo en el que se pueden observar desde otra perspectiva las diferentes especies animales y plantas que habitan en el agua.', 'uploads/67b26c4061708_LANCHAS.jpg', '609,498', 'Visita nuestar zoan de lanchas en donde te podras divertir', 'https://www.ifaw.org/journal/20-most-fascinating-animals-asia'),
(22, 'gJUAN', 'Esta zona está dedicada a uno de los felinos más majestuosos del mundo, el tigre. En este hábitat, los tigres tienen amplias áreas donde pueden moverse con libertad, y los visitantes pueden observarlos de cerca en un entorno que simula su hábitat natural. Además, se ofrece información sobre los esfuerzos de conservación de esta especie en peligro de extinción.', 'uploads/67b2a1192005d_ACUARIO.jpg', '739,412', '6ytr', 'https://claude.ai/chat/fb7db924-2944-4df9-8b38-0e2eac8198be');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `zonas`
--
ALTER TABLE `zonas`
  ADD PRIMARY KEY (`ZonasID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `zonas`
--
ALTER TABLE `zonas`
  MODIFY `ZonasID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
