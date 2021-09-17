<?php
/**
 * 支付类
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use app\index\middleware\Auth;
use app\index\model\Authorization as AuthorizationModel;
use qqpay\QQPay;
use think\facade\Cache;
use think\Exception;
use think\facade\Db;
use think\facade\Request;

class Pay
{
    /**
     * 检测登录中间件调用
     * @var string[]
     */
    protected $middleware = [Auth::class];

    /**
     * 选择支付方式
     * @param array $data 前台发送过来的授权信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function selectPay($data = [])
    {
        // 查询支付开关
        $switch = Db::name('switch')->where('id', 1)->find();
        // 查询支付配置信息
        $payConfig = Db::name('pay')->where('id', 1)->find();
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
        } else {
            switch ($data['pay_type']) {
                case 'wxpay':// 微信支付
                    if (empty($payConfig['wxpay_mchid']) || empty($payConfig['wxpay_key']) || empty($payConfig['wxpay_appid'])) {
                        result(403, "支付配置参数缺失！");
                    }
                    // 发起微信支付
                    $this->wxpay($data);
                    break;
                case 'qqpay':// QQ支付
                    if (empty($payConfig['qqpay_mchid']) || empty($payConfig['qqpay_key'])) {
                        result(403, "支付配置参数缺失！");
                    }
                    // 发起QQ支付
                    $this->qqpay($data);
                    break;
                case 'alipay':// 支付宝支付
                    // 判读当前选择支付宝的接口
                    if ($payConfig['alipay_type'] == 0) {// 官方支付
                        if (empty($payConfig['alipay_private_id']) || empty($payConfig['alipay_private_key']) || empty($payConfig['alipay_public_key'])) {
                            result(403, "支付配置参数缺失！");
                        }
                        // 发起支付宝支付
                        $this->alipay($data);
                    } else {// 当面付
                        if (empty($payConfig['alipayf2f_private_id']) || empty($payConfig['alipayf2f_private_key']) || empty($payConfig['alipayf2f_public_key'])) {
                            result(403, "支付配置参数缺失！");
                        }
                        // 发起当面付
                        $this->facepay($data);
                    }
                    break;
                default:
                    result(403, "非法请求！");
            }
        }
    }

    /**
     * 微信支付通道
     * @param array $data 前台发送过来的授权信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function wxpay(&$data = [])
    {
        $wxpayInfo = Db::name('pay')->where('id', 1)->field('wxpay_appid,wxpay_key,wxpay_mchid')->find();
        //生成订单
        $tradeNo = $data['order'];
        // 公共配置
        $params = new \Yurun\PaySDK\Weixin\Params\PublicParams();
        // 支付平台分配给开发者的应用ID
        $params->appID = $wxpayInfo['wxpay_appid'];
        // 微信支付分配的商户号
        $params->mch_id = $wxpayInfo['wxpay_mchid'];
        // API 密钥
        $params->key = $wxpayInfo['wxpay_key'];
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\Weixin\SDK($params);
        // 支付接口
        $requests = new \Yurun\PaySDK\Weixin\Native\Params\Pay\Request();
        $requests->body = $data['title']; // 商品描述
        $requests->out_trade_no = $tradeNo; // 订单号
        $requests->total_fee = $data['price'] * 100; // 订单总金额，单位为：分
        $requests->spbill_create_ip = Request::ip(); // 客户端ip
        //交易类型
        $requests->trade_type = "NATIVE";
        //异步通知地址
        $requests->notify_url = Request::domain() . '/' . $data['notify_url'];
        try {
            // 调用接口
            $result = $pay->execute($requests);
            $data['code_url'] = $result['$result'];
            // 赋值订单号
            $data['trade_no'] = $tradeNo;
            // 设置缓存
            $data['user_id'] = request()->uid;
            Cache::set('wxpay_' . Request::ip(), $data, 600);
            result(200, "下单成功，请支付！", $data);
        } catch (\Exception $e) {
            result(403, $e->getMessage());
        }
    }

    /**
     * 微信支付回调地址
     * @return bool true为支付成功 否则为支付失败
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function wxpayReturn()
    {
        if (request()->isGet()) {
            // 获取订单数据
            $data = Cache::pull('wxpay_' . Request::ip());
            $wxpayInfo = Db::name('pay')->where('id', 1)->field('wxpay_appid,wxpay_key,wxpay_mchid')->find();
            // 公共配置
            $params = new \Yurun\PaySDK\Weixin\Params\PublicParams();
            // 支付平台分配给开发者的应用ID
            $params->appID = $wxpayInfo['wxpay_appid'];
            // 微信支付分配的商户号
            $params->mch_id = $wxpayInfo['wxpay_mchid'];
            // API 密钥
            $params->key = $wxpayInfo['wxpay_key'];
            // SDK实例化，传入公共配置
            $pay = new \Yurun\PaySDK\Weixin\SDK($params);
            $requests = new \Yurun\PaySDK\Weixin\OrderQuery\Request;
            // 微信订单号，与商户订单号二选一
            $requests->transaction_id = $data['trade_no'];
            try {
                $result = $pay->execute($requests);
                if ($pay->checkResult()) {
                    //支付成功
                    if ($result['trade_state '] == "SUCCESS") {
                        if ((float)$result['cash_fee'] / 100 !== (float)$data['price']) {
                            // 支付金额异常，撤销订单
                            $request = new \Yurun\PaySDK\Weixin\Reverse\Request;
                            $request->transaction_id = $data['trade_no']; // 微信订单号，与商户订单号二选一
                            $request->total_fee = $data['price'] * 100; // 订单总金额，单位为分，只能为整数
                            $request->refund_fee = $data['price'] * 100; // 退款总金额，订单总金额，单位为分，只能为整数
                            $pay->execute($request);
                            if ($pay->checkResult()) {
                                result(200, "支付金额异常，退款成功！");
                            } else {
                                result(403, "支付金额异常，退款失败！");
                            }
                        }
                        return true;
                    }
                }
                return false;
            } catch (\Exception $e) {
                result(403, $e->getMessage());
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
        $tradeNo = $data['order'];
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
        $requests->businessParams->subject = $data['title'];
        //最晚付款时间,10分钟
        $requests->businessParams->timeout_express = "10m";
        try {
            $payData = $pay->execute($requests);
            if ($pay->checkResult()) {
                // 二维码链接
                $data['code_url'] = $payData["alipay_trade_precreate_response"]['qr_code'];
                // 赋值订单号
                $data['trade_no'] = $tradeNo;
                // 订单创建时间
                $data['create_time'] = time();
                // 设置缓存
                $data['user_id'] = request()->uid;
                Cache::set('facepay_' . Request::ip(), $data, 600);
                result(200, "下单成功，请支付！", $data);
            } else {
                result(403, $pay->getError());
            }
        } catch (Exception $e) {
            result(403, $e->getMessage());
        }
    }

    /**
     * 当面付回调地址
     * @return bool true为支付成功，否则为支付失败
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function facepayReturn()
    {
        if (request()->isGet()) {
            // 获取缓存
            $data = Cache::get('facepay_' . Request::ip());
            $facepayInfo = Db::name('pay')->where('id', 1)->field('alipayf2f_private_id,alipayf2f_private_key,alipayf2f_public_key')->find();
            // 公共配置
            $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
            //APP ID
            $params->appID = $facepayInfo["alipayf2f_private_id"];
            //支付宝公钥
            $params->appPublicKey = $facepayInfo["alipayf2f_public_key"];
            //应用私钥
            $params->appPrivateKey = $facepayInfo["alipayf2f_private_key"];
//            $params->apiDomain = 'https://openapi.alipaydev.com/gateway.do'; // 设为沙箱环境，如正式环境请把这行注释
            // SDK实例化，传入公共配置
            $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
            $requests = new \Yurun\PaySDK\AlipayApp\Params\Query\Request;
            // 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
            $requests->businessParams->out_trade_no = $data['trade_no'];
            try {
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
                            $refundRequest->businessParams->refund_reason = '支付金额异常！';
                            // 调用接口
                            $pay->execute($refundRequest);
                            if ($pay->checkResult()) {
                                // 删除缓存
                                Cache::delete('facepay_' . Request::ip());
                                result(403, "支付金额异常！");
                            }
                        }
                        return true;
                    }
                }
                return false;
            } catch (Exception $e) {
                result(403, $e->getMessage());
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
        $tradeNo = $data['order'];
        $qqArr = [
            "mch_id" => $qqpayInfo['qqpay_mchid'],//商户号
            "notify_url" => Request::domain() . '/' . $data['notify_url'],//异步通知回调地址
            "key" => $qqpayInfo['qqpay_key'],//商户key
        ];
        $param = [
            "out_trade_no" => $tradeNo,// 订单号
            "trade_type" => "NATIVE",// 固定值
            "total_fee" => $data['price'],// 单位为分
            "body" => $data['title'],//订单标题
        ];
        //实例化
        $qq = new QQPay($qqArr);
        // 下单操作
        $result = $qq->unifiedOrder($param);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            // 二维码链接
            $data['code_url'] = $result['code_url'];
            // 赋值订单号
            $data['trade_no'] = $tradeNo;
            // 订单创建时间
            $data['create_time'] = time();
            // 设置缓存
            $data['user_id'] = request()->uid;
            Cache::set('qqpay_' . Request::ip(), $data, 600);
            result(200, "下单成功，请支付！", $data);
        } else {
            result(400, "请求失败！");
        }
    }

    /**
     * QQ支付回调地址
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function qqpayReturn()
    {
        // 查询QQ的支付信息
        $qqpayInfo = Db::name('pay')->where('id', 1)->field('qqpay_mchid,qqpay_key')->find();
        $qqArr = [
            "mch_id" => $qqpayInfo['qqpay_mchid'],//商户号
            "key" => $qqpayInfo['qqpay_key'],//商户key
        ];
        //实例化
        $qq = new QQPay($qqArr);
        // 获取缓存
        $data = Cache::get('qqpay_' . Request::ip());
        $result = $qq->orderQuery($data['trade_no']);
        // 判断是否支付成功
        if ($result['trade_state'] == 'SUCCESS') {
            // 判断支付的金额是否正确
            if ((float)$result['cash_fee'] / 100 !== (float)$data['price']) {
                result(403, "支付金额异常！");
            }
            return true;
        }
        return false;
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
        $tradeNo = $data['order'];
        // 判断易支付接口的协议
        $preg = '/^http(s)?:\\/\\/.+/';
        if (preg_match($preg, $epayInfo['epay_api'])) {
            $httpType = 'https';
        } else {
            $httpType = 'http';
        }
        //易支付的配置信息
        $epayConfig = [
            'partner' => (int)$epayInfo['epay_appid'],// 商户ID
            'key' => $epayInfo['epay_key'],// 商户KEY
            'sign_type' => strtoupper('MD5'),// 签名方式 不需修改
            'input_charset' => strtolower('utf-8'),// 字符编码格式 目前支持 gbk 或 utf-8
            'transport' => $httpType,// 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
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
            'notify_url' => Request::domain() . url($data['notify_url']), // 异步通知地址
            "return_url" => Request::domain() . url($data['return_url']),//页面跳转同步通知页面路径
            'name' => $data['title'], // 商品名称
            'money' => $data['price'], // 商品价格
            'sign' => $epayInfo['epay_key'], // 签名字符串
            'sign_type' => strtoupper('MD5') // 签名字符串
        ];
        // 设置缓存
        $data['user_id'] = request()->uid;
        Cache::set('epay_' . $tradeNo, $data, 600);
        // 返回跳转链接
        $info['epay_url'] = $epay->buildRequestUrl($parameter);
        result(200, "获取跳转链接成功！", $info);
    }

    /**
     * 易支付回调地址
     * @param array $info 第三方服务器返回的数据
     * @return int -1为支付金额异常，0支付失败，1支付成功
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function epayReturn($info = [])
    {
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
            // 支付成功
            if ($info['trade_status'] == 'TRADE_SUCCESS') {
                // 获取缓存
                $data = Cache::get('epay_' . $info['out_trade_no']);
                if ((float)$info['money'] !== (float)$data['price']) {
                    return -1;
                }
                return 1;
            }
        }
        return 0;
    }

    /**
     * 易支付异步通知地址
     * @param array $info 返回的数据
     * @return bool true表示支付成功，否则表示支付失败
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function epayNotify($info = [])
    {
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
            // 支付成功
            if ($info['trade_status'] == 'TRADE_SUCCESS') {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 支付宝官方通道
     * @param array $data 前台发送过来的授权信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function alipay(&$data = [])
    {
        $alipayInfo = Db::name('pay')->where('id', 1)->field('alipay_private_id,alipay_public_key,alipay_private_key')->find();
        // 订单号生成
        $tradeNo = $data['order'];
        // 公共配置
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
        $params->appID = $alipayInfo['alipay_private_id'];
        $params->appPrivateKey = $alipayInfo['alipay_private_key'];
        $params->apiDomain = 'https://openapi.alipaydev.com/gateway.do'; // 设为沙箱环境，如正式环境请把这行注释
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        // 支付接口
        $requests = new \Yurun\PaySDK\AlipayApp\Page\Params\Pay\Request();
        $requests->notify_url = Request::domain() . url($data['notify_url']); // 支付后通知地址（作为支付成功回调，这个可靠）
        $requests->return_url = Request::domain() . url($data['return_url']); // 支付后跳转返回地址
        $requests->businessParams->out_trade_no = $tradeNo; // 商户订单号
        $requests->businessParams->total_amount = $data['price']; // 价格
        $requests->businessParams->subject = $data['title']; // 商品标题
        $requests->businessParams->timeout_express = '10m'; // 该笔订单允许的最晚付款时间
        $requests->businessParams->goods_type = 0; // 商品主类型：0—虚拟类商品，1—实物类商品（默认）
        try {
            // 赋值订单号
            $data['trade_no'] = $tradeNo;
            // 设置缓存
            $data['user_id'] = request()->uid;
            Cache::set('alipay_' . $tradeNo, $data, 600);
            // 获取跳转url
            $pay->prepareExecute($requests, $url);
            // 赋值跳转地址
            $data['alipay_url'] = $url;
            result(200, "下单成功，请付款！", $data);
        } catch (Exception $e) {
            result(403, $e->getMessage());
        }
    }

    /**
     * 支付宝官方支付回调地址
     * @param array $data 第三方服务器返回的数据
     * @return int -1为支付金额异常，0支付失败，1支付成功
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function alipayReturn($data = [])
    {
        $alipayInfo = Db::name('pay')->where('id', 1)->field('alipay_private_id,alipay_public_key,alipay_private_key')->find();
        // 公共配置
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
        $params->appPublicKey = $alipayInfo['alipay_public_key'];
        $params->appPrivateKey = $alipayInfo['alipay_private_key'];
        $params->apiDomain = 'https://openapi.alipaydev.com/gateway.do'; // 设为沙箱环境，如正式环境请把这行注释
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        try {
            if ($pay->verifyCallback($data)) {
                // 获取缓存
                $info = Cache::get('alipay_' . $data['out_trade_no']);
                if ((float)$data['total_amount'] !== (float)$info['price']) {
                    return -1;
                }
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 支付宝官方异步通知地址
     * @param array $data 第三方服务器返回的数据
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function alipayNotify($data = [])
    {
        $alipayInfo = Db::name('pay')->where('id', 1)->field('alipay_private_id,alipay_public_key,alipay_private_key')->find();
        // 公共配置
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams();
        $params->appPublicKey = $alipayInfo['alipay_public_key'];
        $params->appPrivateKey = $alipayInfo['alipay_private_key'];
        $params->apiDomain = 'https://openapi.alipaydev.com/gateway.do'; // 设为沙箱环境，如正式环境请把这行注释
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        if ($pay->verifyCallback($data)) {
            // 支付成功状态
            if ($data["trade_status"] == "TRADE_SUCCESS") {
                // 获取缓存
                $info = Cache::get('alipay_' . $data['out_trade_no']);
                // 支付的金额异常
                if ((float)$data['buyer_pay_amount'] !== (float)$info['price']) {
                    // 退款操作
                    $requests = new \Yurun\PaySDK\AlipayApp\Params\Refund\Request;
                    $requests->businessParams->out_trade_no = $info['trade_no']; // 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
                    $requests->businessParams->refund_amount = $info['price']; // 需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
                    $requests->businessParams->refund_reason = '支付金额异常';
                    // 调用接口
                    $pay->execute($requests);
                    if ($pay->checkResult()) {
                        result(200, "支付金额异常，退款成功！");
                    } else {
                        result(403, "支付金额异常，退款失败！");
                    }
                }
                return true;
            }
            return false;
        }
        return false;
    }
}
