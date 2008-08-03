
-- ***************************************************************              -- EOQ
-- * SQL export made with Grum Plugins Classes (Export tool r1.3)         -- EOQ
-- * Export date    :2008-08-02 02:51:37                                    -- EOQ
-- * Export options : [drop] [create] [insert]                            -- EOQ
-- ***************************************************************              -- EOQ



-- ***************************************************************              -- EOQ
-- * Statements for piwigo_amm_urls table                               -- EOQ
-- ***************************************************************              -- EOQ
DROP TABLE `piwigo_amm_urls`; -- EOQ
CREATE TABLE `piwigo_amm_urls` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(50) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `mode` int(11) NOT NULL default '0',
  `icon` varchar(50) NOT NULL default '',
  `position` int(11) NOT NULL default '0',
  `visible` char(1) NOT NULL default 'y',
  PRIMARY KEY  (`id`),
  KEY `order_key` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; -- EOQ
INSERT INTO `piwigo_amm_urls`  VALUES('1', 'Test', 'http://192.168.1.1', '0', 'internet.png', '0', 'y'); -- EOQ
