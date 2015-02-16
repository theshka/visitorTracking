--
-- Table structure for table `visitors`
--

CREATE TABLE IF NOT EXISTS `visitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visitor_ip` varchar(32) DEFAULT NULL,
  `visitor_city` varchar(64) NOT NULL,
  `visitor_state` varchar(64) NOT NULL,
  `visitor_country` varchar(64) NOT NULL,
  `visitor_flag` varchar(256) NOT NULL,
  `visitor_browser` varchar(255) DEFAULT NULL,
  `visitor_OS` varchar(64) NOT NULL,
  `visitor_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visitor_day` varchar(2) NOT NULL,
  `visitor_month` varchar(2) NOT NULL,
  `visitor_year` varchar(4) NOT NULL,
  `visitor_hour` varchar(2) NOT NULL DEFAULT '0',
  `visitor_minute` varchar(2) NOT NULL DEFAULT '0',
  `visitor_seconds` varchar(2) NOT NULL,
  `visitor_referer` varchar(255) DEFAULT NULL,
  `visitor_page` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
