SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de datos: `db_comanda`
CREATE DATABASE IF NOT EXISTS `db_comanda` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish2_ci;

USE `db_comanda`;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarioTipo`;

CREATE TABLE `usuarioTipo` (
  `id` int(11) NOT NULL,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
   UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `usuarioTipo` (`id`, `tipo`, `fechaAlta`) VALUES
(1, 'administrador', '2022-06-12 18:20:31'),
(2, 'socio', '2022-06-12 18:20:31'),
(3, 'mozo', '2022-06-12 18:20:31'),
(4, 'bartender', '2022-06-12 18:20:31'),
(5, 'cervecero', '2022-06-12 18:20:31'),
(6, 'cocinero', '2022-06-12 18:20:31');

ALTER TABLE `usuarioTipo`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usuarioTipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 6;
COMMIT;

----------------------------------------------------------------------
USE `db_comanda`;
DROP TABLE IF EXISTS `area`;

CREATE TABLE `area` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  UNIQUE KEY `descripcion` (`descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `area` (`id`, `descripcion`, `fechaAlta`) VALUES
(1, 'Administracion', '2022-06-12 18:20:31'),
(2, 'Salon', '2022-06-12 18:20:31'),
(3, 'Barra de Tragos y Vinos', '2022-06-12 18:20:31'),
(4, 'Barra de Choperas de Cerveza', '2022-06-12 18:20:31'),
(5, 'Cocina', '2022-06-12 18:20:31'),
(6, 'Candy Bar', '2022-06-12 18:20:31');

ALTER TABLE `area`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 6;
COMMIT;

----------------------------------------------------------------------
USE `db_comanda`;
DROP TABLE IF EXISTS `usuario`;

-- Estructura de tabla para la tabla `usuario`
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `idUsuarioTipo` int(11) NOT NULL,
  `idArea` int(11) NOT NULL,
  `usuario` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `clave` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  UNIQUE KEY `usuario` (`usuario`),
  FOREIGN KEY `FK_usuario_usuarioTipo` (`idUsuarioTipo`) REFERENCES `usuarioTipo` (`id`),
  FOREIGN KEY `FK_usuario_area` (`idArea`) REFERENCES `area` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- Volcado de datos para la tabla `usuario`
INSERT INTO `usuario` (`id`, `idUsuarioTipo`, `idArea`, `usuario`, `clave`, `estado`, `fechaAlta`) VALUES
(1, 1, 1, 'laplume', 'super123', 1, '2022-06-12 18::31'),
(2, 2, 1, 'socio1', 'so111', 1, '2022-06-12 18:20:31'),
(3, 2, 1, 'socio2', 'so222', 1, '2022-06-12 18:20:31'),
(4, 2, 1, 'socio3', 'so333', 1, '2022-06-12 18:20:31'),
(5, 3, 2, 'mozo1', 'mo111', 1, '2022-06-12 18:20:31'),
(6, 3, 2, 'mozo2', 'mo222', 1, '2022-06-12 18:20:31'),
(7, 3, 2, 'mozo3', 'mo333', 1, '2022-06-12 18:20:31'),
(8, 6, 5, 'cocinero1', 'co111', 1, '2022-06-12 18:20:31'),
(9, 6, 5, 'cocinero2', 'co222', 1, '2022-06-12 18:20:31'),
(10, 6, 5, 'cocinero3', 'co333', 1, '2022-06-12 18:20:31'),
(11, 4, 3, 'bartender1', 'bar111', 1, '2022-06-12 18:20:31'),
(12, 4, 3, 'bartender2', 'bar222', 1, '2022-06-12 18:20:31'),
(13, 1, 4, 'cervecero1', 'cer111', 1, '2022-06-12 18:20:31'),
(14, 1, 4, 'cervecero2', 'cer222', 1, '2022-06-12 18:20:31');

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 14;
COMMIT;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `mesaEstado`;

