
drop table sys_auth_group;
drop table sys_auth_access;
drop table sys_auth_rule;

ALTER TABLE `sys_user`
ADD COLUMN `salt` varchar(128) NULL COMMENT '盐串' AFTER `referee`;

#image file 迁移

ALTER TABLE sys_file RENAME sys_file_old;
ALTER TABLE sys_image RENAME sys_image_old;

drop table if exists sys_file;

/*==============================================================*/
/* Table: sys_file                                              */
/*==============================================================*/
create table sys_file
(
   id                   int not null auto_increment,
   file_url             varchar(256) not null comment '文件url',
   file_path            varchar(256) not null comment '文件路径: file_url所在目录',
   name                 varchar(128) not null comment '名称',
   real_name            varchar(128) comment '原始名称',
   size                 int comment '大小',
   ext                  varchar(16) comment '后缀',
   bucket               varchar(64) comment 'oss桶',
   oss_url              varchar(512) comment 'ossurl',
   thumb_image_url      varchar(256) comment '缩略图',
   remark               varchar(512) comment '备注',
   create_by            varchar(255) comment '创建者',
   create_time          datetime not null comment '创建时间',
   primary key (id)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

alter table sys_file comment '文件表';

insert into sys_file(id,file_url,file_path,name,real_name,size,ext,bucket,oss_url,thumb_image_url,remark,create_by,create_time)
select id,file_url,file_path,file_name,file_name,file_size,ext,null,null,null,remark,null,create_time from sys_file_old;

insert into sys_file(id,file_url,file_path,name,real_name,size,ext,bucket,oss_url,thumb_image_url,remark,create_by,create_time)
select id,image_url,'',image_name,image_name,image_size,ext,null,oss_image_url,thumb_image_url,remark,null,create_time from sys_image_old;

drop table sys_file_old;
drop table sys_image_old;