
ALTER TABLE `sys_action_log` 
ADD COLUMN `http_referer` text NULL COMMENT 'http referer来源' AFTER `user_agent`;