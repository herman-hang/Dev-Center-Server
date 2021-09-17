<?php
/**
 * 下载应用控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;

class Download extends Base
{
    /**
     * 应用列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page', 'type']);
            if (empty($data['type'])) {
                // 查询所有升级包信息
                $info = Db::name('app')
                    ->whereLike('name|author', "%" . $data['keywords'] . "%")
                    ->where('status', '2')
                    ->withoutField('cause')
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            } else {
                // 查询指定类型的应用
                $info = Db::name('app')
                    ->whereLike('name|author', "%" . $data['keywords'] . "%")
                    ->where('type', $data['type'])
                    ->where('status', '2')
                    ->withoutField('cause')
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            }
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 根据ID查询应用信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收ID
        $id = Request::param('id');
        // 查询用户信息
        $info = Db::name('app')->where('status', '2')->where('id', $id)->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 应用下载
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function download()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['auth_id', 'app_id']);
            $app = Db::name('app')->where('id', $data['app_id'])->field('is_pay,zip')->find();
            // 免费的应用直接返回下载地址
            if ($app['is_pay'] == '0') {
                result(200, "获取下载地址成功！", $app['zip']);
            }
            // 检测是否授权
            $info = Db::name('authorization')->where('user_id', request()->uid)->where('id', $data['auth_id'])->field('auth_plug,auth_temp')->find();
            if ($info) {
                $plug = explode(',', $info['auth_plug']);
                $temp = explode(',', $info['auth_temp']);
                if (in_array($data['app_id'], $plug) || in_array($data['app_id'], $temp)) {
                    // 下载量自增1
                    Db::name('app')->where('id', $data['app_id'])->inc('download')->update();
                    // 执行下载
                    result(200, "获取下载地址成功！", $app['zip']);
                } else {
                    result(403, "未授权！");
                }
            } else {
                result(403, "未找到授权信息！");
            }
        }
    }

    /**
     * 应用购买
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function appBuy()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::only(['id', 'pay_type', 'authorization_id']);
            if (empty($data['pay_type'])) {
                result(403, "请选择支付方式！");
            }
            $app = Db::name('app')->where('id', $data['id'])->field('name,money,type')->find();
            // 统一商品名称
            $data['title'] = $app['name'];
            // 价格
            $data['price'] = $app['money'];
            // 实例化支付类
            $pay = new Pay();
            // 异步通知地址
            $data['notify_url'] = "download/appPaySuccess";
            // 回调地址
            $data['return_url'] = "authorization/PaySuccessReturn";
            // 设置缓存
            Cache::set('pay_type_' . Request::ip(), $data['pay_type'], 600);
            // 订单号
            $data['order'] = trade_no();
            // 记录订单
            $indent['uid'] = request()->uid;
            $indent['indent'] = $data['order'];
            $indent['product_id'] = $data['id'];
            $indent['product_type'] = $app['type'];
            $indent['pay_type'] = $data['pay_type'];
            $indent['create_time'] = time();
            $indent['status'] = '0';
            if ($app['type'] == '0') {
                $indent['introduction'] = "购买了插件：{$app['name']}";
            } else {
                $indent['introduction'] = "购买了模板：{$app['name']}";
            }
            Db::name('user_buylog')->insert($indent);
            // 发起支付
            $pay->selectPay($data);
        }
    }

    /**
     * 应用购买
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function appPaySuccess()
    {
        if (request()->isGet()) {
            // 接收数据
            $info = Request::get();
            // 获取支付类型缓存
            $payType = Cache::get('pay_type_' . Request::ip());
        } else if (request()->isPost()) {// 支付宝官方支付
            $payType = 'alipay';
            // 接收数据
            $info = Request::post();
        }
        $pay = new Pay();
        // 查询支付开关
        $switch = Db::name('switch')->where('id', 1)->find();
        // 查询支付配置信息
        $alipay = Db::name('pay')->where('id', 1)->field('alipay_type')->find();
        if (empty($payType)) {// 为空则直接为易支付通道
            // 易支付通道
            $isSuccess = $pay->epayNotify($info);
            // 获取缓存
            $data = Cache::get('epay_' . $info['out_trade_no']);
        } else {
            // 判断选择的支付是否关闭
            if ($switch[$payType . '_switch'] == 0) {
                // 易支付通道
                $isSuccess = $pay->epayNotify($info);
                // 获取缓存
                $data = Cache::get('epay_' . $info['out_trade_no']);
            } else {
                // 其他官方通道
                switch ($payType) {
                    case 'wxpay':
                        $isSuccess = $pay->wxpayReturn();
                        $data = Cache::get('wxpay_' . Request::ip());
                        break;
                    case 'qqpay':
                        $isSuccess = $pay->qqpayReturn();
                        $data = Cache::get('qqpay_' . Request::ip());
                        break;
                    case 'alipay':
                        if ($alipay['alipay_type'] == 0) {// 官方支付
                            $isSuccess = $pay->alipayNotify($info);
                            // 获取缓存
                            $data = Cache::get('alipay_' . $info['out_trade_no']);
                        } else {// 当面付
                            $isSuccess = $pay->facepayReturn();
                            // 获取缓存
                            $data = Cache::get('facepay_' . Request::ip());
                        }
                        break;
                    default:
                        result(403, "没有该支付类型！");
                }
            }
        }
        // $isSuccess为true表示支付成功
        if ($isSuccess) {
            $authorization = Db::name('authorization')->where('id', $data['authorization_id'])->field('auth_plug,auth_temp')->find();
            $order = Db::name('user_buylog')->where('indent', $data['order'])->field('product_type')->find();
            if ($order['product_type'] == '0') {
                if (!empty($authorization['auth_plug'])) {
                    $plug = explode(",", $authorization['auth_plug']);
                    $newPlug = array_push($plug, $data['id']);
                    $plugStr = implode(",", $newPlug);
                    $res = Db::name('authorization')->where('id', $data['authorization_id'])->update(['auth_plug' => $plugStr]);
                } else {
                    $res = Db::name('authorization')->where('id', $data['authorization_id'])->update(['auth_plug' => $data['id']]);
                }
            } else {
                if (!empty($authorization['auth_plug'])) {
                    $temp = explode(",", $authorization['auth_plug']);
                    $newTemp = array_push($temp, $data['id']);
                    $tempStr = implode(",", $newTemp);
                    $res = Db::name('authorization')->where('id', $data['authorization_id'])->update(['auth_temp' => $tempStr]);
                } else {
                    $res = Db::name('authorization')->where('id', $data['authorization_id'])->update(['auth_temp' => $data['id']]);
                }
            }
            if ($res) {
                // 更新订单状态
                Db::name('user_buylog')->where('indent', $data['order'])->update(['status' => '1']);
                result(201, "支付成功！");
            } else {
                result(403, "支付失败！");
            }
        }
    }

}
