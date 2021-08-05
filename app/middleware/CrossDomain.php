<?php
/**
 * 跨域设置中间件
 * by:小航 11467102@qq.com
 */
namespace app\middleware;
use think\Response;
class CrossDomain
{
    /**
     * 设置跨域
     * @param $request
     * @param \Closure $next
     *
     * @return mixed|void
     */
    public function handle($request, \Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 1800');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With');
        if (strtoupper($request->method()) == "OPTIONS") {
            return Response::create()->send();
        }
        return $next($request);
    }
}
