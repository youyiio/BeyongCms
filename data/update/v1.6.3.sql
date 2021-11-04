ALTER TABLE `sys_config` 
CHANGE COLUMN `tab` `group` varchar(16) NULL DEFAULT NULL COMMENT '字典组' AFTER `value_type`,
MODIFY COLUMN `value` text  NULL COMMENT '字典值' AFTER `name`;

ALTER TABLE `sys_config`
CHANGE COLUMN `name` `key` varchar(255) NULL COMMENT '字典键' AFTER `id`;

ALTER TABLE `sys_config` 
MODIFY COLUMN `group` varchar(16) NULL DEFAULT NULL COMMENT '字典组' AFTER `id`,
ADD COLUMN `name` varchar(255) NULL COMMENT '字典名称' AFTER `id`;

ALTER TABLE `sys_config` 
ADD COLUMN `status` tinyint(255) NULL COMMENT '启用状态' AFTER `value_type`,
ADD COLUMN `create_by` varchar(255) NULL AFTER `sort`,
ADD COLUMN `update_by` varchar(255) NULL AFTER `create_by`,
ADD COLUMN `create_time` datetime(0) NULL AFTER `update_by`,
ADD COLUMN `update_time` datetime(0) NULL AFTER `create_time`;