CREATE TABLE `mesaEstado` (
  `id` int(11) NOT NULL,
  `descripcionEstado` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  UNIQUE KEY `descripcionEstado` (`descripcionEstado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `mesaEstado` (`id`, `descripcionEstado`, `fechaAlta`) VALUES
(1, 'Con cliente esperando pedido', '2022-06-12 18:20:31'),
(2, 'Con cliente comiendo', '2022-06-12 18:20:31'),
(3, 'Con cliente pagando', '2022-06-12 18:20:31'),
(4, 'Cerrada', '2022-06-12 18:20:31');

ALTER TABLE `mesaEstado`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mesaEstado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 4;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `mesa`;

CREATE TABLE `mesa` (
  `id` int(11) NOT NULL,
  `idMesaEstado` int(11) COLLATE utf8_spanish2_ci NOT NULL DEFAULT 4,
  `codigo` varchar(5) COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_spanish2_ci NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  UNIQUE KEY `codigo` (`codigo`),
  FOREIGN KEY `FK_mesa_mesaEstado` (`idMesaEstado`) REFERENCES `mesaEstado` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `mesa` (`id`, `codigo`, `idMesaEstado`, `descripcion`, `fechaAlta`) VALUES
(1, '0JBaE', 4, 'Mesa junto al ventanal, con vista a la calle', '2022-06-12 18:20:31'),
(2, 'lLIKY', 4, 'Mesa junto al baño al Candy Bar', '2022-06-12 18:20:31'),
(3, 'NvxMm', 4, 'Mesa junto a la barra,', '2022-06-12 18:20:31'),
(4, '1Io77', 4, 'Mesa especial cumpleaños, aislada con espacios para movilidad cómoda', '2022-06-12 18:20:31'),
(5, '6qX07', 1, 'Mesa en esquina, lugar oscuro, con intimidad', '2022-06-12 18:20:31');

ALTER TABLE `mesa`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `productoTipo`;

CREATE TABLE `productoTipo` (
  `id` int(11) NOT NULL,
  `tipo` varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
   UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `productoTipo` (`id`, `tipo`, `fechaAlta`) VALUES
(1, 'comida', '2022-06-12 18:20:31'),
(2, 'bebida', '2022-06-12 18:20:31');

ALTER TABLE `productoTipo`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `productoTipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 2;
COMMIT;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `producto`;

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `idArea` int(11) COLLATE utf8_spanish2_ci NOT NULL,
  `idProductoTipo` int(11) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(150) COLLATE utf8_spanish2_ci NOT NULL,
  `precio` float NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `tiempoEstimado` int(11) NULL DEFAULT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  UNIQUE KEY `nombre` (`nombre`),
  FOREIGN KEY `FK_producto_productoTipo` (`idProductoTipo`) REFERENCES `productoTipo` (`id`),
  FOREIGN KEY `FK_producto_area` (`idArea`) REFERENCES `area` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `producto` (`id`, `idArea`, `idProductoTipo`, `nombre`, `precio`, `stock`, `tiempoEstimado`, `fechaAlta`) VALUES
(1, 5, 1, 'Milanesa a caballo', 850.00, 100, 25, '2022-06-12'),
(2, 5, 1, 'Hamburguesa de Garbanzo', 750.00, 50, 17, '2022-06-12'),
(3, 5, 1, 'Empanada', 150.00, 100, 7, '2022-06-12'),
(4, 3, 2, 'Don Valentin 750cc', 1200.00, 50, 5, '2022-06-12'),
(5, 4, 2, 'Corona 450cc', 650.00, 50, 5, '2022-06-12'),
(6, 3, 2, 'Daikiri', 500.00, 60, 7, '2022-06-12'),
(7, 6, 1, 'Flan casero', 350.00, 80, 12, '2022-06-12'),
(8, 6, 2, 'Helado Tricolor (chocolate - vainilla - frutilla)', 450.00, 100, 10, '2022-06-12');

ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 8;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `pedidoEstado`;

CREATE TABLE `pedidoEstado` (
  `id` int(11) NOT NULL,
  `estado` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
   UNIQUE KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `pedidoEstado` (`id`, `estado`, `fechaAlta`) VALUES
(1, 'Pendiente', '2022-06-12 18:20:31'),
(2, 'En preparacion', '2022-06-12 18:20:31'),
(3, 'Listo para servir', '2022-06-12 18:20:31'),
(4, 'Cancelado', '2022-06-12 18:20:31'),
(5, 'Servido', '2022-06-12 18:20:31'),
(6, 'Cobrado', '2022-06-12 18:20:31');

ALTER TABLE `pedidoEstado`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pedidoEstado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 6;
COMMIT;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `pedido`;

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `codigo` varchar(5) COLLATE utf8_spanish2_ci NOT NULL,
  `idMesa` int(11) NOT NULL,
  `idPedidoEstado` int(11) NOT NULL,
  `idUsuarioMozo` int(11) NOT NULL,
  `idUsuarioSocio` int(11) NULL,
  `nombreCliente` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `foto` varchar(100) NULL,
  `importe` float NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  UNIQUE KEY `codigo` (`codigo`),
  FOREIGN KEY `FK_pedido_usuarioMozo` (`idUsuarioMozo`) REFERENCES `usuario` (`id`),
  FOREIGN KEY `FK_pedido_usuarioSocio` (`idUsuarioSocio`) REFERENCES `usuario` (`id`),
  FOREIGN KEY `FK_pedido_pedidoEstado` (`idPedidoEstado`) REFERENCES `pedidoEstado` (`id`),
  FOREIGN KEY `FK_pedido_mesa` (`idMesa`) REFERENCES `mesa` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- INSERT INTO `pedido` (`id`, `codigo`, `idMesa`,
-- `idPedidoEstado`, `idUsuarioMozo`, `idUsuarioSocio`,
-- `nombreCliente`, `foto`, `importe`,) VALUES

ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `pedidoDetalle`;

CREATE TABLE `pedidoDetalle` (
  `id` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idPedidoEstado` int(11) NOT NULL,
  `idUsuarioEncargado` int(11) NULL,
  `cantidadProducto` int(11) NOT NULL DEFAULT 1,
  `tiempoEstimado` int(11) NULL,
  `tiempoInicio` varchar(8) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `tiempoFin` varchar(8) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  FOREIGN KEY `FK_pedidoDetalle_pedido` (`idPedido`) REFERENCES `pedido` (`id`),
  FOREIGN KEY `FK_pedidoDetalle_producto` (`idProducto`) REFERENCES `producto` (`id`),
  FOREIGN KEY `FK_pedidoDetalle_pedidoEstado` (`idPedidoEstado`) REFERENCES `pedidoEstado` (`id`),
  FOREIGN KEY `FK_pedidoDetalle_usuario` (`idUsuarioEncargado`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- INSERT INTO `pedido` (`id`, `codigo`, `idMesa`,
-- `idPedidoEstado`, `idUsuarioMozo`, `idUsuarioSocio`,
-- `nombreCliente`, `foto`, `importe`,) VALUES

ALTER TABLE `pedidoDetalle`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pedidoDetalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `UsuarioAccionTipo`;

CREATE TABLE `UsuarioAccionTipo` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
   UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

INSERT INTO `UsuarioAccionTipo` (`id`, `tipo`, `fechaAlta`) VALUES
(1, 'Login', '2022-06-12 18:20:31'),
(2, 'Alta', '2022-06-12 18:20:31'),
(3, 'Baja', '2022-06-12 18:20:31'),
(4, 'Modificacion', '2022-06-12 18:20:31'),
(5, 'CargaFoto', '2022-06-12 18:20:31'),
(6, 'CargaCSV', '2022-06-12 18:20:31'),
(7, 'DescargaCSV', '2022-06-12 18:20:31');

ALTER TABLE `UsuarioAccionTipo`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `UsuarioAccionTipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 7;
COMMIT;
----------------------------------------------------------------------
USE `db_comanda`;
DROP TABLE IF EXISTS `usuarioAccion`;

-- Estructura de tabla para la tabla `usuario`
CREATE TABLE `usuarioAccion` (
  `id` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idUsuarioAccionTipo` int(11) NOT NULL,
  `idPedido` int(11)  NULL,
  `idPedidoDetalle` int(11) NULL,
  `idMesa` int(11) NULL,
  `idProducto` int(11) NULL,
  `idArea` int(11) NULL,
  `hora` varchar(8) COLLATE utf8_spanish2_ci NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaModificacion` date DEFAULT NULL,
  `fechaBaja` date DEFAULT NULL,
  FOREIGN KEY `FK_usuarioAccion_usuario` (`idUsuario`) REFERENCES `usuario` (`id`),
  FOREIGN KEY `FK_usuarioAccion_UsuarioAccionTipo` (`idUsuarioAccionTipo`) REFERENCES `UsuarioAccionTipo` (`id`),
  FOREIGN KEY `FK_usuarioAccion_pedido` (`idPedido`) REFERENCES `pedido` (`id`),
  FOREIGN KEY `FK_usuarioAccion_pedidoDetalle` (`idPedidoDetalle`) REFERENCES `pedidoDetalle` (`id`),
  FOREIGN KEY `FK_usuarioAccion_mesa` (`idMesa`) REFERENCES `mesa` (`id`),
  FOREIGN KEY `FK_usuarioAccion_producto` (`idProducto`) REFERENCES `producto` (`id`),
  FOREIGN KEY `FK_usuarioAccion_area` (`idArea`) REFERENCES `area` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

ALTER TABLE `usuarioAccion`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usuarioAccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

----------------------------------------------------------------------
DROP TABLE IF EXISTS `pedidoEncuesta`;

CREATE TABLE `pedidoEncuesta` (
  `id` int(11) NOT NULL,
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
  FOREIGN KEY `FK_pedidoEncuesta_mesa` (`idMesa`) REFERENCES `mesa` (`id`),
  FOREIGN KEY `FK_pedidoEncuesta_pedido` (`idPedido`) REFERENCES `pedido` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

ALTER TABLE `pedidoEncuesta`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pedidoEncuesta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;








