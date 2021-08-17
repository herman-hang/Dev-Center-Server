<?php
/**
 * API中间件
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\middleware;
use thans\jwt\exception\JWTException;
use thans\jwt\facade\JWTAuth;

class Api
{
    /**
     * token验证处理请求
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        try {
            //可验证token, 并获取token中的payload部分
            $token = JWTAuth::auth();
            //获取token的有效时间
            $expTime = $token['exp']->getValue();
            //如果JWT的有效时间小于15分钟则刷新token并返回给客户端
            if ($expTime - time() < 900){
                //刷新token，会将旧token加入黑名单
                $newToken = JWTAuth::refresh();
                header('Authorization:bearer '.$newToken);
            }
            //向控制器传当前管理员的ID
            $request->uid= $token['uid']->getValue();
        } catch (JWTException $e) {
            result(401,$e->getMessage());
        }
        return $next($request);
    }
}
