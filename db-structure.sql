-- MySQL dump 10.13  Distrib 5.5.28, for Linux (x86_64)
--
-- Host: localhost    Database: flogeek
-- ------------------------------------------------------
-- Server version	5.5.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actividad`
--

DROP TABLE IF EXISTS `actividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actividad` (
  `actividad_id` int(11) NOT NULL AUTO_INCREMENT,
  `actividad_tipo` int(11) NOT NULL,
  `actividad_fecha` datetime NOT NULL,
  `actividad_usuario_usuario` varchar(32) COLLATE latin1_german2_ci NOT NULL,
  `actividad_usuario_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `actividad_checada` tinyint(1) NOT NULL DEFAULT '0',
  `actividad_valor` int(11) NOT NULL,
  PRIMARY KEY (`actividad_id`),
  KEY `actividad_fecha` (`actividad_fecha`)
) ENGINE=MyISAM AUTO_INCREMENT=39764 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amigos`
--

DROP TABLE IF EXISTS `amigos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amigos` (
  `relacion_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `amigo_id` int(11) NOT NULL,
  `amigo_confirmado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`relacion_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1758 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE latin1_german2_ci NOT NULL,
  `correo` varchar(200) COLLATE latin1_german2_ci NOT NULL,
  `telefono` varchar(50) COLLATE latin1_german2_ci NOT NULL,
  `comentarios` varchar(500) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comentarios` (
  `comentario_id` int(11) NOT NULL AUTO_INCREMENT,
  `comentario_comentario` text CHARACTER SET latin1 NOT NULL,
  `comentario_fecha` datetime NOT NULL,
  `comentario_host` varchar(255) CHARACTER SET latin1 NOT NULL,
  `foto_id` bigint(20) DEFAULT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `usuario_usuario` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`comentario_id`),
  KEY `foto_id` (`foto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33710 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `estadisticas`
--

DROP TABLE IF EXISTS `estadisticas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estadisticas` (
  `usuario_id` int(11) NOT NULL DEFAULT '0',
  `estadistica_voto_recp` int(11) NOT NULL DEFAULT '0',
  `estadistica_voto_recn` int(11) NOT NULL DEFAULT '0',
  `estadistica_voto_dadp` int(11) NOT NULL DEFAULT '0',
  `estadistica_voto_dadd` int(11) NOT NULL DEFAULT '0',
  `estadistica_fotos` int(11) NOT NULL DEFAULT '0',
  `estadistica_visitas` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `estilos`
--

DROP TABLE IF EXISTS `estilos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estilos` (
  `estilo_id` varchar(32) NOT NULL,
  `estilo_nombre` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`estilo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fotos`
--

DROP TABLE IF EXISTS `fotos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fotos` (
  `foto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) DEFAULT NULL,
  `foto_titulo` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `foto_descripcion` text CHARACTER SET latin1,
  `foto_visitas` int(11) NOT NULL DEFAULT '0',
  `foto_comentarios` int(11) NOT NULL DEFAULT '0',
  `foto_voto_si` int(11) NOT NULL DEFAULT '0',
  `foto_voto_no` int(11) NOT NULL DEFAULT '0',
  `foto_es_video` tinyint(1) NOT NULL,
  `foto_video_id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `foto_video_tipo` tinyint(4) NOT NULL,
  `foto_tags` text CHARACTER SET latin1 NOT NULL,
  `foto_fecha` datetime NOT NULL,
  `foto_dia` smallint(6) NOT NULL,
  `foto_lote` int(11) NOT NULL DEFAULT '1',
  `ancho` int(11) NOT NULL,
  `alto` int(11) NOT NULL,
  PRIMARY KEY (`foto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5795 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fotos_notas`
--

DROP TABLE IF EXISTS `fotos_notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fotos_notas` (
  `id` char(32) NOT NULL,
  `id_foto` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `ancho` int(11) NOT NULL,
  `alto` int(11) NOT NULL,
  `nota` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `galletas`
--

DROP TABLE IF EXISTS `galletas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galletas` (
  `galleta_id` varchar(50) COLLATE latin1_german2_ci NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`galleta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paises` (
  `pais_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `pais_nombre` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`pais_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `usuario_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha_alta` datetime NOT NULL,
  `usuario_tipo` tinyint(4) NOT NULL DEFAULT '0',
  `usuario_usuario` varchar(32) DEFAULT NULL,
  `usuario_nombre` varchar(48) DEFAULT NULL,
  `usuario_email` varchar(100) DEFAULT NULL,
  `usuario_password` varchar(100) DEFAULT NULL,
  `usuario_ciudad` varchar(255) NOT NULL,
  `usuario_estilo` varchar(32) DEFAULT NULL,
  `usuario_titulo` varchar(255) DEFAULT NULL,
  `usuario_visitas` int(11) NOT NULL DEFAULT '0',
  `usuario_fotos` int(11) NOT NULL DEFAULT '0',
  `usuario_foto_ultima_id` int(11) NOT NULL DEFAULT '0',
  `usuario_votos_recibidos_positivos` int(11) NOT NULL,
  `usuario_votos_recibidos_negativos` int(11) NOT NULL,
  `usuario_amigos` int(11) NOT NULL DEFAULT '0',
  `usuario_notificacion_comentario` tinyint(1) NOT NULL DEFAULT '1',
  `usuario_notificacion_foto` tinyint(1) NOT NULL DEFAULT '1',
  `usuario_notificacion_conversacion` tinyint(1) NOT NULL DEFAULT '1',
  `usuario_notificacion_amigo` tinyint(1) NOT NULL DEFAULT '1',
  `usuario_tema_ajeno` tinyint(1) NOT NULL,
  `usuario_lote` int(11) NOT NULL,
  `foto_fecha` int(11) NOT NULL,
  `pais_id` smallint(6) NOT NULL DEFAULT '0',
  `usuario_fecha_nacimiento` datetime DEFAULT NULL,
  PRIMARY KEY (`usuario_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `usuario_usuario` (`usuario_usuario`),
  KEY `foto_fecha` (`foto_fecha`)
) ENGINE=MyISAM AUTO_INCREMENT=309 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_recuperaciones`
--

DROP TABLE IF EXISTS `usuarios_recuperaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_recuperaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `token_a` char(32) NOT NULL,
  `token_b` char(32) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` char(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votacion`
--

DROP TABLE IF EXISTS `votacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votacion` (
  `usuario_id` int(11) NOT NULL,
  `foto_id` int(11) NOT NULL,
  `votacion_voto` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
