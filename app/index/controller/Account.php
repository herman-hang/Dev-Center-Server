<?php
/**
 * 应用发布控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use think\Exception;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use app\index\model\User;
use app\index\validate\Account as AccountValidate;

class Account extends Base
{
    /**
     * 我的资料
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function material()
    {
        if (request()->isGet()) {
            $info = Db::name('user')->where('id', request()->uid)->field(['user', 'photo', 'nickname', 'sex', 'age', 'region', 'mobile', 'email', 'qq', 'introduction'])->find();
            if (!empty($info['mobile'])) {
                $info['mobile'] = $new_tel = substr_replace($info['mobile'], '****', 3, 4);
            }
            if (!empty($info['email'])) {
                $info['email'] = $new_tel = substr_replace($info['email'], '****', 3, 4);
            }
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 编辑我的资料
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function materialEdit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::only(['photo', 'nickname', 'sex', 'age', 'region', 'qq', 'introduction']);
            // 验证数据
            $validate = new AccountValidate();
            if (!$validate->sceneMaterialEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // xss过滤
            $data = $this->removeXSS($data);
            // 执行更新
            $user = User::find(request()->uid);
            $res = $user->save($data);
            if ($res) {
                result(200, "修改成功！");
            } else {
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 绑定手机发送验证码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bindMobileSendCode()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::only(['mobile', 'type']);
            // 生成验证码
            $code = codeStr(2);
            $data['code'] = $code;
            // 查询接口类型以及thinkAPI短信的模板ID
            $sms = Db::name('sms')->where('id', 1)->field('sms_type,bind_mobile,relieve_mobile')->find();
            // 查询网站名称
            $system = Db::name('system')->where('id', 1)->field('name')->find();
            if ($data['type'] == '1') {// 手机绑定验证码发送
                // 验证数据
                $validate = new AccountValidate();
                if (!$validate->sceneBindMobile()->check($data)) {
                    result(403, $validate->getError());
                }
                if ($sms['sms_type'] == '0') {// ThinkAPI接口
                    try {
                        $res = $this->sendMobile('', $data['mobile'], '0', $code, $sms['bind_mobile']);
                    } catch (\Exception $e) {
                        result(500, $e->getMessage());
                    }
                } else if ($sms['sms_type'] == '1') {
                    try {
                        $content = "【{$system['name']}】您正在进行手机绑定，验证码是：{$code}，验证码在五分钟内有效。如非本人操作，请忽略本短信";
                        $res = $this->sendMobile($content, $data['mobile']);
                    } catch (\Exception $e) {
                        result(500, $e->getMessage());
                    }
                }
            } else if ($data['type'] == '0') {// 手机解除绑定验证码发送
                // 查询当前的手机是否正确
                $user = Db::name('user')->where('id', request()->uid)->field('mobile')->find();
                if ($data['mobile'] !== $user['mobile']) {
                    result(403, "手机号码不正确！");
                }
                if ($sms['sms_type'] == '0') {// ThinkAPI接口
                    try {
                        $res = $this->sendMobile('', $data['mobile'], '0', $code, $sms['relieve_mobile']);
                    } catch (\Exception $e) {
                        result(500, $e->getMessage());
                    }
                } else if ($sms['sms_type'] == '1') {
                    try {
                        $content = "【{$system['name']}】您正在解除手机绑定，验证码是：{$code}，验证码在五分钟内有效。如非本人操作，请忽略本短信";
                        $res = $this->sendMobile($content, $data['mobile']);
                    } catch (\Exception $e) {
                        result(500, $e->getMessage());
                    }
                }
            }
            if ($res) {
                // 设置缓存
                Cache::set('send_mobile_code_' . request()->uid, $data, 300);
                result(200, "发送成功！");
            } else {
                result(403, "发送失败！");
            }
        }
    }

    /**
     * 绑定/解除手机号码
     * @throws \think\db\exception\DbException
     */
    public function bindMobile()
    {
        if (request()->isPost()) {
            // 接收手机号码
            $data = Request::only(['type', 'code']);
            // 获取缓存并删除
            $info = Cache::pull('send_mobile_code_' . request()->uid);
            if (empty($info)) {
                result(403, "验证码已过期！");
            }
            if ($data['code'] !== $info['code']) {
                result(403, "验证码错误！");
            }
            // 绑定手机号码
            if ($data['type'] == '1') {
                // 执行更新
                $res = Db::name('user')->where('id', request()->uid)->update(['mobile' => $info['mobile']]);
                if ($res) {
                    result(200, "绑定成功！");
                } else {
                    result(403, "绑定失败！");
                }
            } else if ($data['type'] == '0') {// 解除手机号码
                // 执行更新
                $res = Db::name('user')->where('id', request()->uid)->update(['mobile' => '']);
                if ($res) {
                    result(200, "解除成功！");
                } else {
                    result(403, "解除失败！");
                }
            }
        }
    }
}
