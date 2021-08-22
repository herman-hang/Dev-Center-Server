<?php
/**
 * 后台相关路由
 * by:小航 11467102@qq.com
 */

use think\facade\Route;

// 后台首页
Route::rule('home', 'Index/home', 'GET');
// 后台桌面
Route::rule('welcome', 'Index/welcome', 'GET');
// 清除缓存
Route::rule('clear', 'Index/clear', 'POST');
// 登录页
Route::rule('login', 'Login/login', 'POST');
// 获取图片验证码
Route::rule('captcha', 'Login/getCaptcha', 'GET');

/**
 * 系统管理相关路由
 */
Route::group(function () {
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
 * 用户相关路由
 */
Route::group('user',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','user/query',"GET");
});


/**
 * 功能配置相关路由
 */
Route::group(function (){
    // 支付配置
    Route::rule('pay','functional/pay',"GET");
    // 支付配置编辑
    Route::rule('payedit','functional/payedit',"PUT");
    // 短信配置
    Route::rule('sms','functional/sms',"GET");
    // 短信配置编辑
    Route::rule('smsedit','functional/smsedit',"PUT");
    // 短信测试
    Route::rule('testSms','functional/testSms','POST');
    // 邮件配置
    Route::rule('email','functional/email','GET');
    // 邮件配置编辑
    Route::rule('emailedit','functional/emailedit','PUT');
    // 邮件发送测试
    Route::rule('testemail','functional/testemail',"POST");
    // 第三方登录配置
    Route::rule('thirdparty','functional/thirdparty',"GET");
    // 第三方登录配置编辑
    Route::rule('thirdpartyedit','functional/thirdpartyedit',"PUT");
});

/**
 * 通知公告相关路由
 */
Route::group('notice',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','notice/query',"GET");
});

/**
 * 广告相关路由
 */
Route::group('advertising',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','advertising/query',"GET");
});

/**
 * 开发者相关路由
 */
Route::group('developer',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','developer/query',"GET");
});

/**
 * 升级中心相关路由
 */
Route::group('upgrade',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','upgrade/query',"GET");
});


/**
 * 应用中心相关路由
 */
Route::group('app',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','app/query',"GET");
});


/**
 * 应用中心相关路由
 */
Route::group('authorization',function (){
    // 根据ID获取管理员数据
    Route::rule('query/:id','authorization/query',"GET");
});