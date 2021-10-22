
ALTER TABLE `cms_category` 
CHANGE COLUMN `title_cn` `title` varchar(64) NOT NULL AFTER `pid`,
CHANGE COLUMN `title_en` `name` varchar(64) NOT NULL AFTER `title`;

ALTER TABLE `cms_ad_slot` 
CHANGE COLUMN `title_cn` `title` varchar(32) NOT NULL AFTER `id`,
CHANGE COLUMN `title_en` `name` varchar(32)  NOT NULL AFTER `title`;