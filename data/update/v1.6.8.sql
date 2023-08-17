
ALTER TABLE `sys_action_log` 
ADD COLUMN `http_referer` text NULL COMMENT 'http referer来源' AFTER `user_agent`;


ALTER TABLE `sys_file` 
MODIFY COLUMN `thumb_image_url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '缩略图' AFTER `ext`;

ALTER TABLE `sys_file` 
CHANGE COLUMN `bucket` `oss_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'oss类型: minio,aliyun,tencent,qiniuyun' AFTER `thumb_image_url`,
CHANGE COLUMN `oss_url` `oss_object_key` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'oss对象key' AFTER `oss_type`;