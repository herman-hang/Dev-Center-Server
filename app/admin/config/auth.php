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
        'login/getCaptcha',//获取验证码
        'login/oauth', // 第三方登录绑定
        'login/getswitch', // 获取快捷登录开关
        'oauth/login', // 第三方登录
        'oauth/callback', // 第三方登录回调地址
    ],
    // 后台需要登录，但是不需要验证权限的路由（控制器/方法）
    'is_login' => [
        'base/log', //记录管理员登录和操作日志
        'base/upload',//文件上传
        'base/sendEmail',//邮件发送
        'index/home', // 后台首页
        'index/clear', // 清除缓存
        'index/welcome', // 后台我的桌面
        'admin/query', //根据ID查询管理员信息
        'group/query', //根据ID查询权限信息
        'user/query',//根据ID查询用户信息
        'notice/query',//根据ID查询公告信息
        'advertising/query',//根据ID查询广告信息
        'app/query',//根据ID查询应用信息
        'upgrade/query',//根据ID查询升级包信息
        'authorization/query',//根据ID查询授权信息
    ]
];