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
    Route::rule('systemEdit', 'System/systemEdit', 'POST');
    // 安全配置
    Route::rule('security', 'System/security', 'GET');
    // 安全配置编辑
    Route::rule('securityEdit', 'System/securityEdit', 'POST');
    // 开关管理
    Route::rule('switch', 'System/switch', 'GET');
    // 开关管理编辑
    Route::rule('switchEdit', 'System/switchEdit', 'POST');
    // 修改密码
    Route::rule('pass', 'System/pass', 'GET');
    // 修改密码编辑
    Route::rule('passEdit', 'System/passEdit', 'POST');
});

/**
 * 管理员管理相关路由
 */
Route::group('admin',function (){
    Route::rule('query/:id','admin/query',"GET");
});