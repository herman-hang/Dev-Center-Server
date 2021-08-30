<?php
/**
 * 登录控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use app\admin\model\System;
use edward\captcha\facade\CaptchaApi;
use thans\jwt\facade\JWTAuth;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use app\index\validate\Login as LoginValidate;
use app\index\model\User;

class Login
{
    public function login()
    {
        if (request()->isPost()) {
            //接收数据
            $data = Request::only(['user', 'password', 'code']);
            //验证数据
            $validate = new LoginValidate();
            if (!$validate->check($data)) {
                result(403, $validate->getError());
            }
            // 查询当前用户
            $user = User::where('user|email|mobile', $data['user'])->field('id,password,status,login_sum,error_time,login_error,ban_time')->find();
            //查询系统信息
            $system = System::where('id', 1)->field('max_logerror')->find();
            //记录登录错误时间,1800秒后所有的记录清零
            $errorTime = time() + 1800;
            //封禁时间常量，单位：分钟
            $BAN = 30;
            //登录错误次数到达后的封禁时间
            $banTime = time() + $BAN * 60;
            if (empty($user)) {//用户不存在
                result(401, '用户不存在！');
            } else {//管理员存在
                if ($user['login_error'] == 0) {//不存在密码登录错误的情况
                    if ($user['status'] == 0) {//用户已停用
                        result(401, '用户已停用！');
                    } else {//管理员状态正常（已启用状态）
                        //哈希加密
                        if (password_verify($data['password'], $user['password'])) {//密码正确
                            //第三方登录绑定
                            $this->oauth($user['id']);
                            //登录总次数自增1
                            $user->inc('login_sum')->update();
                            //参数为用户认证的信息，请自行添加
                            $token = JWTAuth::builder(['uid' => $user['id']]);
                            result(200, '登录成功！', ['Authorization' => 'bearer ' . $token]);
                        } else {
                            //记录密码登录错误时间,便于$errorTime分钟后对登录错误次数清零
                            $user->save(['error_time' => $errorTime]);
                            //密码登录错误次数自增1
                            $user->inc('login_error')->update();
                            //调用生成验证码方法
                            $code = $this->captcha();
                            result(403, '密码错误！', $code);
                        }
                    }
                } else {//存在密码登录错误情况
                    //获取验证码的key
                    $key = Cache::pull('captcha_' . Request::ip());
                    if (empty($key)) {
                        //调用生成验证码方法
                        $code = $this->captcha();
                        result(403, '验证码异常，请重新登录！', $code);
                    }
                    //判断输入的验证码是否正确
                    if (!CaptchaApi::check($data['code'], $key)) {
                        //调用生成验证码方法
                        $code = $this->captcha();
                        result(403, '验证码错误！', $code);
                    }
                    //解除登录错误的计算时间，恢复初始化
                    if ($user->getData('error_time') <= time()) {
                        //将登录错误错误时间,登录错误次数清零
                        $user->save(['error_time' => NULL, 'login_error' => 0]);
                        //判断防止封禁时间大于等于登录错误时间出现的BUG
                        if ($user->getData('ban_time') == NULL || $user->getData('ban_time') <= time()) {
                            //将封禁时间设置为空
                            $user->save(['ban_time' => NULL]);
                            if ($user['status'] == 0) {
                                result(401, '用户已停用！');
                            } else {
                                if (password_verify($data['password'], $user['password'])) {//密码正确
                                    //第三方登录绑定
                                    $this->oauth($user['id']);
                                    //登录总次数自增1
                                    $user->inc('login_sum')->update();
                                    //参数为用户认证的信息，请自行添加
                                    $token = JWTAuth::builder(['uid' => $user['id']]);
                                    result(200, '登录成功！', ['Authorization' => 'bearer ' . $token]);
                                } else {//密码错误
                                    //记录密码登录错误时间,便于$errorTime分钟后对登录错误次数清零
                                    $user->save(['error_time' => $errorTime]);
                                    //登录密码错误次数自增1
                                    $user->inc('login_error')->update();
                                    //获取当前登录错误次数
                                    $errorCount = $user->getData('login_error');
                                    //获取允许登录错误的最大次数
                                    $maxError = $system->getData('max_logerror');
                                    $count = $maxError - $errorCount;
                                    //调用生成验证码方法
                                    $code = $this->captcha();
                                    result(403, "登录密码错误，还有{$count}次机会！", $code);
                                }
                            }
                        } else {
                            //计算剩余多少分钟解封，这里强制转为int类型
                            $time = (int)(($user->getData('ban_time') - time()) / 60);
                            //调用生成验证码方法
                            $code = $this->captcha();
                            result(403, "登录错误过多，请{$time}分钟后再试！", $code);
                        }
                    } else {
                        //判断当前的封禁时间是否为空
                        if ($user->getData('ban_time') == NULL || $user->getData('ban_time') <= time()) {
                            //将封禁时间设置为空
                            $user->save(['ban_time' => NULL]);
                            if ($user['status'] == 0) {
                                result(401, '用户已停用！');
                            } else {
                                //判断登录错误次数是否大于或等于指定登录错误次数
                                if ($user->getData('login_error') >= $system->getData('max_logerror')) {
                                    //封禁时间写入
                                    $user->save(['ban_time' => $banTime]);
                                    //调用生成验证码方法
                                    $code = $this->captcha();
                                    result(403, "登录错误过多，请{$BAN}分钟后再试！", $code);
                                } else {
                                    if (password_verify($data['password'], $user['password'])) {
                                        //第三方登录绑定
                                        $this->oauth($user['id']);
                                        //登录总次数自增1
                                        $user->inc('login_sum')->update();
                                        //登录错误次数清零,登录错误时间清空
                                        $user->save(['login_error' => 0, 'error_time' => NULL]);
                                        //参数为用户认证的信息，请自行添加
                                        $token = JWTAuth::builder(['uid' => $user['id']]);
                                        result(200, '登录成功！', ['Authorization' => 'bearer ' . $token]);
                                    } else {
                                        //记录密码登录错误时间,便于$errorTime分钟后对登录错误次数清零
                                        $user->save(['error_time' => $errorTime]);
                                        //登录密码错误次数自增1
                                        $user->inc('login_error')->update();
                                        //获取当前登录错误次数
                                        $errorCount = $user->getData('login_error');
                                        //获取允许登录错误最大次数
                                        $maxError = $system->getData('max_logerror');
                                        $count = $maxError - $errorCount;
                                        //调用生成验证码方法
                                        $code = $this->captcha();
                                        result(403, "密码错误，还有{$count}次机会！", $code);
                                    }
                                }
                            }
                        } else {
                            //计算剩余多少分钟解封，这里强制转为int类型
                            $time = (int)(($user->getData('ban_time') - time()) / 60);
                            //调用生成验证码方法
                            $code = $this->captcha();
                            result(403, "登录错误过多，请{$time}分钟后再试！", $code);
                        }
                    }
                }
            }
        }
    }

    /**
     * 生成验证码
     * @return array
     */
    public function captcha(): array
    {
        //生成验证码
        $code = CaptchaApi::create();
        //存入key
        Cache::set('captcha_' . Request::ip(), $code['key'], 600);
        //删除数组中的key和code
        unset($code['key'], $code['code']);
        return $code;
    }

    /**
     * 第三方登录绑定
     * @param $id 管理员ID
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function oauth($id): int
    {
        //判断openid的缓存是否存在，存在则进行判定
        $oauth = Cache::pull('oauth_' . Request::ip());
        if (empty($oauth)) {
            //获取值失败，无法绑定
            return 0;
        }
        $user = User::where('id', $id)->field('qq_openid,weixin_openid,weibo_openid,gitee_openid')->find();
        switch ($oauth['type']) {
            case 'qq':
                //判断QQ登录
                $user->save(['qq_openid' => $oauth['openid']]);
                break;
            case 'weixin':
                //判断微信登录
                $user->save(['weixin_openid' => $oauth['openid']]);
                break;
            case 'sina':
                //判断微博登录
                $user->save(['weibo_openid' => $oauth['openid']]);
                break;
            case 'gitee':
                //判断Gitee登录
                $user->save(['gitee_openid' => $oauth['openid']]);
                break;
            default:
                return 0;
        }
        return 1;
    }

    /**
     * 快捷登录开关获取
     */
    public function getSwitch()
    {
        //查询所有开关信息
        $info = Db::name('switch')->where('id', 1)->field('qqlogin_switch,weixinlogin_switch,sinalogin_switch,giteelogin_switch')->find();
        // 转为布尔值
        foreach ($info as $key => $val) {
            $info[$key] = (bool)$val;
        }
        result(200, "获取开关信息成功！", $info);
    }

    /**
     * 获取验证码
     */
    public function getCaptcha()
    {
        //生成验证码
        $code = CaptchaApi::create();
        //存入key
        Cache::set('captcha_' . Request::ip(), $code['key'], 600);
        //删除数组中的key和code
        unset($code['key'], $code['code']);
        result(200, "获取验证码成功！", $code);
    }
}
