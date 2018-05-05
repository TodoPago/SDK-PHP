/*
Autor: Sergio de EOM Design Group www.e-om.com.ar

Target Server Type    : MYSQL
Target Server Version : 50539
File Encoding         : 65001

Date: 2017-03-03 19:15:40
*/
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for plugin_todo_pago_provincias
-- ----------------------------
DROP TABLE IF EXISTS `plugin_todo_pago_provincias`;
CREATE TABLE `plugin_todo_pago_provincias` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `codigo` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of plugin_todo_pago_provincias
-- ----------------------------
INSERT INTO `plugin_todo_pago_provincias` VALUES ('1', 'CABA', 'C');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('2', 'Buenos Aires', 'B');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('3', 'Catamarca', 'K');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('4', 'Chaco', 'H');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('5', 'Chubut', 'U');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('6', 'Córdoba', 'X');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('7', 'Corrientes', 'W');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('8', 'Entre Ríos', 'E');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('9', 'Formosa', 'P');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('10', 'Jujuy', 'Y');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('11', 'La Pampa', 'L');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('12', 'La Rioja', 'F');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('13', 'Mendoza', 'M');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('14', 'Misiones', 'N');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('15', 'Neuquén', 'Q');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('16', 'Río Negro', 'R');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('17', 'Salta', 'A');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('18', 'San Juan', 'J');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('19', 'San Luis', 'D');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('20', 'Santa Cruz', 'Z');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('21', 'Santa Fe', 'S');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('22', 'Santiago del Estero', 'G');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('23', 'Tierra del Fuego', 'V');
INSERT INTO `plugin_todo_pago_provincias` VALUES ('24', 'Tucumán', 'T');
