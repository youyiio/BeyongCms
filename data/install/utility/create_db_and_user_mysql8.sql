# 创建mysql8 数据库和用户： 需手动替换数据库名和密码


create database cmsdb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

#mysql8默认使用caching_sha2_password加密插件(php7.4及以上支持)，php7.4以下只支持mysql_native_password以支付旧版
create user 'cmsdba'@'%' IDENTIFIED WITH mysql_native_password BY '' PASSWORD EXPIRE NEVER;

grant 
    create, drop,  grant   option, alter,
    delete, index, select, insert, update,
    create view, show view
on
    cmsdb.*
To
    'cmsdba'@'%';


#修改密码
ALTER USER 'cmsdba'@'%' IDENTIFIED WITH mysql_native_password BY 'new_password_xxx' PASSWORD EXPIRE NEVER;