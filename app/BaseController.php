<?php
declare (strict_types=1);

namespace app;

use think\api\Client;
use think\App;
use think\exception\ValidateException;
use think\facade\Db;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 邮件发送
     * @param string $email 接收邮箱
     * @param string $title 邮件标题
     * @param string $content 邮件正文
     * @param string $user 接收邮件用户名（本站）
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sendEmail(string $email, string $title, string $content, string $user)
    {
        //查询邮件配置信息
        $info = Db::name('email')->where('id', 1)->find();
        //查询网站信息
        $system = Db::name('system')->where('id', 1)->field('name,logo')->find();
        if ($info) {
            // 本站域名
            $domain = request()->domain();
            // 本站LOGO
            $logo = $system['logo'];
            // 构造HTML模板
            $html = emailHtml($domain, $logo, $user, $content, $system['name']);
            //执行邮件发送
            $res = sendEmail($info['email'], $info['key'], $info['stmp'], $info['sll'], $system['name'], $title, $html, $email);
            if ($res) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * 手机验证码发送
     * @param string $content 需要发送的内容
     * @param string $mobile 待发送的手机号码
     * @param string $type 接口类型（1为短信宝，0为ThinkAPI）
     * @param string $code 验证码
     * @param int $tempId 短信模板ID，当$type=1的时候这个参数必须
     * @return bool
     * @throws \think\api\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sendMobile(string $content, string $mobile, $type = '1', $code = '', $tempId = 0)
    {
        //查询AppCode
        $sms = Db::name('sms')->where('id', 1)->find();
        if ($type == '0') {
            //实例化ThinkAPI短信接口
            $client = new Client($sms['app_code']);
            $res = $client->smsSend()
                ->withSignId($sms['sign_id'])
                ->withTemplateId($tempId)
                ->withPhone($mobile)
                ->withParams(json_encode(['code' => $code]))
                ->request();
            $res = $res['code'];
        } else if ($type == '1') {
            //调用发送函数
            $res = sendSms($sms['smsbao_account'], $sms['smsbao_pass'], $content, $mobile);
        }
        if ($res == 0) {
            return true;
        } else {
            return false;
        }
    }
}
