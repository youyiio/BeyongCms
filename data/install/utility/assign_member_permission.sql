# 分配会员角色权限

drop procedure if exists assign_member_permission;
delimiter $$
create procedure assign_member_permission()
begin
    
    declare var_role_id int(10) default 0;

    select id into var_role_id from sys_role where name = 'member';
    
    select var_role_id;
    
    delete from sys_role_menu where role_id = var_role_id;
    # user    
    insert into `sys_role_menu`(role_id, menu_id) SELECT var_role_id, id FROM `sys_menu` where belongs_to='api' and permission like 'ucenter:%';

    # project menu permission
    # insert into `sys_role_menu`(role_id, menu_id) SELECT var_role_id, id FROM `sys_menu` where belongs_to='api' and permission like 'proj:api%';
    


end $$
delimiter ;

call assign_member_permission();

drop procedure if exists assign_member_permission;