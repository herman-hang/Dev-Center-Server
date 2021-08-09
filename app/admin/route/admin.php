<?php
/**
 * 后台相关路由
 * by:小航 11467102@qq.com
 */

use think\facade\Route;

/**
 * 系统管理相关路由
 */
Route::group(function () {
    // 后台首页
    Route::rule('index', 'Index/index', 'GET');
    // 后台桌面
    Route::rule('welcome', 'Index/welcome', 'GET');
    // 登录页
    Route::rule('login', 'Login/login', 'POST');
    // 系统设置
    Route::rule('system', 'System/system', 'GET');
    // 系统设置编辑
    Route::rule('systemEdit', 'System/systemEdit', 'PUT');
    // 安全配置
    Route::rule('security', 'System/security', 'GET');
    // 安全配置编辑
    Route::rule('securityEdit', 'System/securityEdit', 'PUT');
    // 开关管理
    Route::rule('switch', 'System/switch', 'GET');
    // 开关管理编辑
    Route::rule('switchEdit', 'System/switchEdit', 'PUT');
    // 修改密码
    Route::rule('pass', 'System/pass', 'GET');
    // 修改密码编辑
    Route::rule('passEdit', 'System/passEdit', 'PUT');
});

/**
 * 管理员管理相关路由
 */
Route::group('admin',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','admin/query',"GET");
});

/**
 * 权限组相关路由
 */
Route::group('group',function (){
    // 根据ID获取权限组数据
    Route::rule('query/:id','group/query',"GET");
});