<?php
/**
 * 授权中心控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use think\console\command\make\Controller;
use think\Exception;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use app\index\validate\Authorization as AuthorizationValidate;
use app\index\model\Authorization as AuthorizationModel;

class Authorization extends Base
{
    /**
     * 我的授权列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有开发者
            $info = Db::name('authorization')
                ->whereLike('name|ip|domain_one|domain_two|domain_tree', "%" . $data['keywords'] . "%")
                ->where('user_id', request()->uid)
                ->order('create_time', 'desc')
                ->paginate([
                    'list_rows' => $data['per_page'],
                    'query' => request()->param(),
                    'var_page' => 'page',
                    'page' => $data['current_page']
                ]);
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 查询授权配置信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function queryConfig()
    {
        if (request()->isGet()) {
            $info = Db::name('authorization_config')->where('id', 1)->find();
            result(200, "获取配置信息成功！", $info);
        }
    }


    /**
     * 编辑授权站点
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::only(['id', 'name', 'ip', 'domain_one', 'domain_two', 'domain_tree']);
            // 验证数据
            $validate = new AuthorizationValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // xss过滤
            $data = $this->removeXSS($data);
            // 执行更新
            $authorization = AuthorizationModel::find($data['id']);
            $res = $authorization->save($data);
            if ($res) {
                result(200, "修改成功！");
            } else {
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 根据ID查询授权站点的信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        if (request()->isGet()) {
            // 接收授权站点ID
            $id = Request::param('id');
            // 查询用户信息
            $info = Db::name('authorization')->where('id', $id)->find();
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 添加授权站点
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::param();
            // 验证数据
            $validate = new AuthorizationValidate();
            if (!$validate->sceneAdd()->check($data)) {
                result(403, $validate->getError());
            }
            // xss过滤
            $data = $this->removeXSS($data);
            // 判断添加是否为铁牌数据，是则直接添加
            if ($data['level'] == 0) {// 免费
                // 添加数据到数据库
                $data['user_id'] = request()->uid;
                $res = AuthorizationModel::create($data);
                if ($res) {
                    result(201, "添加成功！");
                } else {
                    result(403, "添加失败！");
                }
            } else {// 付费
                // 查询铜牌，银牌，金牌的授权价格
                $price = Db::name('authorization_config')->where('id', 1)->field('copper,silver,gold')->find();
                switch ($data['level']) {
                    case 1:// 铜牌
                        $data['price'] = $price['copper'];
                        break;
                    case 2:// 银牌
                        $data['price'] = $price['silver'];
                        break;
                    case 3:// 金牌
                        $data['price'] = $price['gold'];
                        break;
                    default:
                        result(403, "非法请求！");
                }
                // 判断是否选择了支付方式
                if (empty($data['pay_type'])) {
                    result(403, "请选择支付方式！");
                }
                // 统一商品名称
                $data['title'] = '域名授权服务';
                // 实例化支付类
                $pay = new Pay();
                // 异步通知地址
                $data['notify_url'] = "authorization/authPaySuccess";
                // 回调地址
                $data['return_url'] = "authorization/PaySuccessReturn";
                // 设置缓存
                Cache::set('pay_type_' . Request::ip(), $data['pay_type'], 600);
                // 发起支付
                $pay->selectPay($data);
            }
        }
    }

    /**
     * 添加授权站点支付异步通知地址
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function authPaySuccess()
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
            $res = AuthorizationModel::create($data);
            if ($res) {
                result(201, "支付成功！");
            } else {
                result(403, "支付失败！");
            }
        }
    }

    /**
     * 易支付/支付宝官方 支付回调地址
     * @return \think\response\View
     */
    public function PaySuccessReturn()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::get();
            // 获取易支付缓存
            $epay = Cache::get('epay_' . $data['out_trade_no']);
            // 获取支付宝官方支付缓存
            $alipay = Cache::get('alipay_' . $data['out_trade_no']);
            try {
                $pay = new Pay();
                if (!empty($epay) && empty($alipay)) {
                    $res = $pay->epayReturn($data);
                    if ($res == 1) {
                        return view('pay/pay', ['code' => 201, 'msg' => '支付成功！']);
                    } else if ($res == -1) {
                        return view('pay/pay', ['code' => 403, 'msg' => '支付金额异常！']);
                    } else {
                        return view('pay/pay', ['code' => 403, 'msg' => '支付失败！']);
                    }
                } else {
                    $res = $pay->alipayReturn($data);
                    if ($res == 1) {
                        return view('pay/pay', ['code' => 201, 'msg' => '支付成功！']);
                    } else if ($res == -1) {
                        return view('pay/pay', ['code' => 403, 'msg' => '支付金额异常！']);
                    } else {
                        return view('pay/pay', ['code' => 403, 'msg' => '支付失败！']);
                    }
                }
            } catch (\Exception $e) {
                result(403, $e->getMessage());
            }
        }
    }

    /**
     * 授权升级服务
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateService()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::only(['id', 'upgrade_level', 'pay_type']);
            // 验证数据
            $validate = new AuthorizationValidate();
            if (!$validate->sceneUpdateService()->check($data)) {
                result(403, $validate->getError());
            }
            // 查询当前授权站点的服务等级
            $authorization = Db::name('authorization')->where('id', $data['id'])->field('level')->find();
            // 授权服务只能升级不能降级
            if ($data['upgrade_level'] <= $authorization['level']) {
                result(403, "授权服务只允许升级！");
            }
            // 查询当前选择升级服务的价格
            $price = Db::name('authorization_config')->where('id', 1)->field('iron_copper,iron_silver,iron_gold,copper_silver,copper_gold,silver_gold')->find();
            switch ($authorization['level']) {
                case 0:// 铁牌
                    switch ($data['upgrade_level']) {
                        case 1:// 铁牌升级铜牌
                            $data['price'] = $price['iron_copper'];
                            break;
                        case 2:// 铁牌升级银牌
                            $data['price'] = $price['iron_silver'];
                            break;
                        case 3:// 铁牌升级金牌
                            $data['price'] = $price['iron_gold'];
                            break;
                        default:
                            result(403, "非法请求！");
                    }
                    break;
                case 1:// 铜牌
                    switch ($data['upgrade_level']) {
                        case 2:// 铜牌升级银牌
                            $data['price'] = $price['copper_silver'];
                            break;
                        case 3:// 铜牌升级金牌
                            $data['price'] = $price['copper_gold'];
                            break;
                        default:
                            result(403, "非法请求！");
                    }
                    break;
                case 2:// 银牌
                    switch ($data['upgrade_level']) {
                        case 3:// 银牌升级金牌
                            $data['price'] = $price['silver_gold'];
                            break;
                        default:
                            result(403, "非法请求！");
                    }
                    break;
                default:
                    result(403, "非法请求！");
            }
            // 统一商品名称
            $data['title'] = '升级域名授权服务';
            // 实例化支付类
            $pay = new Pay();
            // 异步通知地址
            $data['notify_url'] = "authorization/upgradeAuthPaySuccess";
            // 回调地址
            $data['return_url'] = "authorization/PaySuccessReturn";
            // 设置缓存
            Cache::set('pay_type_' . Request::ip(), $data['pay_type'], 600);
            // 发起支付
            $pay->selectPay($data);
        }
    }

    /**
     * 升级服务支付异步通知地址
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upgradeAuthPaySuccess()
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
            // 更新
            $authorization = AuthorizationModel::find($data['id']);
            $data['level'] = $data['upgrade_level'];
            $res = $authorization->save($data);
            if ($res) {
                result(201, "支付成功！");
            } else {
                result(403, "支付失败！");
            }
        }
    }
}
