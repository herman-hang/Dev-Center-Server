<?php
/**
 * 第三方登录登录控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use thans\jwt\facade\JWTAuth;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;

class Oauth
{
    /**
     * 第三方登录地址
     * @param null $type 第三方登录类型，比如：QQ
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login($type = null)
    {
        if ($type == null) {
            result(400, '参数错误');
        }
        // 查询开关
        $switch = Db::name('switch')->where('id', 1)->find();
        if ($switch[$type . 'login_switch'] === '0') {
            result(403, "该快捷登录已关闭！");
        }
        //查询所有配置信息
        $info = Db::name('thirdparty')->where('id', 1)->find();
        switch ($type) {
            case "qq":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/qq";
                $OAuth = new \Yurun\OAuthLogin\QQ\OAuth2($info['qq_appid'], $info['qq_secret'], $callback);
                break;
            case "weixin":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/weixin";
                $OAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($info['wx_appid'], $info['wx_secret'], $callback);
                break;
            case "sina":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/sina";
                $OAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($info['weibo_appid'], $info['weibo_secret'], $callback);
                break;
            case "gitee":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/gitee";
                $OAuth = new \Yurun\OAuthLogin\Gitee\OAuth2($info['gitee_appid'], $info['gitee_secret'], $callback);
                break;
            default:
                result(400, '非法请求！');
        }
        //调用getAuthUrl方法获取state
        $url = $OAuth->getAuthUrl();
        //设置缓存,方便回调验证,防止跨站请求伪造（CSRF）攻击
        Cache::set('state_' . Request::ip(), $OAuth->state, 600);
        header('location:' . $url);
    }

    /**
     * 授权回调地址
     * @param null $type 第三方登录类型，比如：QQ
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function callback($type = null)
    {
        $state = Cache::pull('state_' . Request::ip());
        //查询所有配置信息
        $info = Db::name('thirdparty')->where('id', 1)->find();
        switch ($type) {
            case "qq":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/qq";
                $OAuth = new \Yurun\OAuthLogin\QQ\OAuth2($info['qq_appid'], $info['qq_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid)) {
                    $user = Db::name('user')->where('qq_openid', $openid)->field('id')->find();
                } else {
                    $user = null;
                }
                break;
            case "weixin":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/weixin";
                $OAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($info['wx_appid'], $info['wx_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid)) {
                    $user = Db::name('user')->where('weixin_openid', $openid)->field('id')->find();
                } else {
                    $user = null;
                }
                break;
            case "sina":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/sina";
                $OAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($info['weibo_appid'], $info['weibo_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid)) {
                    $user = Db::name('user')->where('weibo_openid', $openid)->field('id')->find();
                } else {
                    $user = null;
                }
                break;
            case "gitee":
                //定义回调地址
                $callback = Request::domain() . "/index/oauth/callback/type/gitee";
                $OAuth = new \Yurun\OAuthLogin\Gitee\OAuth2($info['gitee_appid'], $info['gitee_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户信息
                $userInfo = $OAuth->getUserInfo();
                // 用户唯一标识
                $openid = $userInfo['id'];
                if (!empty($openid)) {
                    $user = Db::name('user')->where('gitee_openid', $openid)->field('id')->find();
                } else {
                    $user = null;
                }
                break;
            default:
                result(400, '非法请求！');
        }
        if (!empty($accessToken)) {
            $system = Db::name('system')->where('id', 1)->field('access')->find();
            //判断是否已经是用户
            if (!empty($user)) {
                //参数为用户认证的信息，请自行添加
                $token = JWTAuth::builder(['uid' => $user['id']]);
                //登录总次数自增1
                Db::name('user')->where('id', $user['id'])->Inc('login_sum');
                return view('loading', ['token' => 'bearer ' . $token]);
            } else {
                //设置openid的缓存,方便登录成功后进行绑定
                $oauth['type'] = $type;
                $oauth['openid'] = $openid;
                Cache::set('oauth_' . Request::ip(), $oauth, 600);
                return view('loading', ['token' => '']);
            }
        } else {
            result(500, '获取第三方用户信息失败！');
        }
    }
}
