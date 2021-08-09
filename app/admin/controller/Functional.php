<?php
/**
 * 功能配置控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\api\Client;
use think\facade\Db;
use think\facade\Request;

class Functional extends Base
{
    /**
     * 获取支付配置信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pay()
    {
        //查询所有支付配置信息
        $info = Db::name('pay')->where('id', 1)->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 修改支付配置信息
     * @throws \think\db\exception\DbException
     */
    public function payEdit()
    {
        $data = Request::param();
        //当存在数据时执行更新数据
        $res = Db::name('pay')->where('id', 1)->update($data);
        //判断返回的值是否为true
        if ($res) {
            $this->log("修改了支付配置信息！");
            result(200, "修改成功！");
        } else {
            $this->log("修改支付配置信息失败！");
            result(200, "修改失败！");
        }
    }

    /**
     * 短信配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sms()
    {
        //查询短信配置信息
        $info = Db::name('sms')->where('id', 1)->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 编辑短信配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function smsEdit()
    {
        // 接收数据
        $data = Request::param();
        //查询短信宝密码
        $info = Db::name('sms')->where('id', 1)->field('smsbao_pass,app_code')->find();
        //对密码进行MD5算法加密
        if ($data['smsbao_pass'] !== $info['smsbao_pass']) {
            $data['smsbao_pass'] = md5($data['smsbao_pass']);
        }
        if ($data['app_code'] !== $info['app_code']) {
            $data['app_code'] = md5($data['app_code']);
        }
        //当存在数据时执行更新数据
        $res = Db::name('sms')->strict(false)->where('id', 1)->update($data);
        if ($res) {
            $this->log("修改了短信配置信息！");
            result(200, "修改成功！");
        } else {
            $this->log("修改短信配置信息失败！");
            result(403, "修改失败！");
        }
    }

    /**
     * 短信测试
     * @throws \think\api\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function testSms()
    {
        // 接收数据
        $data = Request::param();
        //调用随机验证码方法
        $code = codeStr(2);
        //验证码有效时间(单位：分钟)
        $time = 5;
        //think:ThinkAPI接口，smsbao:短信宝接口
        if ($data['type'] == "think") {
            //查询AppCode
            $sms = Db::name('sms')->where('id', 1)->field('app_code')->find();
            //利用正则表达式检测当前的密码是否为MD5字符串
            if (!preg_match("/^[a-z0-9]{32}$/", $data['app_code'])) {
                //对密码进行MD5算法加密
                $data['app_code'] = md5($data['app_code']);
            }
            //实例化ThibkAPI短信接口
            $client = new Client($sms['app_code']);
            $res = $client->smsSend()
                ->withSignId($data['sign_id'])
                ->withTemplateId('2')
                ->withPhone($data['think_phone'])
                ->withParams(json_encode(['code' => $code]))
                ->request();
            $res = $res['code'];
        } else {
            //利用正则表达式检测当前的密码是否为MD5字符串
            if (!preg_match("/^[a-z0-9]{32}$/", $data['smsbao_pass'])) {
                //对密码进行MD5算法加密
                $data['smsbao_pass'] = md5($data['smsbao_pass']);
            }
            //自定义测试短信内容
            $content = "【测试】这是一条测试内容，您的验证码是{$code}，在{$time}分钟有效。";
            //调用发送函数
            $res = sendSms($data['smsbao_account'], $data['smsbao_pass'], $content, $data['smsbao_phone']);
        }
        if ($res == 0) {
            $this->log("测试发送短信！");
            result(200, "发送成功！");
        } else {
            $this->log("测试发送短信！");
            result(403, "发送失败！");
        }
    }

    /**
     * 邮件配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function email()
    {
        //查询邮件配置信息
        $info = Db::name('email')->where('id', 1)->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 编辑邮件配置
     * @throws \think\db\exception\DbException
     */
    public function emailEdit()
    {
        // 接收数据
        $data = Request::param();
        $res = Db::name('email')->strict(false)->where('id', 1)->update($data);
        //判断返回的值是否为true
        if ($res) {
            $this->log("修改了邮件配置信息！");
            result(200, "修改成功！");
        } else {
            $this->log("修改邮件配置信息失败！");
            result(200, "修改失败！");
        }
    }

    /**
     * 测试邮件发送
     * @throws \think\db\exception\DbException
     */
    public function testEmail()
    {
        //接收前台信息
        $data = Request::param();
        //数据模拟定义
        $name = "我叫测试";
        $title = "这是邮件发送测试标题";
        $content = "我是邮件发送测试的内容！";
        //执行测试发送
        $res = sendEmail($data['email'], $data['key'], $data['stmp'], $data['sll'], $name, $title, $content, $data['test_email']);
        if ($res) {
            $this->log("测试发送邮件！");
            result(200, "发送成功！");
        } else {
            $this->log("测试发送邮件！");
            result(403, "发送失败！");
        }
    }

    /**
     * 第三方登录配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function thirdparty()
    {
        //查询第三方登录配置信息
        $info = Db::name('thirdparty')->where('id', 1)->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 第三方登录配置编辑
     * @throws \think\db\exception\DbException
     */
    public function thirdpartyEdit()
    {
        //接收前台传过来的值
        $data = Request::param();
        //执行更新操作
        $res = Db::name('thirdparty')->where('id', 1)->update($data);
        //判断是否操作成功，true为操作成功
        if ($res) {
            result(200, "修改成功！");
        } else {
            result(403, "修改失败！");
        }
    }
}
