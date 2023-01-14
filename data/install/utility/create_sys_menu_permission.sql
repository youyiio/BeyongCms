
drop procedure if exists init_data_permission;
delimiter $$
create procedure init_data_permission()
begin
    
    declare f1stmaxid, s2ndmaxid int(10) default 0; #一级菜单id,二级菜单id
    set f1stmaxid = 1;
    set s2ndmaxid = 0;
    
    select f1stmaxid, s2ndmaxid;



# 菜单初始化
truncate table sys_menu;

/*=============================api接口权限=============================*/

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

# 外链菜单 提供id生成值，避免与固定值冲突
INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(0, '官网链接', '', null, 'https://www.beyongx.com', null, 0, 1, "", 1, 999, 'api')
;
# 扩展菜单
INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(0, '二级菜单', 'twoMenu', 'Layout', 'twoMenu', 'nested', 1, 1, "", 1, 999, 'api')
;
select max(id) from sys_menu into f1stmaxid;
INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(f1stmaxid, '第一级菜单', 'one', 'empty/index', 'one/index', null, 1, 1, "", 1, 0, 'api')
;
select max(id) from sys_menu into s2ndmaxid;
select f1stmaxid, s2ndmaxid;
INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(s2ndmaxid, '第二级菜单', 'two', 'empty/index', 'two/index', null, 1, 1, "", 1, 0, 'api')
;

INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
(f1stmaxid, '平级菜单', 'pingji', 'empty/index', 'pingji/index', null, 1, 1, "", 1, 1, 'api')
;

/********************* 新增api扩展菜单或权限**********************/
# project api
#INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
#(0, 'project api', 'pcapi', 'Layout', 'pcapi', 'nested', 2, 0, "", 1, 999, 'api')
#;
#select max(id) from sys_menu into f1stmaxid;
#INSERT INTO `sys_menu`(pid,title,name,component,path,icon,type,is_menu,permission,status,sort,belongs_to) VALUES 
#(f1stmaxid, '版本检查', '', null, '', null, 2, 0, "proj:api.app:checkVersion", 1, 0, 'api'),
#(f1stmaxid, 'ip查询', '', null, '', null, 2, 0, "proj:api.app:ip", 1, 0, 'api')
#;

/*=============================admin管理后台权限=============================*/

