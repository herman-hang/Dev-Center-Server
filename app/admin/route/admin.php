<?php
/**
 * 后台相关路由
 * by:小航 11467102@qq.com
 */
use think\facade\Route;

Route::group(function () {
    // 后台首页
    Route::rule('/', 'Index/index', 'GET');
    // 后台桌面
    Route::rule('welcome', 'Index/welcome', 'GET');
})->middleware([
    app\middleware\Api::class,
]);
// 登录页
Route::rule('login', 'Login/login', 'POST');