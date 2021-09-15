开发者中心
===============
## 项目介绍
开发者中心v1.0.0是一款轻量级，开源，免费的系统，采用ThinkPHP6.0.x轻量级框架和ElementUI共同开发完成的，模式采用前后端分离开发，前后台可以独立分开部署，整套程序不超过10MB。

## 主要特性

* 授权模块
* 系统版本发布
* 应用中心
* 开发者管理
* 第三方登录
* 用户管理
* 广告管理
* 通知公告
* 功能配置
* 日志信息
* 系统管理
* 管理员管理
* 每周数据报表

## 演示站
* 后台演示站：http://demo.vividc.net/admin
* 前台演示站：http://demo.vividc.net/index

## 环境要求
* PHP7.1 (强类型严格模式)
* fileinfo扩展（宝塔面板：软件商城->运行环境->安装PHP7.1后点击设置->安装扩展->安装fileinfo扩展）
* CentOS 7.0+
* Nginx 1.10+
* MySQL 5.6+
* curl扩展（一般都自带，虚拟主机除外，没有自行下载）

## 源码下载
* 后端源码（服务器端）：https://github.com/xiaohang2020/Dev-Center-Server
* 前台前端（客户端）：https://github.com/xiaohang2020/dev-center-index-client
* 后台前端（客户端）：https://github.com/xiaohang2020/Dev-center-admin-client

## 如何安装
1、 将后端源码上传到服务器中，并且将站点运行目录设置为/public  
2、 `根目录/config/database`下修改数据库信息  
3、 将后端源码包中的`install.sql`文件导入数据库中  
4、 完成部署
其他：如果需要把客户端的代码单独部署，请把后端源码中的`根目录/public/admin`和`根目录/public/index`单独拿出来，并修改config.js文件中的API接口即可。对于有学习需求的可以走上面客户端的创库下载源码学习即可。  

## 登录地址

* 后台的登录地址：http://绑定的域名/admin
* 前台登录地址：http://绑定的域名/index

## 版权信息
本系统未经授权禁止商业用途，仅供个人运营或者学习使用，违者发现必追究！

## 个人公众号
更多开源组件、项目请走传送门
![微信公众号](https://www.yundaohang.net/tuoguan/wx.png "个人公众号")