/**************************一级菜单******************************/
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (20, 0, '综合面板', 'admin/ShowNav/Index', 'fa-th-large', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (21, 0, '个人中心', 'admin/ShowNav/Ucenter', 'fa-user', 1, 1, 14, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (22, 0, '用户管理', 'admin/ShowNav/User', 'fa-users', 1, 1, 16, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (23, 0, '权限管理', 'admin/ShowNav/Rule', 'fa-key', 1, 1, 17, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (24, 0, '系统管理', 'admin/ShowNav/System', 'fa-cog', 1, 1, 18, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (25, 0, '扩展管理', 'admin/ShowNav/Extension', 'fa-th-list', 1, 1, 15, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (26, 0, '内容管理', 'admin/ShowNav/Cms', 'fa-file-text', 1, 1, 11, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (27, 0, '客服管理', 'admin/ShowNav/Feedback', 'fa-comment', 1, 1, 12, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (28, 0, '资源管理', 'admin/ShowNav/Resource', 'fa-archive', 1, 1, 13, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (29, 0, '采集系统', 'admin/ShowNav/Crawler', 'fa-bug', 1, 1, 14, 1,'admin');


/**************************二级菜单******************************/
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (201, 20, '后台主框架', 'admin/Index/index', '', 1, 0, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (202, 20, '欢迎页面', 'admin/Index/welcome', '', 1, 0, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (203, 20, '基础面板','admin/Index/dashboard', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (209, 20, '公共功能列表', 'admin/ShowNav/Common', '', 1, 0, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (211, 21, '个人首页', 'admin/Ucenter/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (212, 21, '修改资料', 'admin/Ucenter/profile', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (213, 21, '修改密码', 'admin/Ucenter/password', '', 1, 1, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (221, 22, '用户列表', 'admin/User/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (222, 22, '新增用户', 'admin/User/addUser', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (223, 22, '用户统计', 'admin/User/userStat', '', 1, 1, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (231, 23, '权限规则', 'admin/Rule/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (232, 23, '用户分组', 'admin/Rule/group', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (233, 23, '管理员列表', 'admin/Rule/userList', '', 1, 0, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (241, 24, '系统设置', 'admin/System/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (242, 24, '友情链接', 'admin/System/links', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (243, 24, '清理缓存', 'admin/System/clearCache', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (244, 24, '日志审计', 'admin/System/actionLogs', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (245, 24, '站长工具', 'admin/Webmaster/index', '', 1, 1, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (251, 25, '主题管理', 'admin/Theme/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (252, 25, '插件管理', 'admin/Addon/index', '', 1, 1, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (261, 26, '文章管理', 'admin/Article/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (262, 26, '评论管理', 'admin/Comment/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (263, 26, '文章分类', 'admin/Category/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (264, 26, '广告管理', 'admin/Ad/index', '', 1, 1, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (271, 27, '客服消息', 'admin/Feedback/index', '', 1, 1, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (281, 28, '文档管理', 'admin/Resource/documents', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (282, 28, '图片管理', 'admin/Resource/images', '', 1, 1, 1, 1,'admin');

INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (291, 29, '采集列表', 'admin/Crawler/index', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (292, 29, '新增采集', 'admin/Crawler/create', '', 1, 0, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (293, 29, '数据预处理', 'admin/Crawler/preprocess', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (294, 29, '数据入库', 'admin/Crawler/warehouse', '', 1, 1, 1, 1,'admin');
INSERT INTO `sys_menu`(id,pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (295, 29, '发布计划', 'admin/Crawler/postPlan', '', 1, 1, 1, 1,'admin');

/**************************三级菜单******************************/
INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(201, '面板消息', 'admin/Message/index', '', 1, 0, 1, 1,'admin'),
(203, '今日数据','admin/Index/today', '', 1, 0, 1, 1,'admin'),
(203, '本月数据', 'admin/Index/month', '', 1, 0, 1, 1,'admin'),
(203, '年度数据', 'admin/Index/year', '', 1, 0, 1, 1,'admin'),
(209, '文件上传', 'admin/File/upload', '', 1, 0, 1, 1,'admin'),
(209, '软件上传', 'admin/File/uploadSoftware', '', 1, 0, 1, 1,'admin'),
(209, '移动App上传', 'admin/File/uploadApp', '', 1, 0, 1, 1,'admin'),
(209, '百度编辑器接口', 'admin/BaiduUeditor/index', '', 1, 0, 1, 1,'admin'),
(209, '图片上传截取', 'admin/Image/upcrop', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(211, '查看文章', 'admin/Ucenter/viewArticle', '', 1, 0, 1, 1,'admin'),
(211, '编辑文章', 'admin/Ucenter/editArticle', '', 1, 0, 1, 1,'admin'),
(211, '删除文章', 'admin/Ucenter/deleteArticle', '', 1, 0, 1, 1,'admin'),
(211, '发布文章', 'admin/Ucenter/postArticle', '', 1, 0, 1, 1,'admin'),
(211, '上头条', 'admin/Ucenter/upTop', '', 1, 0, 1, 1,'admin'),
(211, '取消头条', 'admin/Ucenter/deleteTop', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(221, '修改用户', 'admin/User/editUser', '', 1, 0, 1, 1,'admin'),
(221, '查看用户', 'admin/User/viewUser', '', 1, 0, 1, 1,'admin'),
(221, '修改密码', 'admin/User/changePwd', '', 1, 0, 1, 1,'admin'),
(221, '删除用户', 'admin/User/deleteUser', '', 1, 0, 1, 1,'admin'),
(221, '发送邮件', 'admin/User/sendMail', '', 1, 0, 1, 1,'admin'),
(221, '激活用户', 'admin/User/active', '', 1, 0, 1, 1,'admin'),
(221, '冻结用户', 'admin/User/freeze', '', 1, 0, 1, 1,'admin'),
(221, '延迟会员时间', 'admin/User/vip', '', 1, 0, 1, 1,'admin'),
(223, '统计报表数据', 'admin/User/echartShow', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(231, '新增权限规则', 'admin/Rule/add', '', 1, 0, 1, 1,'admin'),
(231, '编辑权限规则', 'admin/Rule/edit', '', 1, 0, 1, 1,'admin'),
(231, '删除权限规则', 'admin/Rule/delete', '', 1, 0, 1, 1,'admin'),
(231, '排序权限规则', 'admin/Rule/order', '', 1, 0, 1, 1,'admin'),
(231, '设置菜单值', 'admin/Rule/setMenu', '', 1, 0, 1, 1,'admin'),
(232, '新增分组', 'admin/Rule/addGroup', '', 1, 0, 1, 1,'admin'),
(232, '编辑分组', 'admin/Rule/editGroup', '', 1, 0, 1, 1,'admin'),
(232, '删除分组', 'admin/Rule/deleteGroup', '', 1, 0, 1, 1,'admin'),
(232, '分配权限', 'admin/Rule/ruleGroup', '', 1, 0, 1, 1,'admin'),
(232, '分组成员', 'admin/Rule/checkUser', '', 1, 0, 1, 1,'admin'),
(232, '添加成员', 'admin/Rule/addUserToGroup', '', 1, 0, 1, 1,'admin'),
(232, '移除成员', 'admin/Rule/deleteUserFromGroup', '', 1, 0, 1, 1,'admin'),
(233, '添加管理员', 'admin/Rule/addAdmin', '', 1, 0, 1, 1,'admin'),
(233, '编辑管理员', 'admin/Rule/editAdmin', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(241, '基本设置', 'admin/System/index', '', 1, 0, 1, 1,'admin'),
(241, '联系信息', 'admin/System/contact', '', 1, 0, 1, 1,'admin'),
(241, '通知邮箱', 'admin/System/email', '', 1, 0, 1, 1,'admin'),
(241, 'SEO设置', 'admin/System/seo', '', 1, 0, 1, 1,'admin'),
(242, '添加友链', 'admin/System/addLinks', '', 1, 0, 1, 1,'admin'),
(242, '修改友链', 'admin/System/editLinks', '', 1, 0, 1, 1,'admin'),
(242, '排序友链', 'admin/System/orderLinks', '', 1, 0, 1, 1,'admin'),
(242, '删除友链', 'admin/System/deleteLinks', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(245, '站长工具', 'admin/Webmaster/index', '', 1, 0, 1, 1,'admin'),
(245, '百度站长', 'admin/Webmaster/baidu', '', 1, 0, 1, 1,'admin'),
(245, '生成站点地图', 'admin/Webmaster/sitemap', '', 1, 0, 1, 1,'admin'),
(245, '站点地图分割信息', 'admin/Webmaster/sitemapInfo', '', 1, 0, 1, 1,'admin'),
(245, '站点地图txt下载', 'admin/Webmaster/sitemapTxt', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(251, '查看主题', 'admin/Theme/viewTheme', '', 1, 0, 1, 1,'admin'),
(251, '主题演示', 'admin/Theme/demo', '', 1, 0, 1, 1,'admin'),
(251, '下载主题', 'admin/Theme/download', '', 1, 0, 1, 1,'admin'),
(251, '上传主题', 'admin/Theme/upload', '', 1, 0, 1, 1,'admin'),
(251, '更新主题', 'admin/Theme/update', '', 1, 0, 1, 1,'admin'),
(251, '切换主题', 'admin/Theme/setCurrentTheme', '', 1, 0, 1, 1,'admin'),
(252, '查看插件', 'admin/Theme/viewTheme', '', 1, 0, 1, 1,'admin'),
(252, '插件演示', 'admin/Theme/demo', '', 1, 0, 1, 1,'admin'),
(252, '下载插件', 'admin/Theme/download', '', 1, 0, 1, 1,'admin'),
(252, '上传插件', 'admin/Theme/upload', '', 1, 0, 1, 1,'admin'),
(252, '更新插件', 'admin/Theme/upload', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(261, '查看文章', 'admin/Article/viewArticle', '', 1, 0, 1, 1,'admin'),
(261, '新增文章', 'admin/Article/addArticle', '', 1, 0, 1, 1,'admin'),
(261, '编辑文章', 'admin/Article/editArticle', '', 1, 0, 1, 1,'admin'),
(261, '删除文章', 'admin/Article/deleteArticle', '', 1, 0, 1, 1,'admin'),
(261, '置顶', 'admin/Article/setTop', '', 1, 0, 1, 1,'admin'),
(261, '取消置顶', 'admin/Article/unsetTop', '', 1, 0, 1, 1,'admin')
;
INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES
(261, '发布文章', 'admin/Article/postArticle', '', 1, 0, 1, 1,'admin'),
(261, '初审', 'admin/Article/auditFirst', '', 1, 0, 1, 1,'admin'),
(261, '终审', 'admin/Article/auditSecond', '', 1, 0, 1, 1,'admin'),
(261, '定时发布', 'admin/Article/setTimingPost', '', 1, 0, 1, 1,'admin'),
(261, '文章访问统计', 'admin/Article/articleStat', '', 1, 0, 1, 1,'admin'),
(261, '文章访问量统计图', 'admin/Article/echartShow', '', 1, 0, 1, 1,'admin'),
(261, '批量修改分类', 'admin/Article/batchCategory', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(262, '审核评论', 'admin/Comment/auditComment', '', 1, 0, 1, 1,'admin'),
(262, '回发评论', 'admin/Comment/postComment', '', 1, 0, 1, 1,'admin'),
(262, '删除评论', 'admin/Comment/deleteComment', '', 1, 0, 1, 1,'admin'),
(262, '查看评论', 'admin/Comment/viewComments', '', 1, 0, 1, 1,'admin'),
(263, '新增分类', 'admin/Category/addCategory', '', 1, 0, 1, 1,'admin'),
(263, '编辑分类', 'admin/Category/editCategory', '', 1, 0, 1, 1,'admin'),
(263, '排序分类', 'admin/Category/orderCategory', '', 1, 0, 1, 1,'admin'),
(263, '删除分类', 'admin/Category/deleteCategory', '', 1, 0, 1, 1,'admin'),
(264, '新增广告', 'admin/Ad/addAd', '', 1, 0, 1, 1,'admin'),
(264, '编辑广告', 'admin/Ad/editAd', '', 1, 0, 1, 1,'admin'),
(264, '广告排序', 'admin/Ad/orderAd', '', 1, 0, 1, 1,'admin'),
(264, '删除广告', 'admin/Ad/deleteAd', '', 1, 0, 1, 1,'admin')
;


INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(271, '消息列表', 'admin/Feedback/chat', '', 1, 0, 1, 1,'admin'),
(271, '消息回复', 'admin/Feedback/reply', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(281, '上传文档', 'admin/Resource/uploadDocument', '', 1, 0, 1, 1,'admin'),
(281, '删除文档', 'admin/Resource/deleteDocument', '', 1, 0, 1, 1,'admin'),
(282, '上传图片', 'admin/Resource/uploadImage', '', 1, 0, 1, 1,'admin'),
(282, '删除图片', 'admin/Resource/deleteImage', '', 1, 0, 1, 1,'admin')
;

INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
(291, '编辑规则', 'admin/Crawler/edit', '', 1, 0, 1, 1,'admin'),
(291, '采集操作', 'admin/Crawler/startCrawl', '', 1, 0, 1, 1,'admin'),
(291, '删除规则', 'admin/Crawler/deleteCrawler', '', 1, 0, 1, 1,'admin'),
(291, '克隆规则', 'admin/Crawler/cloneCrawler', '', 1, 0, 1, 1,'admin'),
(292, '采集测试', 'admin/Crawler/crawlTest', '', 1, 0, 1, 1,'admin'),
(293, '数据清洗', 'admin/Crawler/cleanData', '', 1, 0, 1, 1,'admin')
;


/********************* 新增admin扩展菜单或权限**********************/
#INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (0, '一级菜单', 'admin/ShowNav/NewMenu', 'fa-th-large', 1, 1, 999, 1,'admin');
#select max(id) from sys_menu into f1stmaxid;
#INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES (f1stmaxid, '二级菜单', 'admin/NewMenu/index', '', 1, 1, 1, 1,'admin');
#select max(id) from sys_menu into s2ndmaxid;
#INSERT INTO `sys_menu`(pid,title,path,icon,type,is_menu,sort,status,belongs_to) VALUES 
#(s2ndmaxid, '三级菜单1', 'admin/NewMenu/menu1', '', 1, 1, 1, 1,'admin'),
#(s2ndmaxid, '三级菜单2', 'admin/NewMenu/menu2', '', 1, 1, 1, 1,'admin'),
#(s2ndmaxid, '三级操作1', 'admin/NewMenu/action2', '', 1, 0, 1, 1,'admin'),
#(s2ndmaxid, '三级操作2', 'admin/NewMenu/action2', '', 1, 0, 1, 1,'admin')
#;



/*=============================给角色授权=============================*/
truncate `sys_role_menu`;
# delete from `sys_role_menu` where role_id = 1;

INSERT INTO `sys_role_menu`(role_id,menu_id) SELECT 1, id FROM `sys_menu`;




end $$
delimiter ;

call init_data_permission();

drop procedure if exists init_data_permission;