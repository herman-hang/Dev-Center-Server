<?php
/**
 * 前台不需要检测登录路由配置文件
 * by:小航 11467102@qq.com
 */
return [
    // 后台不需要验证登录（控制器/方法）
    'not_login' => [
        'index/index',
        'login/login',// 用户登录
        'login/getswitch',// 第三方登录开关获取
        'login/getcaptcha',// 获取验证码接口
        'login/register',// 用户注册
        'login/sendpassemailcode',// 修改密码发送验证码
        'login/passeditnext',// 修改密码下一步操作
        'login/password',// 修改密码
        'login/captcha',// 生成验证码
        'login/oauth',// 第三方登录绑定
        'oauth/login',// 第三方登录地址
        'oauth/callback',// 第三方登录授权回调地址
        'authorization/authPaySuccess', // 授权站点的支付异步通知地址
        'authorization/PaySuccessReturn', //支付回调地址
        'authorization/upgradeAuthPaySuccess', //升级域名授权服务支付回调地址
        'authorization/epayReturn',// 易支付回调地址
        'authorization/epayNotify',// 易支付异步通知地址
        'authorization/alipayReturn',// 支付宝官方支付回调地址
        'authorization/alipayNotify',// 支付宝官方支付异步通知地址
        'download/list',// 应用列表
        'download/query',// 根据ID查询应用信息
    ]
];