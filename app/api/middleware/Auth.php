<?php
/**
 * 验证KEY中间件
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\api\middleware;

use think\facade\Db;
use think\facade\Request;

class Auth
{
    /**
     * 验证KEY合法性
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $key = Request::header('key');
        $res = Db::name('user')->where('api_key',$key)->field('id')->find();
        if (empty($res)){
            result(403,"KEY验证失败！");
        }
        $request->uid = $res['id'];
        return $next($request);
    }
}
