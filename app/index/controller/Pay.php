<?php
/**
 * 支付控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use QQPay\QQPay;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use app\index\model\Authorization;
use app\index\validate\Authorization as AuthorizationValidate;

class Pay extends Base
{
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
            // 判断添加是否为铁牌数据，是则直接添加
            if ($data['level'] == 0) {// 免费
                // 添加数据到数据库
                $data['user_id'] = request()->uid;
                $res = Authorization::create($data);
                if ($res) {
                    result(201, "添加成功！");
                } else {
                    result(403, "添加失败！");
                }
            } else {// 付费
                // 查询支付开关
                $switch = Db::name('switch')->where('id', 1)->find();
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
                // 查询支付配置信息
                $payConfig = Db::name('pay')->where('id', 1)->find();
                // 判断是否选择了支付方式
                if (empty($data['pay_type'])) {
                    result(403, "请选择支付方式！");
                }
                // 判断选择的支付是否关闭
                if ($switch[$data['pay_type'] . '_switch'] == 0) {
                    // 再判断易支付是否关闭
                    if ($switch['epay_switch'] == 0) {
                        result(403, "支付已关闭！");
                    } else {// 易支付通道
                        if (empty($payConfig['epay_api']) || empty($payConfig['epay_appid']) || empty($payConfig['epay_key'])) {
                            result(403, "支付配置参数缺失！");
                        }
                        // 发起易支付进行付款
                        $this->epay($data);
                    }
                }
                switch ($data['pay_type']) {
                    case 'wxpay':// 微信支付
                        if (empty($payConfig['wxpay_mchid']) || empty($payConfig['wxpay_key']) || empty($payConfig['wxpay_appid'])) {
                            result(403, "支付配置参数缺失！");
                        }
                        break;
                    case 'qqpay':// QQ支付
                        if (empty($payConfig['qqpay_mchid']) || empty($payConfig['qqpay_key'])) {
                            result(403, "支付配置参数缺失！");
                        }
                        $this->qqpay($data);
                        break;
                    case 'alipay':// 支付宝支付
                        // 判读当前选择支付宝的接口
                        if ($payConfig['alipay_type'] == 0) {// 官方支付
                            if (empty($payConfig['alipay_private_id']) || empty($payConfig['alipay_private_key']) || empty($payConfig['alipay_public_key'])) {
                                result(403, "支付配置参数缺失！");
                            }
                        } else {// 当面付
                            if (empty($payConfig['alipayf2f_private_id']) || empty($payConfig['alipayf2f_private_key']) || empty($payConfig['alipayf2f_public_key'])) {
                                result(403, "支付配置参数缺失！");
                            }
                            $this->facepay($data);
                        }
                        break;
                    default:
                        result(403, "非法请求！");
                }
            }
        }
    }

    /**
     * 当面付通道
     * @param array $data 前台发送过来的授权信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function facepay(&$data = [])
    {
        $facepayInfo = Db::name('pay')->where('id', 1)->field('alipayf2f_private_id,alipayf2f_private_key,alipayf2f_public_key')->find();
        // 订单号生成
        $tradeNo = trade_no();
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
        $params->appID = $facepayInfo['alipayf2f_private_id'];
        $params->appPrivateKey = $facepayInfo['alipayf2f_private_key'];
        $params->appPublicKey = $facepayInfo['alipayf2f_public_key'];
//        $params->apiDomain = 'https://openapi.alipaydev.com/gateway.do'; // 设为沙箱环境，如正式环境请把这行注释
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        $requests = new \Yurun\PaySDK\AlipayApp\FTF\Params\QR\Request();
        // 商户订单号
        $requests->businessParams->out_trade_no = $tradeNo;
        // 价格
        $requests->businessParams->total_amount = $data['price'];
        // 产品标题
        $requests->businessParams->subject = '域名授权服务';
        //最晚付款时间,10分钟
        $requests->businessParams->timeout_express = "10m";
        try {
            $payData = $pay->execute($requests);
            if ($pay->checkResult()) {
                //异步通知回调地址
                $data['return_url'] = Request::domain() . url("Pay/facepayReturn");
                // 二维码链接
                $data['code_url'] = $payData["alipay_trade_precreate_response"]['qr_code'];
                // 赋值订单号
                $data['trade_no'] = $tradeNo;
                // 订单创建时间
                $data['create_time'] = time();
                // 设置缓存
                $data['user_id'] = request()->uid;
                Cache::set('authorization_facepay_' . Request::ip(), $data, 600);
                result(200, "下单成功，请支付！", $data);
            } else {
                result(403, $pay->checkResult());
            }
        } catch (\Exception $e) {
            result(403, $e->getMessage());
        }
    }

    /**
     * 当面付轮询地址
     */
    public function facepayReturn()
    {
        if (request()->isPost()) {
            // 获取缓存
            $data = Cache::get('authorization_facepay_' . Request::ip());
            $facepayInfo = Db::name('pay')->where('id', 1)->field('alipayf2f_private_id,alipayf2f_private_key,alipayf2f_public_key')->find();
            // 公共配置
            $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
            //APP ID
            $params->appID = $facepayInfo["alipayf2f_private_id"];
            //支付宝公钥
            $params->appPublicKey = $facepayInfo["alipayf2f_public_key"];
            //应用私钥
            $params->appPrivateKey = $facepayInfo["alipayf2f_private_key"];
            // SDK实例化，传入公共配置
            $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
            $requests = new \Yurun\PaySDK\AlipayApp\Params\Query\Request;
            // 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
            $requests->businessParams->out_trade_no = $data['trade_no'];
            // 调用接口
            $result = $pay->execute($requests);
            if ($pay->checkResult()) {
                // 支付成功
                if ($result["alipay_trade_query_response"]["trade_status"] == "TRADE_SUCCESS") {
                    //支付金额异常,退款操作
                    if ((float)$data['price'] !== (float)$result["alipay_trade_query_response"]["buyer_pay_amount"]) {
                        // 退款操作
                        $refundRequest = new \Yurun\PaySDK\AlipayApp\Params\Refund\Request;
                        $refundRequest->businessParams->out_trade_no = $data['trade_no'];
                        $refundRequest->businessParams->refund_amount = $result["alipay_trade_query_response"]["buyer_pay_amount"];
                        $refundRequest->businessParams->refund_reason = '支付金额异常!';
                        // 调用接口
                        $result = $pay->execute($refundRequest);
                        if ($pay->checkResult()) {
                            // 删除缓存
                            Cache::delete('authorization_facepay_' . Request::ip());
                            result(403, "支付金额异常,终止支付!");
                        }
                    } else {
                        $res = Authorization::create($data);
                        if ($res) {
                            // 删除缓存
                            Cache::delete('authorization_facepay_' . Request::ip());
                            result(201, "添加成功！");
                        } else {
                            result(403, "添加失败！");
                        }
                    }
                }
            }
        }
    }

    /**
     * QQ支付通道
     * @param array $data 前台发送过来的授权信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function qqpay(&$data = [])
    {
        // 查询QQ的支付信息
        $qqpayInfo = Db::name('pay')->where('id', 1)->field('qqpay_mchid,qqpay_key')->find();
        // 订单号生成
        $tradeNo = trade_no();
        $qqArr = [
            "mch_id" => $qqpayInfo['qqpay_mchid'],//商户号
            "notify_url" => Request::domain() . url("Pay/qqpayReturn"),//异步通知回调地址
            "key" => $qqpayInfo['qqpay_key'],//商户key
        ];
        $param = [
            "out_trade_no" => $tradeNo,// 订单号
            "trade_type" => "NATIVE",// 固定值
            "total_fee" => $data['price'],// 单位为分
            "body" => '域名授权服务',//订单标题
        ];
        //实例化
        $qq = new QQPay($qqArr);
        // 下单操作
        $result = $qq->unifiedOrder($param);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            //异步通知回调地址
            $data['return_url'] = Request::domain() . url("Pay/qqpayReturn");
            // 二维码链接
            $data['code_url'] = $result['code_url'];
            // 赋值订单号
            $data['trade_no'] = $tradeNo;
            // 订单创建时间
            $data['create_time'] = time();
            // 设置缓存
            $data['user_id'] = request()->uid;
            Cache::set('authorization_qqpay_' . Request::ip(), $data, 600);
            result(200, "下单成功，请支付！", $data);
        } else {
            result(400, "请求失败！");
        }
    }

    /**
     * QQ支付轮询地址
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function qqpayReturn()
    {
        if (request()->isGet()) {
            // 查询QQ的支付信息
            $qqpayInfo = Db::name('pay')->where('id', 1)->field('qqpay_mchid,qqpay_key')->find();
            $qqArr = [
                "mch_id" => $qqpayInfo['qqpay_mchid'],//商户号
                "key" => $qqpayInfo['qqpay_key'],//商户key
            ];
            //实例化
            $qq = new QQPay($qqArr);
            // 获取缓存
            $data = Cache::get('authorization_qqpay_' . Request::ip());
            $result = $qq->orderQuery($data['trade_no']);
            // 判断是否支付成功
            if ($result['trade_state'] == 'trade_state') {
                // 判断支付的金额是否正确
                if ((float)$result['cash_fee'] / 100 !== (float)$data['price']) {
                    result(403, "支付金额异常！");
                }
                $res = Authorization::create($data);
                if ($res) {
                    // 删除缓存
                    Cache::delete('authorization_qqpay_' . Request::ip());
                    result(201, "添加成功！");
                } else {
                    result(403, "添加失败！");
                }
            }
        }
    }

    /**
     * 易支付通道
     * @param array $data 前台发送过来的授权信息
     */
    public function epay($data = [])
    {
        // 查询支付配置信息
        $epayInfo = Db::name('pay')->where('id', 1)->field('epay_api,epay_appid,epay_key')->find();
        // 订单号生成
        $tradeNo = trade_no();
        //易支付的配置信息
        $epayConfig = [
            'partner' => (int)$epayInfo['epay_appid'],// 商户ID
            'key' => $epayInfo['epay_key'],// 商户KEY
            'sign_type' => strtoupper('MD5'),// 签名方式 不需修改
            'input_charset' => strtolower('utf-8'),// 字符编码格式 目前支持 gbk 或 utf-8
            'transport' => 'http',// 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
            'apiurl' => $epayInfo['epay_api']// 支付API地址
        ];
        require_once(root_path() . "extend/epay/lib/epay_submit.class.php");
        // 实例化易支付对象
        $epay = new \AlipaySubmit($epayConfig);
        // 构造要请求的参数数组
        $parameter = [
            'pid' => $epayInfo['epay_appid'],// 商户ID
            'type' => $data['pay_type'], // 支付方式
            'out_trade_no' => $tradeNo, // 订单号
            'notify_url' => Request::domain() . url('pay/epayNotify'), // 异步通知地址
            "return_url" => Request::domain() . url("pay/epayReturn"),//页面跳转同步通知页面路径
            'name' => '域名授权服务', // 商品名称
            'money' => $data['price'], // 商品价格
            'sign' => $epayInfo['epay_key'], // 签名字符串
            'sign_type' => strtoupper('MD5') // 签名字符串
        ];
        // 设置缓存
        $data['user_id'] = request()->uid;
        Cache::set('authorization_epay_' . Request::ip(), $data, 600);
        // 返回跳转链接
        $info['epay_url'] = $epay->buildRequestUrl($parameter);
        result(200, "获取跳转链接成功！", $info);
    }

    /**
     * 易支付服务器异步通知地址
     */
    public function epayNotify()
    {
        if (request()->isGet()) {
            // 查询支付配置信息
            $epayInfo = Db::name('pay')->where('id', 1)->field('epay_api,epay_appid,epay_key')->find();
            //易支付的配置信息
            $epayConfig = [
                'partner' => (int)$epayInfo['epay_appid'],// 商户ID
                'key' => $epayInfo['epay_key'],// 商户KEY
                'sign_type' => strtoupper('MD5'),// 签名方式 不需修改
                'input_charset' => strtolower('utf-8'),// 字符编码格式 目前支持 gbk 或 utf-8
                'transport' => 'http',// 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
                'apiurl' => $epayInfo['epay_api']// 支付API地址
            ];
            require_once(root_path() . "extend/epay/lib/epay_notify.class.php");
            // 实例化易支付对象
            $epay = new \AlipayNotify($epayConfig);
            $verifyNotify = $epay->verifyNotify();
            if ($verifyNotify) {
                // 接收数据
                $info = Request::get();
                // 支付成功
                if ($info['trade_status'] == 'TRADE_SUCCESS') {
                    // 获取缓存
                    $data = Cache::get('authorization_epay_' . Request::ip());
                    if ($info['money'] !== $data['price']) {
                        result(403, "支付金额异常！");
                    }
                    $res = Authorization::create($data);
                    if ($res) {
                        result(201, "添加成功！");
                    } else {
                        result(403, "添加失败！");
                    }
                }
            }
        }
    }

    /**
     * 回调地址
     */
    public function epayReturn()
    {
        if (request()->isGet()) {
            // 查询支付配置信息
            $epayInfo = Db::name('pay')->where('id', 1)->field('epay_api,epay_appid,epay_key')->find();
            //易支付的配置信息
            $epayConfig = [
                'partner' => (int)$epayInfo['epay_appid'],// 商户ID
                'key' => $epayInfo['epay_key'],// 商户KEY
                'sign_type' => strtoupper('MD5'),// 签名方式 不需修改
                'input_charset' => strtolower('utf-8'),// 字符编码格式 目前支持 gbk 或 utf-8
                'transport' => 'http',// 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
                'apiurl' => $epayInfo['epay_api']// 支付API地址
            ];
            require_once(root_path() . "extend/epay/lib/epay_notify.class.php");
            // 实例化易支付对象
            $epay = new \AlipayNotify($epayConfig);
            $verifyResult = $epay->verifyReturn();
            if ($verifyResult) {
                // 接收数据
                $info = Request::get();
                // 支付成功
                if ($info['trade_status'] == 'TRADE_SUCCESS') {
                    // 获取缓存
                    $data = Cache::get('authorization_epay_' . Request::ip());
                    if ((float)$info['money'] !== (float)$data['price']) {
                        return view('pay', ['code' => 403, 'msg' => '支付金额异常！']);
                    }
                    return view('pay', ['code' => 201, 'msg' => '支付成功！']);
                } else {
                    result(403, "回调地址验证失败！");
                }
            }
        }
    }
}
