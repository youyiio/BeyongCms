# 分配用户角色

drop procedure if exists assign_member_role;
delimiter $$
create procedure assign_member_role()
begin
    
    declare f1stmaxid, s2ndmaxid int(10) default 0; 
    declare role_id int(10);

    set f1stmaxid = 1;
    set s2ndmaxid = 0;

    select f1stmaxid, s2ndmaxid;

    

    select id into role_id from sys_role where name = 'member';
    
    select role_id;
    
    insert into sys_user_role(uid, role_id) select id, role_id from sys_user where id != 1;
    


end $$
delimiter ;

call assign_member_role();

drop procedure if exists assign_member_role;