
ALTER TABLE `beyongcmsdb`.`cms_category` 
CHANGE COLUMN `title` `title` varchar(64) NOT NULL AFTER `pid`,
CHANGE COLUMN `name` `name` varchar(64) NOT NULL AFTER `title`;

ALTER TABLE `cms_ad_slot` 
CHANGE COLUMN `title` `title` varchar(32) NOT NULL AFTER `id`,
CHANGE COLUMN `name` `name` varchar(32)  NOT NULL AFTER `title`;
