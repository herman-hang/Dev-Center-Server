<?php
/**
 * 前台相关路由
 * by:小航 11467102@qq.com
 */

use think\facade\Route;

// 获取图片验证码
Route::rule('captcha', 'Login/getCaptcha', 'GET');
// 获取快捷登录开关
Route::rule('getswitch', 'Login/getSwitch', 'GET');
// 登录页
Route::rule('login', 'Login/login', 'POST');