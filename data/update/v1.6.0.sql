DROP TABLE IF EXISTS api_device;
DROP TABLE IF EXISTS api_push_token;

ALTER TABLE cms_action_log rename to sys_action_log;
ALTER TABLE cms_addons rename to sys_addons;
ALTER TABLE cms_auth_group rename to sys_auth_group;
ALTER TABLE cms_auth_group_access rename to sys_auth_group_access;
ALTER TABLE cms_auth_rule rename to sys_auth_rule;
ALTER TABLE cms_config rename to sys_config;
ALTER TABLE cms_file rename to sys_file;
ALTER TABLE cms_hooks rename to sys_hooks;
ALTER TABLE cms_image rename to sys_image;
ALTER TABLE cms_message rename to sys_message;
ALTER TABLE cms_region rename to sys_region;
ALTER TABLE cms_user rename to sys_user;
ALTER TABLE cms_user_meta rename to sys_user_meta;
ALTER TABLE cms_user_verify_code rename to sys_user_verify_code;