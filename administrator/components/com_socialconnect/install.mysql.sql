CREATE TABLE IF NOT EXISTS `#__socialconnect_sessions` (
  `session_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;