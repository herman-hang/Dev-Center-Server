<?php
/**
 * 后台限制IP访问中间件
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\middleware;

use think\facade\Db;
use think\facade\Request;

class CheckIp
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle($request, \Closure $next)
    {
        //查询系统信息
        $system = Db::name('system')->where('id', 1)->field('ip')->find();
        //判断允许访问的IP是否为空
        if (!empty($system['ip'])) {
            //查询到的ip字段分割成数组的形式
            $pieces = explode("|", $system['ip']);
            //统计有多少个IP地址,便于for循坏
            $count = count($pieces);
            //获取当前客户端的IP地址
            $ip = Request::ip();
            //要检测的ip拆分成数组
            $checkIpArr = explode('.', $ip);
            //限制IP
            if (!in_array($ip, $pieces)) {
                foreach ($pieces as $val) {
                    //发现有*号替代符
                    if (strpos($val, '*') !== false) {
                        $arr = explode('.', $val);
                        //用于记录循环检测中是否有匹配成功的
                        $bl = true;
                        for ($i = 0; $i < $count; $i++) {
                            //不等于*  就要进来检测，如果为*符号替代符就不检查
                            if ($arr[$i] !== '*') {
                                if ($arr[$i] !== $checkIpArr[$i]) {
                                    $bl = false;
                                    //终止检查本个ip 继续检查下一个ip
                                    break;
                                }
                            }
                        }
                        //如果是true则找到有一个匹配成功的就返回
                        if ($bl) {
                            die;
                        }
                    }
                }
                header('HTTP/1.1 403 Forbidden');
                result(403, '当前IP地址不可访问！');
            }
        }
        return $next($request);
    }
}
