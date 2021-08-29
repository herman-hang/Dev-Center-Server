<?php
/**
 * 后台登录和权限验证中间件
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\middleware;

use app\admin\model\AdminLog;
use thans\jwt\exception\JWTException;
use thans\jwt\facade\JWTAuth;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;

class Auth
{
    /**
     * 权限验证处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 当前访问的控制器
        $controller = Request::controller();
        // 当前访问的方法
        $action = Request::action();
        // 拼接url
        $url = strtolower($controller . '/' . $action);
        //获取不需要验证登录和权限的路由
        $notAuthRoute = Config::get('auth');
        // 将数组中的每一项全部转为小写
        $notAuth = array_map('strtolower', $notAuthRoute['not_auth']);
        // $url存在数组则跳过，不存在开始检测登录
        if (!in_array($url, $notAuth)) {
            try {
                // 验证token, 并获取token中的payload部分
                $token = JWTAuth::auth();
                // 将数组中的每一项全部转为小写
                $isLogin = array_map('strtolower', $notAuthRoute['is_login']);
                // $url存在数组则跳过，不存在开始检测权限，超级管理员（ID=1）也跳过
                if (!in_array($url, $isLogin) && $token['uid']->getValue() !== 1) {
                    $auth = new \auth\Auth();
                    if (!$auth->check($url, $token['uid']->getValue())) {//规则名称,管理员ID
                        //没有操作权限
                        result(403, "无权限操作！");
                    }
                }
                //获取token的有效时间
                $expTime = $token['exp']->getValue();
                //如果JWT的有效时间小于15分钟则刷新token并返回给客户端
                if ($expTime - time() < 900) {
                    //刷新token，会将旧token加入黑名单
                    $newToken = JWTAuth::refresh();
                    header('Access-Control-Expose-Headers:Authorization');
                    header('Authorization:bearer ' . $newToken);
                }
                //向控制器传当前管理员的ID
                $request->uid = $token['uid']->getValue();
            } catch (JWTException $e) {
                // 状态码-1为token在黑名单宽限期列表中，这是应该继续放行
                if ($e->getCode() !== -1) {
                    //实例化对象
                    $log = new AdminLog();
                    // 记录退出的时间和IP地址
                    $expToken = JWTAuth::auth(false);
                    $id = $expToken['uid']->getValue();
                    $ip = Request::ip();
                    Db::name('admin')->where('id', $id)->update(['lastlog_time' => time(), 'lastlog_ip' => $ip]);
                    // 日志记录
                    $log->save(['type' => 1, 'admin_id' => $id, 'content' => "登录超时！", 'ip' => $ip]);
                    result(0, "登录超时！");
                }
            }
        }
        return $next($request);
    }
}
