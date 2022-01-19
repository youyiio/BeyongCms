/*==============================================================*/
/* Table: sys_role                                              */
/*==============================================================*/
create table sys_role
(
   id                   smallint(6) not null auto_increment,
   name                 varchar(64) comment '角色标识',
   title                varchar(64) comment '角色名称',
   status               tinyint(1) default 1 comment '状态:1.激活;2.冻结;3.删除',
   remark               varchar(512) comment '备注',
   create_by            varchar(255) comment '创建者',
   update_by            varchar(255) comment '更新者',
   create_time          datetime comment '创建时间',
   update_time          datetime comment '更新时间',
   primary key (id)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

alter table sys_role comment '角色表';

/*==============================================================*/
/* Table: sys_role_menu                                         */
/*==============================================================*/
create table sys_role_menu
(
   id                   int not null auto_increment,
   role_id              int not null,
   menu_id              int not null,
   primary key (id)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

alter table sys_role_menu comment '角色菜单关联表';

/*==============================================================*/
/* Index: uniq_role_menu_role_id_menu_id                        */
/*==============================================================*/
create index uniq_role_menu_role_id_menu_id on sys_role_menu
(
   role_id,
   menu_id
);

/*==============================================================*/
/* Table: sys_menu                                              */
/*==============================================================*/
create table sys_menu
(
   id                   int not null auto_increment,
   pid                  int not null default 0 comment '父节点',
   title                varchar(64) not null comment '标题',
   name                 varchar(64) comment '名称',
   component            varchar(255) comment '前端组件',
   path                 varchar(255) comment '路由地址',
   icon                 varchar(64) comment '图标',
   type                 tinyint(1) not null default 1 comment '菜单类型 0.网页页面1.菜单组件2.动作组件',
   is_menu              tinyint(1) not null comment '是否菜单',
   permission           varchar(64) comment '权限标识',
   status               tinyint(1) not null default 1 comment '状态 -1.删除;0.暂停;1.激活;',
   sort                 int not null default 0 comment '排序',
   belongs_to           varchar(16) comment '归属于',
   create_by            varchar(255) comment '创建者',
   update_by            varchar(255) comment '更新者',
   create_time          datetime comment '创建时间',
   update_time          datetime comment '更新时间',
   primary key (id)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

alter table sys_menu comment '菜单表';

/*==============================================================*/
/* Table: sys_user_role                                         */
/*==============================================================*/
create table sys_user_role
(
   id                   int not null auto_increment,
   uid                  int not null comment '用户id',
   role_id              int not null comment '角色id',
   primary key (id)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

alter table sys_user_role comment '用户角色关联表';

/*==============================================================*/
/* Index: uniq_user_role_uid_role_id                            */
/*==============================================================*/
create unique index uniq_user_role_uid_role_id on sys_user_role
(
   uid,
   role_id
);


truncate table sys_menu;

/**************************一级菜单******************************/
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (1, 0, '面板', 'Dashboard', 'dashboard/index', 'dashboard/index','el-icon-s-home', 1, 0, null, 1, 0, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (2, 0, '通用公共接口', 'Common', 'Layout', 'common', null, 1, 0, null, 1, 0, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (3, 0, '用户中心', 'Ucenter', 'Layout', 'ucenter', null, 1, 0, null, 1, 0, 'api');

INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (4, 0, '内容管理', 'CmsIndex', 'Layout', 'cms', 'el-icon-news', 1, 1, null, 1, 7, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (5, 0, '运维管理', 'OperationIndex', 'Layout', 'operation', 'el-icon-data-line', 1, 1, null, 1, 8, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (6, 0, '系统管理', 'SystemIndex', 'Layout', 'system', 'el-icon-news', 1, 1, null, 1, 9, 'api');


/**************************二级菜单******************************/
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (100, 4, '文章管理', 'ArticleIndex', 'cms/article/index', 'article/index', null, 1, 1, 'article:list', 1, 0, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (101, 4, '评论管理', 'CommentIndex', 'cms/comment/index', 'comment/index', null, 1, 1, 'comment:list', 1, 2, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (102, 4, '文章分类', 'CategoryIndex', 'cms/category/index', 'category/index', null, 1, 1, 'category:list', 1, 3, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (103, 4, '广告管理', 'AdIndex', 'cms/ad/index', 'ad/index', '', 1, 1, null, 1, 4, 'api');

INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (105, 5, '服务器监控', 'ServerIndex', 'monitor/server/index', 'server/index', '', 1, 1, null, 1, 0, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (106, 5, '操作日志', 'LogIndex', 'monitor/log/index', 'log/index', '', 1, 1, null, 1, 1, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (107, 5, '数据库管理', 'DatabaseIndex', 'monitor/database/index', 'database/index', '', 1, 1, null, 1, 2, 'api');

INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (108, 6, '用户管理', 'UserIndex', 'system/user/index', 'user/index', '', 1, 1, null, 1, 0, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (109, 6, '角色管理', 'RoleIndex', 'system/role/index', 'role/index', '', 1, 1, null, 1, 1, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (110, 6, '菜单管理', 'MenuIndex', 'system/menu/index', 'menu/index', '', 1, 1, null, 1, 2, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (111, 6, '部门管理', 'DeptIndex', 'system/dept/index', 'dept/index', '', 1, 1, null, 1, 3, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (112, 6, '岗位管理', 'JobIndex', 'system/job/index', 'job/index', '', 1, 1, null, 1, 4, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (113, 6, '字典管理', 'DictIndex', 'system/dict/index', 'dict/index', '', 1, 1, null, 1, 5, 'api');
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES (114, 6, '友链管理', 'LinkIndex', 'system/link/index', 'link/index', '', 1, 0, null, 1, 6, 'api');


INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(2, '查询字典信息', '', null, '', null, 2, 0, "config:query", 1, 0, 'api'),
(2, '查询状态字典', '', null, '', null, 2, 0, "config:status", 1, 0, 'api'),
(2, '查询部门信息', '', null, '', null, 2, 0, "dept:dict", 1, 0, 'api'),
(2, '查询岗位字典', '', null, '', null, 2, 0, "job:dict", 1, 0, 'api'),
(2, '筛选用户列表', '', null, '', null, 2, 0, "user:quickSelect", 1, 0, 'api'),
(2, '图片上传', '', null, '', null, 2, 0, "image:upload", 1, 0, 'api'),
(2, '文件上传', '', null, '', null, 2, 0, "file:upload", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(3, '个人中心', 'UcenterIndex', 'system/user/center', 'index', '', 1, 1, null, 1, 0, 'api'),
(3, '查询用户信息', '', null, '', null, 2, 0, "ucenter:getInfo", 1, 0, 'api'),
(3, '编辑个人资料', '', null, '', null, 2, 0, "ucenter:profile", 1, 0, 'api'),
(3, '修改个人密码', '', null, '', null, 2, 0, "ucenter:modifyPassword", 1, 0, 'api'),
(3, '查询权限菜单', '', null, '', null, 2, 0, "ucenter:menus", 1, 0, 'api'),
(3, '退出登录', '', null, '', null, 2, 0, "sign:logout", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(100, '查询文章列表', '', null, '', null, 2, 0, "article:list", 1, 0, 'api'),
(100, '查询文章内容', 'ArticleDetail', 'cms/article/detail', 'articleDetail', null, 1, 0, 'article:query', 1, 1, 'api'),
(100, '新增文章', 'ArticleCreate', 'cms/article/operation', 'articleCreate', null, 1, 0, "article:create", 1, 0, 'api'),
(100, '编辑文章', 'ArticleUpdate', 'cms/article/operation', 'articleUpdate', null, 1, 0, "article:edit", 1, 0, 'api'),
(100, '发布文章', '', null, '', null, 2, 0, "article:publish", 1, 0, 'api'),
(100, '审核文章', '', null, '', null, 2, 0, "article:audit", 1, 0, 'api'),
(100, '删除文章', '', null, '', null, 2, 0, "article:delete", 1, 0, 'api'),
(100, '查询文章评论', '', null, '', null, 2, 0, "article:commentList", 2, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(101, '查询评论列表', '', null, '', null, 2, 0, "comment:list", 1, 0, 'api'),
(101, '查询评论内容', '', null, '', null, 2, 0, "comment:query", 1, 0, 'api'),
(101, '新增评论', '', null, '', null, 2, 0, "comment:create", 1, 0, 'api'),
(101, '审核评论', '', null, '', null, 2, 0, "comment:audit", 1, 0, 'api'),
(101, '删除评论', '', null, '', null, 2, 0, "comment:delete", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(102, '查询分类列表', '', null, '', null, 2, 0, "category:list", 1, 0, 'api'),
(102, '新增分类', '', null, '', null, 2, 0, "category:create", 1, 0, 'api'),
(102, '编辑分类', '', null, '', null, 2, 0, "category:edit", 1, 0, 'api'),
(102, '上线/下线分类', '', null, '', null, 2, 0, "category:setStatus", 1, 0, 'api'),
(102, '删除分类', '', null, '', null, 2, 0, "category:delete", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(103, '查询广告列表', '', null, 'api/ad/list', null, 2, 0, "ad:list", 1, 0, 'api'),
(103, '查询广告插槽', '', null, 'api/ad/slots', null, 2, 0, "ad:slots", 1, 0, 'api'),
(103, '新增广告', '', null, 'api/ad/create', null, 2, 0, "ad:create", 1, 0, 'api'),
(103, '编辑广告', '', null, 'api/ad/edit', null, 2, 0, "ad:edit", 1, 0, 'api'),
(103, '上线/下线广告', '', null, 'api/ad/setStatus', null, 2, 0, "ad:setStatus", 1, 0, 'api'),
(103, '删除广告', '', null, 'api/ad/delete', null, 2, 0, "ad:delete", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(105, '查询服务器状态', '', null, '', null, 2, 0, "server:status", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(106, '查询操作日志', '', null, '', null, 2, 0, "log:list", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(107, '查询数据库表', '', null, '', null, 2, 0, "database:tables", 1, 0, 'api'),
(107, '查询数据库', '', null, '', null, 2, 0, "database:databases", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(108, '查询用户列表', '', null, '', null, 2, 0, "user:list", 1, 0, 'api'),
(108, '查询用户信息', '', null, '', null, 2, 0, "user:query", 1, 0, 'api'),
(108, '新增用户', '', null, '', null, 2, 0, "user:create", 1, 0, 'api'),
(108, '编辑用户', '', null, '', null, 2, 0, "user:edit", 1, 0, 'api'),
(108, '删除用户', '', null, '', null, 2, 0, "user:delete", 1, 0, 'api'),
(108, '修改密码', '', null, '', null, 2, 0, "user:modifyPassword", 1, 0, 'api'),
(108, '冻结用户', '', null, '', null, 2, 0, "user:freeze", 1, 0, 'api'),
(108, '解冻用户', '', null, '', null, 2, 0, "user:unfreeze", 1, 0, 'api'),
(108, '分配用户角色', '', null, '', null, 2, 0, "user:addRoles", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(109, '查询角色列表', '', null, '', null, 2, 0, "role:list", 1, 0, 'api'),
(109, '新增角色', '', null, '', null, 2, 0, "role:create", 1, 0, 'api'),
(109, '编辑角色', '', null, '', null, 2, 0, "role:edit", 1, 0, 'api'),
(109, '删除角色', '', null, '', null, 2, 0, "role:delete", 1, 0, 'api'),
(109, '查询角色权限', '', null, '', null, 2, 0, "role:menus", 1, 0, 'api'),
(109, '分配角色权限', '', null, '', null, 2, 0, "role:addMenus", 1, 0, 'api'),
(109, '查询角色用户列表', '', null, '', null, 2, 0, "role:users", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(110, '查询菜单列表', '', null, '', null, 2, 0, "menu:list", 1, 0, 'api'),
(110, '新增菜单', '', null, '', null, 2, 0, "menu:create", 1, 0, 'api'),
(110, '编辑菜单', '', null, '', null, 2, 0, "menu:edit", 1, 0, 'api'),
(110, '删除菜单', '', null, '', null, 2, 0, "menu:delete", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(111, '查询部门列表', '', null, '', null, 2, 0, "dept:list", 1, 0, 'api'),
(111, '查询部门内容', '', null, '', null, 2, 0, "dept:query", 1, 0, 'api'),
(111, '新增部门', '', null, '', null, 2, 0, "dept:create", 1, 0, 'api'),
(111, '编辑部门', '', null, '', null, 2, 0, "dept:edit", 1, 0, 'api'),
(111, '删除部门', '', null, '', null, 2, 0, "dept:delete", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(112, '查询岗位列表', '', null, '', null, 2, 0, "job:list", 1, 0, 'api'),
(112, '查询岗位内容', '', null, '', null, 2, 0, "job:query", 1, 0, 'api'),
(112, '新增岗位', '', null, '', null, 2, 0, "job:create", 1, 0, 'api'),
(112, '编辑岗位', '', null, '', null, 2, 0, "job:edit", 1, 0, 'api'),
(112, '删除岗位', '', null, '', null, 2, 0, "job:delete", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(113, '查询字典组列表', '', null, '', null, 2, 0, "config:groups", 1, 0, 'api'),
(113, '查询字典列表', '', null, '', null, 2, 0, "config:list", 1, 0, 'api'),
(113, '新增字典', '', null, '', null, 2, 0, "config:create", 1, 0, 'api'),
(113, '编辑字典', '', null, '', null, 2, 0, "config:edit", 1, 0, 'api'),
(113, '删除字典', '', null, '', null, 2, 0, "config:delete", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(114, '查询友链列表', '', null, '', null, 2, 0, "link:list", 1, 0, 'api'),
(114, '新增友链', '', null, '', null, 2, 0, "link:create", 1, 0, 'api'),
(114, '编辑友链', '', null, '', null, 2, 0, "link:edit", 1, 0, 'api'),
(114, '删除友链', '', null, '', null, 2, 0, "link:delete", 1, 0, 'api')
;


# 测试menu
INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(0, '官网链接', '', null, 'https://www.beyongx.com', null, 0, 1, "", 1, 999, 'api')
;
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(1000, 0, '二级菜单', 'twoMenu', 'Layout', 'twoMenu', 'nested', 1, 1, "", 1, 999, 'api')
;
INSERT INTO `sys_menu`(id,pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(1001, 1000, '第一级菜单', 'one', 'empty/index', 'one/index', null, 1, 1, "", 1, 0, 'api'),
(1002, 1001, '第二级菜单', 'two', 'empty/index', 'two/index', null, 1, 1, "", 1, 0, 'api'),
(1003, 1000, '平级菜单', 'pingji', 'empty/index', 'pingji/index', null, 1, 1, "", 1, 1, 'api')
;


truncate `sys_role_menu`;
# delete from `sys_role_menu` where role_id = 1;

INSERT INTO `sys_role_menu`(role_id,menu_id) SELECT 1, id FROM `sys_menu`;