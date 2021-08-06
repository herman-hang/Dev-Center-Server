<?php
/**
 * 后台路由验证权限配置文件
 * by:小航 11467102@qq.com
 */
return [
    // 后台不需要验证登录，不需要验证权限的路由（控制器/方法）
    'not_auth' => [
        'login/login',// 登录
        'login/captcha', // 登录验证码
        'login/oauth', // 第三方登录绑定
        'oauth/login', // 第三方登录
        'oauth/callback', // 第三方登录回调地址
    ],
    // 后台需要登录，但是不需要验证权限的路由（控制器/方法）
    'is_login' => [
        'base/log', //记录管理员登录和操作日志
        'index/index', // 后台首页
        'index/welcome' // 后台我的桌面
    ]
];