# 创建mysql5.x 数据库和用户： 需手动替换数据库名和密码

create database cmsdb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

grant 
    create, drop,  grant   option, alter,
    delete, index, select, insert, update,
    create view, show view
on
    cmsdb.*
To
    'cmsdba'@'%' identified by '';



