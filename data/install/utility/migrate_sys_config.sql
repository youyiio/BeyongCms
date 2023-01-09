
ALTER TABLE sys_config RENAME sys_config_old;

create table sys_config
(
   id                   int(11) not null auto_increment,
   name                 varchar(255) comment '字典名称',
   `group`              varchar(255) comment '字典组',
   `key`                varchar(255) comment '字典键',
   value                text comment '字典值',
   value_type           varchar(16) comment '值类型 integer,float,string,text,bool',
   status               tinyint comment '启用状态',
   sort                 int comment '排序',
   remark               varchar(512) comment '备注',
   create_by            varchar(255) comment '创建者',
   update_by            varchar(255) comment '更新者',
   create_time          datetime comment '创建时间',
   update_time          datetime comment '更新时间',
   primary key (id)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

alter table sys_config comment '字典表';

/*==============================================================*/
/* Index: uniq_sys_config_group_key                             */
/*==============================================================*/
create unique index uniq_sys_config_group_key on sys_config
(
   `group`,
   `key`
);

/*==============================================================*/
/* Index: idx_sys_config_key                                    */
/*==============================================================*/
create index idx_sys_config_key on sys_config
(
   `key`
);

truncate table sys_config;

INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('网站名称', 'base', 'site_name', 'BeyongCms内容管理系统', '网站名称', 'string', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('域名', 'base', 'domain_name', 'www.beyongx.com', '域名', 'string', 2);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('网址协议', 'base', 'protocol', 'http://', '网址协议', 'string', 3);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('备案号', 'base', 'icp', '闽ICP备xxxxxxxx号-1', '备案号', 'string', 4);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('主题名称', 'base', 'theme_package_name', 'classic', '主题名称', 'string', 5);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('统计代码', 'base', 'stat_code', '<script>\r\nvar _hmt = _hmt || [];\r\n(function() {\r\n  var hm = document.createElement(\"script\");\r\n  hm.src = \"https://hm.baidu.com/hm.js?3d0c1af3caa383b0cd59822f1e7a751b\";\r\n  var s = document.getElementsByTagName(\"script\")[0]; \r\n  s.parentNode.insertBefore(hm, s);\r\n})();\r\n</script>\r\n<!-- 以下为自动提交代码 -->\r\n<script>\r\n(function(){\r\n    var bp = document.createElement(\"script\");\r\n    var curProtocol = window.location.protocol.split(\":\")[0];\r\n    if (curProtocol === \"https\") {\r\n        bp.src = \"https://zz.bdstatic.com/linksubmit/push.js\";\r\n    }\r\n    else {\r\n        bp.src = \"http://push.zhanzhang.baidu.com/push.js\";\r\n    }\r\n    var s = document.getElementsByTagName(\"script\")[0];\r\n    s.parentNode.insertBefore(bp, s);\r\n})();\r\n</script>\r\n', '统计代码', 'text', 6);

INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('网站标题', 'seo', 'title', 'BeyongCms平台', '网站标题', 'string', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('网站描述', 'seo', 'description', 'BeyongCms内容管理系统|Beyongx,ThinkPHP,CMS，可二次开发的扩展框架，包含用户管理，权限角色管理及内容管理等', '网站描述', 'text', 3);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('网站关键词，有英文逗号分隔', 'seo', 'keywords', 'BeyongCms,Beyongx,ThinkPHP,CMS内容管理系统,扩展框架', '网站关键词，有英文逗号分隔', 'string', 3);

INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('公司名称', 'company', 'company_name', 'XXX公司', '公司名称', 'string', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('公司名称', 'company', 'bank_card', 'xxx', '公司银行账号', 'string', 2);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('公司名称', 'company', 'bank_name', '招商银行', '公司银行帐号开户行', 'string', 3);

INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('文章缩略图大小配置', 'article', 'article_thumb_image', '{\"width\":280,\"height\":280,\"thumb_width\":140,\"thumb_height\":140}', '文章缩略图大小配置', 'string', 0);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('文章审核', 'article', 'article_audit_switch', 'true', '文章审核', 'bool', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('文章评论', 'article', 'article_comment_switch', 'false', '是否允许文章评论', 'bool', 2);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('文章评论审核', 'article', 'article_comment_audit_switch', 'true', '文章评论审核', 'bool', 3);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('水印开关', 'article', 'article_water', '1', '水印开关(0:无水印,1:水印文字,2:水印图片)', 'number', 4);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('水印文本', 'article', 'article_water_text', '', '水印文本', 'string', 5);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('上传图片质量', 'article', 'image_upload_quality', '80', '上传图片质量', 'string', 6);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('上传图片宽高', 'article', 'image_upload_max_limit', '680', '上传图片宽高最大值(单位px,0为不限制)', 'string', 7);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('联系地址', 'contact', 'address', '厦门市思明区软件园二期望海路000号000室', '联系地址', 'string', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('邮编', 'contact', 'zip_code', '361008', '邮编', 'string', 2);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('传真', 'contact', 'fax', '0592-1234567', '传真', 'string', 3);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('联系电话', 'contact', 'tel', '0592-5000000', '联系电话', 'string', 4);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('联系人', 'contact', 'contact', 'beyongx sir', '联系人', 'string', 5);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('联系邮箱', 'contact', 'email', 'xx@xxx.com', '联系邮箱', 'string', 6);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('联系QQ', 'contact', 'qq', 'qq_xxx', '联系QQ', 'string', 7);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('联系微信', 'contact', 'weixin', 'weixin_xx', '联系微信', 'string', 8);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('邮箱服务器地址', 'email', 'email_host', 'smtp.exmail.qq.com', '邮箱SMTP服务器地址', 'string', 0);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('邮箱服务器端口', 'email', 'email_port', '465', 'SMTP服务器端口,一般为25', 'number', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('加密方式', 'email', 'email_security', 'ssl', '加密方式：null|ssl|tls, QQ邮箱必须使用ssl', 'string', 2);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('发件邮箱名称', 'email', 'email_name', 'service', '发件邮箱名称', 'string', 3);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('发件邮箱地址', 'email', 'email_addr', 'service@beyongx.com', '发件邮箱地址', 'string', 4);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('发件邮箱密码', 'email', 'email_pass', 'password', '发件邮箱密码', 'string', 5);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('新用户邮箱激活html格式', 'email_template', 'email_activate_user', '<style type=\"text/css\">\r\n  p{text-indent: 2em;}\r\n</style>\r\n<div><strong>尊敬的用户</strong></div>\r\n<p>您好，非常感谢您对Beyongx(<a href=\"https://www.ituizhan.com/\" target=\"_blank\" title=\"Beyongx\">Beyongx</a>)的关注和热爱</p>\r\n<p>您本次申请注册成为Beyongx会员的邮箱验证链接是: <a style=\"font-size: 30px;color: red;\" href=\"{url}\">{url}</a></p>\r\n<p>如果非您本人操作，请忽略该邮件。</p>\r\n', '新用户邮箱激活html格式', 'text', 6);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('用户邮箱重置密码html格式', 'email_template', 'email_reset_password', '<style type=\"text/css\">\r\np{text-indent: 2em;}\r\n</style>\r\n<div><strong>尊敬的用户</strong></div>\r\n<p>您好，非常感谢您对Beyongx(<a href=\"https://www.ituizhan.com/\" target=\"_blank\" title=\"Beyongx\">Beyongx</a>)的关注和热爱</p>\r\n<p>您本次申请找回密码的邮箱验证码是: <strong style=\"font-size: 30px;color: red;\">{code}</strong></p>\r\n<p>您本次重置密码的邮箱链接是: <a style=\"font-size: 30px;color: red;\"  href=\"{url}\">{url}</strong>\r\n<p>如果非您本人操作，请忽略该邮件。</p>\r\n', '用户邮箱重置密码html格式', 'text', 7);

INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('tab标签元数据', NULL, 'tab_meta', '[{\"tab\":\"base\",\"name\":\"基本设置\",\"sort\":1},{\"tab\":\"seo\",\"name\":\"SEO设置\",\"sort\":2},{\"tab\":\"contact\",\"name\":\"联系方式\",\"sort\":3},{\"tab\":\"email\",\"name\":\"邮箱设置\",\"sort\":4},{\"tab\":\"article\",\"name\":\"文章设置\",\"sort\":5},{\"tab\":\"aliyun_oss\",\"name\":\"阿里OSS存储\",\"sort\":6},{\"tab\":\"qiniuyun_oss\",\"name\":\"七牛OSS存储\",\"sort\":7},{\"tab\":\"email_template\",\"name\":\"邮件模板\",\"sort\":8},{\"tab\":\"oss\",\"name\":\"OSS存储设置\",\"sort\":9}]', 'tab标签元数据', 'string', 0);

#oss存储配置
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('OSS存储开关', 'oss', 'oss_switch', 'false', 'bool', 'oss', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('OSS厂商', 'oss', 'oss_vendor', 'qiniuyun', 'string', 'oss', 2);

INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('阿里oss Bucket名称', 'aliyun_oss', 'ali_bucket', 'Bucket名称', '阿里oss Bucket名称', 'string', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('阿里oss 外网地址endpoint', 'aliyun_oss', 'ali_endpoint', 'xxxx.aliyuncs.com', '阿里oss 外网地址endpoint', 'string', 2);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('阿里Access Key ID', 'aliyun_oss', 'ali_key_id', '阿里云key id', '阿里Access Key ID', 'string', 3);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('阿里Access Key Secret', 'aliyun_oss', 'ali_key_secret', '阿里云key secret', '阿里Access Key Secret', 'string', 4);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('阿里oss 访问的地址', 'aliyun_oss', 'ali_url', '阿里云oss域名地址', '阿里oss 访问的地址', 'string', 5);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('七牛oss Bucket', 'qiniuyun_oss', 'qiniu_bucket', 'Bucket名称', '七牛oss Bucket', 'string', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('七牛oss Accesskey', 'qiniuyun_oss', 'qiniu_key_id', '七牛oss Accesskey', '七牛oss Accesskey', 'string', 2);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('七牛oss Secretkey', 'qiniuyun_oss', 'qiniu_key_secret', '七牛oss Secretkey', '七牛oss Secretkey', 'string', 3);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('七牛oss 访问的地址', 'qiniuyun_oss', 'qiniu_url', '七牛域名地址', '七牛oss 访问的地址', 'string', 4);

#百度站长资源push
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('站长域名', 'zhanzhang', 'zhanzhang_site', '站长域名', '', 'string', 1);
INSERT INTO `sys_config`(name,`group`,`key`,value,remark,value_type,sort) VALUES ('站长token', 'zhanzhang', 'zhanzhang_token', '站长token', '', 'string', 2);


update sys_config t_new set t_new.value = (select t_old.value from sys_config_old t_old where t_old.`key` = t_new.`key`) where exists (select 1 from sys_config_old t_old where t_old.`key` = t_new.`key`);

drop table sys_config_old;
