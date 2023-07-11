-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_comanda
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `descripcion` (`descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

LOCK TABLES `area` WRITE;
/*!40000 ALTER TABLE `area` DISABLE KEYS */;
INSERT INTO `area` VALUES (1,'Administracion','2022-06-12',NULL,NULL),(2,'Salon','2022-06-12',NULL,NULL),(3,'Barra de Tragos y Vinos','2022-06-12',NULL,NULL),(4,'Barra de Choperas de Cerveza','2022-06-12',NULL,NULL),(5,'Cocina','2022-06-12',NULL,NULL),(6,'Candy Bar','2022-06-12',NULL,NULL);
/*!40000 ALTER TABLE `area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesa`
--

DROP TABLE IF EXISTS `mesa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idMesaEstado` int(11) NOT NULL DEFAULT 4,
  `codigo` varchar(5) COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `FK_mesa_mesaEstado` (`idMesaEstado`),
  CONSTRAINT `FK_mesa_mesaEstado` FOREIGN KEY (`idMesaEstado`) REFERENCES `mesaestado` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesa`
--

LOCK TABLES `mesa` WRITE;
/*!40000 ALTER TABLE `mesa` DISABLE KEYS */;
INSERT INTO `mesa` VALUES (2,4,'lLIKY','Mesa junto al baño de hombres, capacidad para 2','2022-06-12','2022-06-18',NULL),(3,4,'NvxMm','Mesa junto a la cocina, capacidad para 6','2022-06-12','2022-06-27',NULL),(4,4,'1Io77','Mesa especial cumpleaños, aislada con espacios para movilidad cómoda','2022-06-12','2022-06-20',NULL),(5,4,'6qX07','Mesa en esquina, lugar oscuro, capacidad para 2 personas','2022-06-12','2022-06-27',NULL),(10,4,'u59sh','Mesa de prueba','2022-06-19','2022-06-19','2022-06-19');
/*!40000 ALTER TABLE `mesa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesaestado`
--

DROP TABLE IF EXISTS `mesaestado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesaestado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcionEstado` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `descripcionEstado` (`descripcionEstado`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesaestado`
--

LOCK TABLES `mesaestado` WRITE;
/*!40000 ALTER TABLE `mesaestado` DISABLE KEYS */;
INSERT INTO `mesaestado` VALUES (1,'Con cliente esperando pedido','2022-06-12',NULL,NULL),(2,'Con cliente comiendo','2022-06-12',NULL,NULL),(3,'Con cliente pagando','2022-06-12',NULL,NULL),(4,'Cerrada','2022-06-12',NULL,NULL);
/*!40000 ALTER TABLE `mesaestado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(5) COLLATE utf8_spanish2_ci NOT NULL,
  `idMesa` int(11) NOT NULL,
  `idPedidoEstado` int(11) NOT NULL,
  `idUsuarioMozo` int(11) NOT NULL,
  `idUsuarioSocio` int(11) DEFAULT NULL,
  `nombreCliente` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `foto` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `FK_pedido_usuarioMozo` (`idUsuarioMozo`),
  KEY `FK_pedido_usuarioSocio` (`idUsuarioSocio`),
  KEY `FK_pedido_pedidoEstado` (`idPedidoEstado`),
  KEY `FK_pedido_mesa` (`idMesa`),
  CONSTRAINT `FK_pedido_mesa` FOREIGN KEY (`idMesa`) REFERENCES `mesa` (`id`),
  CONSTRAINT `FK_pedido_pedidoEstado` FOREIGN KEY (`idPedidoEstado`) REFERENCES `pedidoestado` (`id`),
  CONSTRAINT `FK_pedido_usuarioMozo` FOREIGN KEY (`idUsuarioMozo`) REFERENCES `usuario` (`id`),
  CONSTRAINT `FK_pedido_usuarioSocio` FOREIGN KEY (`idUsuarioSocio`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (11,'Bv8mf',3,6,5,NULL,'Recorrido Final','./imagenes/clientes/11.png',2750,'2022-06-27','2022-06-27',NULL),(12,'glr7C',5,6,5,NULL,'Valentin','./imagenes/clientes/12.png',2400,'2022-06-27','2022-06-27',NULL),(13,'73ueu',5,6,5,NULL,'Lionel Messi','./imagenes/clientes/13.png',3850,'2022-06-27','2022-06-27',NULL),(14,'4ST5j',5,1,5,NULL,'Cancelar',NULL,500,'2022-06-27','2022-06-27',NULL);
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidodetalle`
--

DROP TABLE IF EXISTS `pedidodetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidodetalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPedido` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idPedidoEstado` int(11) NOT NULL,
  `idUsuarioEncargado` int(11) DEFAULT NULL,
  `cantidadProducto` int(11) NOT NULL DEFAULT 1,
  `tiempoEstimado` int(11) DEFAULT NULL,
  `tiempoInicio` varchar(8) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `tiempoFin` varchar(8) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pedidoDetalle_pedido` (`idPedido`),
  KEY `FK_pedidoDetalle_producto` (`idProducto`),
  KEY `FK_pedidoDetalle_pedidoEstado` (`idPedidoEstado`),
  KEY `FK_pedidoDetalle_usuario` (`idUsuarioEncargado`),
  CONSTRAINT `FK_pedidoDetalle_pedido` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`id`),
  CONSTRAINT `FK_pedidoDetalle_pedidoEstado` FOREIGN KEY (`idPedidoEstado`) REFERENCES `pedidoestado` (`id`),
  CONSTRAINT `FK_pedidoDetalle_producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`id`),
  CONSTRAINT `FK_pedidoDetalle_usuario` FOREIGN KEY (`idUsuarioEncargado`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidodetalle`
--

LOCK TABLES `pedidodetalle` WRITE;
/*!40000 ALTER TABLE `pedidodetalle` DISABLE KEYS */;
INSERT INTO `pedidodetalle` VALUES (30,11,1,5,5,1,25,'if(count','01:38:30','2022-06-27','2022-06-27',NULL),(31,11,2,5,5,2,25,'01:36:34','01:49:53','2022-06-27','2022-06-27',NULL),(32,11,6,5,5,1,5,'01:16:16','01:21:54','2022-06-27','2022-06-27',NULL),(33,11,5,5,5,1,3,'01:15:46','01:18:44','2022-06-27','2022-06-27',NULL),(34,12,4,5,5,2,5,'04:15:21','04:55:41','2022-06-27','2022-06-27',NULL),(35,12,23,5,5,2,25,'04:15:07','04:56:32','2022-06-27','2022-06-27',NULL),(36,13,9,5,5,3,25,'04:40:43','04:45:53','2022-06-27','2022-06-27',NULL),(37,13,24,5,5,1,25,'04:40:45','04:44:53','2022-06-27','2022-06-27',NULL),(38,13,5,5,5,3,3,'04:42:30','04:45:16','2022-06-27','2022-06-27',NULL),(39,13,22,5,5,2,5,'04:44:11','04:44:42','2022-06-27','2022-06-27',NULL),(40,14,21,4,11,3,0,NULL,NULL,'2022-06-27','2022-06-27',NULL);
/*!40000 ALTER TABLE `pedidodetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidoencuesta`
--

DROP TABLE IF EXISTS `pedidoencuesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidoencuesta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idMesa` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `puntajeMesa` int(11) NOT NULL,
  `puntajeRestaurante` int(11) NOT NULL,
  `puntajeMozo` int(11) NOT NULL,
  `puntajeCocinero` int(11) NOT NULL,
  `comentario` varchar(66) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pedidoEncuesta_mesa` (`idMesa`),
  KEY `FK_pedidoEncuesta_pedido` (`idPedido`),
  CONSTRAINT `FK_pedidoEncuesta_mesa` FOREIGN KEY (`idMesa`) REFERENCES `mesa` (`id`),
  CONSTRAINT `FK_pedidoEncuesta_pedido` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidoencuesta`
--

LOCK TABLES `pedidoencuesta` WRITE;
/*!40000 ALTER TABLE `pedidoencuesta` DISABLE KEYS */;
INSERT INTO `pedidoencuesta` VALUES (1,3,11,8,9,10,8,'Excelente lugar, y atencion. Volveria a venir','2022-06-27','2022-06-27',NULL),(2,5,12,4,6,5,5,'No volveria a venir, tardaron mucho y el vino parecia abierto','2022-06-27','2022-06-27',NULL),(3,5,13,10,10,10,10,'No la pude pasar mejor con toda mi familia, gracias!','2022-06-27','2022-06-27',NULL);
/*!40000 ALTER TABLE `pedidoencuesta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidoestado`
--

DROP TABLE IF EXISTS `pedidoestado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidoestado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidoestado`
--

LOCK TABLES `pedidoestado` WRITE;
/*!40000 ALTER TABLE `pedidoestado` DISABLE KEYS */;
INSERT INTO `pedidoestado` VALUES (1,'Pendiente','2022-06-12',NULL,NULL),(2,'En preparacion','2022-06-12',NULL,NULL),(3,'Listo para servir','2022-06-12',NULL,NULL),(4,'Cancelado','2022-06-12',NULL,NULL),(5,'Servido','2022-06-12',NULL,NULL),(6,'Cobrado','2022-06-12',NULL,NULL);
/*!40000 ALTER TABLE `pedidoestado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idArea` int(11) NOT NULL,
  `idProductoTipo` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8_spanish2_ci NOT NULL,
  `precio` float NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `tiempoEstimado` int(11) DEFAULT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `FK_producto_productoTipo` (`idProductoTipo`),
  KEY `FK_producto_area` (`idArea`),
  CONSTRAINT `FK_producto_area` FOREIGN KEY (`idArea`) REFERENCES `area` (`id`),
  CONSTRAINT `FK_producto_productoTipo` FOREIGN KEY (`idProductoTipo`) REFERENCES `productotipo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES (1,5,1,'Milanesa a caballo',850,100,20,'2022-06-12',NULL,NULL),(2,5,1,'Hamburguesa de Garbanzo',750,50,15,'2022-06-12',NULL,NULL),(3,5,1,'Empanada',150,100,10,'2022-06-12',NULL,NULL),(4,3,2,'Don Valentin 750cc',1200,50,5,'2022-06-12',NULL,NULL),(5,4,2,'Corona 450cc',650,50,5,'2022-06-12',NULL,NULL),(6,3,2,'Daikiri',500,60,10,'2022-06-12',NULL,NULL),(7,6,1,'Flan casero',350,80,10,'2022-06-12',NULL,NULL),(8,6,2,'Helado Tricolor (chocolate - vainilla - frutilla)',450,100,7,'2022-06-12',NULL,NULL),(9,5,1,'Pizza Muzzarella',1100,60,20,'2022-06-13','2022-06-13',NULL),(21,3,2,'Coca cola 1L',500,100,5,'2022-06-27','2022-06-27',NULL),(22,3,2,'Sprite 1L',500,100,5,'2022-06-27','2022-06-27',NULL),(23,5,1,'Pollo al horno con papas',1200,50,25,'2022-06-27','2022-06-27',NULL),(24,5,1,'Pizza Anchoa',1600,50,18,'2022-06-27','2022-06-27',NULL);
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productotipo`
--

DROP TABLE IF EXISTS `productotipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productotipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productotipo`
--

LOCK TABLES `productotipo` WRITE;
/*!40000 ALTER TABLE `productotipo` DISABLE KEYS */;
INSERT INTO `productotipo` VALUES (1,'comida','2022-06-12',NULL,NULL),(2,'bebida','2022-06-12',NULL,NULL);
/*!40000 ALTER TABLE `productotipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuarioTipo` int(11) NOT NULL,
  `idArea` int(11) NOT NULL,
  `usuario` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `clave` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `FK_usuario_usuarioTipo` (`idUsuarioTipo`),
  KEY `FK_usuario_area` (`idArea`),
  CONSTRAINT `FK_usuario_area` FOREIGN KEY (`idArea`) REFERENCES `area` (`id`),
  CONSTRAINT `FK_usuario_usuarioTipo` FOREIGN KEY (`idUsuarioTipo`) REFERENCES `usuariotipo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,1,1,'laplume','super123',1,'2022-06-12',NULL,NULL),(2,2,1,'socio1','so111',1,'2022-06-12',NULL,NULL),(3,2,1,'socio2','so222',1,'2022-06-12',NULL,NULL),(4,2,1,'socio3','so333',1,'2022-06-12',NULL,NULL),(5,3,2,'mozo1','mo111',1,'2022-06-12',NULL,NULL),(6,3,2,'mozo2','mo222',1,'2022-06-12',NULL,NULL),(8,6,5,'cocinero1','co111',1,'2022-06-12',NULL,NULL),(9,6,5,'cocinero2','co222',1,'2022-06-12',NULL,NULL),(11,4,3,'bartender1','bar111',1,'2022-06-12',NULL,NULL),(12,4,3,'bartender2','bar222',1,'2022-06-12',NULL,NULL),(13,5,4,'cervecero1','cer111',1,'2022-06-12',NULL,NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarioaccion`
--

DROP TABLE IF EXISTS `usuarioaccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarioaccion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `idUsuarioAccionTipo` int(11) NOT NULL,
  `idPedido` int(11) DEFAULT NULL,
  `idPedidoDetalle` int(11) DEFAULT NULL,
  `idMesa` int(11) DEFAULT NULL,
  `idProducto` int(11) DEFAULT NULL,
  `idArea` int(11) DEFAULT NULL,
  `hora` varchar(8) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_usuarioAccion_usuario` (`idUsuario`),
  KEY `FK_usuarioAccion_UsuarioAccionTipo` (`idUsuarioAccionTipo`),
  KEY `FK_usuarioAccion_pedido` (`idPedido`),
  KEY `FK_usuarioAccion_pedidoDetalle` (`idPedidoDetalle`),
  KEY `FK_usuarioAccion_mesa` (`idMesa`),
  KEY `FK_usuarioAccion_producto` (`idProducto`),
  KEY `FK_usuarioAccion_area` (`idArea`),
  CONSTRAINT `FK_usuarioAccion_UsuarioAccionTipo` FOREIGN KEY (`idUsuarioAccionTipo`) REFERENCES `usuarioacciontipo` (`id`),
  CONSTRAINT `FK_usuarioAccion_area` FOREIGN KEY (`idArea`) REFERENCES `area` (`id`),
  CONSTRAINT `FK_usuarioAccion_mesa` FOREIGN KEY (`idMesa`) REFERENCES `mesa` (`id`),
  CONSTRAINT `FK_usuarioAccion_pedido` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`id`),
  CONSTRAINT `FK_usuarioAccion_pedidoDetalle` FOREIGN KEY (`idPedidoDetalle`) REFERENCES `pedidodetalle` (`id`),
  CONSTRAINT `FK_usuarioAccion_producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`id`),
  CONSTRAINT `FK_usuarioAccion_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarioaccion`
--

LOCK TABLES `usuarioaccion` WRITE;
/*!40000 ALTER TABLE `usuarioaccion` DISABLE KEYS */;
INSERT INTO `usuarioaccion` VALUES (160,1,6,NULL,NULL,NULL,NULL,NULL,'01:05:51','2022-06-27','2022-06-27',NULL),(161,5,2,11,NULL,NULL,NULL,NULL,'01:09:26','2022-06-27','2022-06-27',NULL),(162,5,5,11,NULL,NULL,NULL,NULL,'01:09:57','2022-06-27','2022-06-27',NULL),(163,8,1,NULL,NULL,NULL,NULL,NULL,'01:11:27','2022-06-27','2022-06-27',NULL),(164,8,4,NULL,30,NULL,NULL,NULL,'01:15:22','2022-06-27','2022-06-27',NULL),(165,13,4,NULL,33,NULL,NULL,NULL,'01:15:47','2022-06-27','2022-06-27',NULL),(166,11,4,NULL,32,NULL,NULL,NULL,'01:16:16','2022-06-27','2022-06-27',NULL),(167,8,4,NULL,30,NULL,NULL,NULL,'01:18:30','2022-06-27','2022-06-27',NULL),(168,13,4,NULL,33,NULL,NULL,NULL,'01:18:44','2022-06-27','2022-06-27',NULL),(169,11,4,NULL,32,NULL,NULL,NULL,'01:18:54','2022-06-27','2022-06-27',NULL),(172,5,4,NULL,30,NULL,NULL,NULL,'01:23:09','2022-06-27','2022-06-27',NULL),(173,5,4,NULL,33,NULL,NULL,NULL,'01:23:16','2022-06-27','2022-06-27',NULL),(174,5,4,NULL,32,NULL,NULL,NULL,'01:23:32','2022-06-27','2022-06-27',NULL),(175,8,4,NULL,31,NULL,NULL,NULL,'01:36:34','2022-06-27','2022-06-27',NULL),(176,1,2,NULL,NULL,NULL,24,NULL,'01:48:56','2022-06-27','2022-06-27',NULL),(177,8,4,NULL,31,NULL,NULL,NULL,'01:49:53','2022-06-27','2022-06-27',NULL),(178,5,4,NULL,31,NULL,NULL,NULL,'01:51:02','2022-06-27','2022-06-27',NULL),(179,1,4,NULL,NULL,3,NULL,NULL,'01:51:10','2022-06-27','2022-06-27',NULL),(180,2,1,NULL,NULL,NULL,NULL,NULL,'02:00:02','2022-06-27','2022-06-27',NULL),(181,1,4,NULL,NULL,3,NULL,NULL,'02:02:29','2022-06-27','2022-06-27',NULL),(182,1,4,11,NULL,NULL,NULL,NULL,'02:15:55','2022-06-27','2022-06-27',NULL),(183,2,4,NULL,NULL,3,NULL,NULL,'02:17:58','2022-06-27','2022-06-27',NULL),(184,5,2,12,NULL,NULL,NULL,NULL,'04:10:10','2022-06-27','2022-06-27',NULL),(185,5,5,12,NULL,NULL,NULL,NULL,'04:10:55','2022-06-27','2022-06-27',NULL),(186,8,4,NULL,35,NULL,NULL,NULL,'04:15:07','2022-06-27','2022-06-27',NULL),(187,11,4,NULL,34,NULL,NULL,NULL,'04:15:21','2022-06-27','2022-06-27',NULL),(188,11,4,NULL,34,NULL,NULL,NULL,'04:15:41','2022-06-27','2022-06-27',NULL),(189,8,4,NULL,35,NULL,NULL,NULL,'04:16:32','2022-06-27','2022-06-27',NULL),(190,5,4,NULL,35,NULL,NULL,NULL,'04:17:23','2022-06-27','2022-06-27',NULL),(191,5,4,NULL,34,NULL,NULL,NULL,'04:17:27','2022-06-27','2022-06-27',NULL),(192,1,4,NULL,NULL,5,NULL,NULL,'04:17:52','2022-06-27','2022-06-27',NULL),(193,1,4,NULL,NULL,5,NULL,NULL,'04:18:02','2022-06-27','2022-06-27',NULL),(194,1,4,12,NULL,NULL,NULL,NULL,'04:18:17','2022-06-27','2022-06-27',NULL),(195,2,4,NULL,NULL,5,NULL,NULL,'04:18:23','2022-06-27','2022-06-27',NULL),(196,5,2,13,NULL,NULL,NULL,NULL,'04:39:23','2022-06-27','2022-06-27',NULL),(197,5,5,13,NULL,NULL,NULL,NULL,'04:40:25','2022-06-27','2022-06-27',NULL),(198,8,4,NULL,36,NULL,NULL,NULL,'04:40:43','2022-06-27','2022-06-27',NULL),(199,8,4,NULL,37,NULL,NULL,NULL,'04:40:45','2022-06-27','2022-06-27',NULL),(200,11,4,NULL,39,NULL,NULL,NULL,'04:40:57','2022-06-27','2022-06-27',NULL),(201,13,4,NULL,38,NULL,NULL,NULL,'04:42:31','2022-06-27','2022-06-27',NULL),(202,11,4,NULL,39,NULL,NULL,NULL,'04:42:41','2022-06-27','2022-06-27',NULL),(203,11,4,NULL,39,NULL,NULL,NULL,'04:44:11','2022-06-27','2022-06-27',NULL),(204,11,4,NULL,39,NULL,NULL,NULL,'04:44:42','2022-06-27','2022-06-27',NULL),(205,8,4,NULL,37,NULL,NULL,NULL,'04:44:53','2022-06-27','2022-06-27',NULL),(206,13,4,NULL,38,NULL,NULL,NULL,'04:45:16','2022-06-27','2022-06-27',NULL),(207,8,4,NULL,36,NULL,NULL,NULL,'04:45:53','2022-06-27','2022-06-27',NULL),(208,5,4,NULL,36,NULL,NULL,NULL,'04:46:19','2022-06-27','2022-06-27',NULL),(209,5,4,NULL,37,NULL,NULL,NULL,'04:46:21','2022-06-27','2022-06-27',NULL),(210,5,4,NULL,38,NULL,NULL,NULL,'04:47:05','2022-06-27','2022-06-27',NULL),(211,5,4,NULL,39,NULL,NULL,NULL,'04:47:08','2022-06-27','2022-06-27',NULL),(212,1,4,NULL,NULL,5,NULL,NULL,'04:47:34','2022-06-27','2022-06-27',NULL),(213,1,4,NULL,NULL,5,NULL,NULL,'04:47:37','2022-06-27','2022-06-27',NULL),(214,1,4,13,NULL,NULL,NULL,NULL,'04:47:45','2022-06-27','2022-06-27',NULL),(215,2,4,NULL,NULL,5,NULL,NULL,'04:47:48','2022-06-27','2022-06-27',NULL),(216,5,2,14,NULL,NULL,NULL,NULL,'06:06:34','2022-06-27','2022-06-27',NULL),(217,11,4,NULL,40,NULL,NULL,NULL,'06:07:04','2022-06-27','2022-06-27',NULL),(218,2,4,NULL,NULL,5,NULL,NULL,'06:07:39','2022-06-27','2022-06-27',NULL);
/*!40000 ALTER TABLE `usuarioaccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarioacciontipo`
--

DROP TABLE IF EXISTS `usuarioacciontipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarioacciontipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarioacciontipo`
--

LOCK TABLES `usuarioacciontipo` WRITE;
/*!40000 ALTER TABLE `usuarioacciontipo` DISABLE KEYS */;
INSERT INTO `usuarioacciontipo` VALUES (1,'Login','2022-06-12',NULL,NULL),(2,'Alta','2022-06-12',NULL,NULL),(3,'Baja','2022-06-12',NULL,NULL),(4,'Modificacion','2022-06-12',NULL,NULL),(5,'CargaFoto','2022-06-12',NULL,NULL),(6,'CargaCSV','2022-06-12',NULL,NULL),(7,'DescargaCSV','2022-06-12',NULL,NULL);
/*!40000 ALTER TABLE `usuarioacciontipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuariotipo`
--

DROP TABLE IF EXISTS `usuariotipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuariotipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuariotipo`
--

LOCK TABLES `usuariotipo` WRITE;
/*!40000 ALTER TABLE `usuariotipo` DISABLE KEYS */;
INSERT INTO `usuariotipo` VALUES (1,'administrador','2022-06-12',NULL,NULL),(2,'socio','2022-06-12',NULL,NULL),(3,'mozo','2022-06-12',NULL,NULL),(4,'bartender','2022-06-12',NULL,NULL),(5,'cervecero','2022-06-12',NULL,NULL),(6,'cocinero','2022-06-12',NULL,NULL);
/*!40000 ALTER TABLE `usuariotipo` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-27 18:29:33
