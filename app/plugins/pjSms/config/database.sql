DROP TABLE IF EXISTS `plugin_sms`;
CREATE TABLE IF NOT EXISTS `plugin_sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;