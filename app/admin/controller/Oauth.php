<?php
/**
 * 第三方登录登录控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use thans\jwt\facade\JWTAuth;
use think\facade\Db;
use think\facade\Config;
use think\facade\Request;
use think\facade\Session;
use think\facade\Cache;

class Oauth extends Base
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
        //获取入口地址
        foreach (Config::get('app.app_map') as $key => $value) {
            $entry[] = $key;
        }
        //查询所有配置信息
        $info = Db::name('thirdparty')->where('id', 1)->find();
        switch ($type) {
            case "qq";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/qq";
                $OAuth = new \Yurun\OAuthLogin\QQ\OAuth2($info['qq_appid'], $info['qq_secret'], $callback);
                break;
            case "weixin";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/weixin";
                $OAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($info['wx_appid'], $info['wx_secret'], $callback);
                break;
            case "sina";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/sina";
                $OAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($info['weibo_appid'], $info['weibo_secret'], $callback);
                break;
            case "gitee";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/gitee";
                $OAuth = new \Yurun\OAuthLogin\Gitee\OAuth2($info['gitee_appid'], $info['gitee_secret'], $callback);
                break;
            case "github";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/github";
                $OAuth = new \Yurun\OAuthLogin\Github\OAuth2($info['github_appid'], $info['github_secret'], $callback);
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
        //获取入口地址
        foreach (Config::get('app.app_map') as $key => $value) {
            $entry[] = $key;
        }
        $state = Cache::pull('state_' . Request::ip());
        //查询所有配置信息
        $info = Db::name('thirdparty')->where('id', 1)->find();
        switch ($type) {
            case "qq";
                $loginType = "QQ";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/qq";
                $OAuth = new \Yurun\OAuthLogin\QQ\OAuth2($info['qq_appid'], $info['qq_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid))
                {
                    $admin = Db::name('admin')->where('qq_openid', $openid)->field('id')->find();
                }else{
                    $admin = null;
                }
                break;
            case "weixin";
                $loginType = "微信";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/weixin";
                $OAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($info['wx_appid'], $info['wx_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid))
                {
                    $admin = Db::name('admin')->where('weixin_openid', $openid)->field('id')->find();
                }else{
                    $admin = null;
                }
                break;
            case "sina";
                $loginType = "微博";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/sina";
                $OAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($info['weibo_appid'], $info['weibo_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid))
                {
                    $admin = Db::name('admin')->where('weibo_openid', $openid)->field('id')->find();
                }else{
                    $admin = null;
                }
                break;
            case "gitee";
                $loginType = "Gitee";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/gitee";
                $OAuth = new \Yurun\OAuthLogin\Gitee\OAuth2($info['gitee_appid'], $info['gitee_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid))
                {
                    $admin = Db::name('admin')->where('gitee_openid', $openid)->field('id')->find();
                }else{
                    $admin = null;
                }
                break;
            case "github";
                $loginType = "Github";
                //定义回调地址
                $callback = Request::domain() . "/" . $entry[0] . "/oauth/callback/type/github";
                $OAuth = new \Yurun\OAuthLogin\Github\OAuth2($info['github_appid'], $info['github_secret'], $callback);
                // 获取accessToken
                $accessToken = $OAuth->getAccessToken($state);
                // 用户唯一标识
                $openid = $OAuth->openid;
                if (!empty($openid))
                {
                    $admin = Db::name('admin')->where('github_openid', $openid)->field('id')->find();
                }else{
                    $admin = null;
                }
                break;
            default:
                result(400, '非法请求！');
        }
        if (!empty($accessToken)) {
            //判断是否已经是管理员
            if (!empty($admin)) {
                //参数为用户认证的信息，请自行添加
                $token = JWTAuth::builder(['uid' => $admin['id']]);
                //登录总次数自增1
                Db::name('admin')->where('id', $admin['id'])->Inc('login_sum');
                //记录日志
                $this->log("使用{$loginType}快捷登录成功！", 1, $admin['id']);
                return view('loading', ['token' => 'bearer ' . $token, 'domain' => 'http://localhost:8080/login']);
            } else {
                //设置openid的缓存,方便登录成功后进行绑定
                $oauth['type'] = $type;
                $oauth['openid'] = $openid;
                Cache::set('oauth_' . Request::ip(), $oauth, 600);
                return view('loading', ['token' => '', 'domain' => 'http://localhost:8080/login']);
            }
        } else {
            result(500, '获取第三方用户信息失败！');
        }
    }
}
