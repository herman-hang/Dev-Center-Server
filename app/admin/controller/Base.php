<?php
/**
 * 模块公共控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use app\admin\middleware\Auth;
use app\BaseController;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use app\admin\model\AdminLog;
use think\facade\Filesystem;

class Base extends BaseController
{
    /**
     * 检测登录和权限中间件调用
     * @var string[]
     */
    protected $middleware = [Auth::class];

    /**
     * 记录管理员日志
     * @param string $content 日志内容
     * @param int $type 日志类型（1为登录日志，2为操作日志）
     * @param null $id 管理员ID
     * @throws \think\db\exception\DbException
     */
    public function log(string $content, int $type = 2, $id = null)
    {
        //删除大于60天的日志
        Db::name('admin_log')->where('create_time', '<= time', time() - (84600 * 60))->delete();
        //记录当前客户端IP地址
        $ip = Request::ip();
        //获取管理员ID
        if ($id == null) {
            $id = request()->uid;
        }
        //实例化对象
        $log = new AdminLog();
        //执行添加并过滤非数据表字段
        $log->save(['type' => $type, 'admin_id' => $id, 'content' => $content, 'ip' => $ip]);
    }


    /**
     * 上传文件
     * 支持文件name:image或者name:file
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload()
    {
        if (request()->isPost()) {
            // 获取表单上传文件
            $files = request()->file();
            // 查询文件存储类型
            $system = Db::name('system')->where('id', 1)->field('file_storage')->find();
            // 上传到本地服务器
            try {
                validate(['image' => 'filesize:156780|fileExt:jpg,png,gif,ico,bmp', 'file' => 'fileExt:zip,rar,7z,tar,gz'])->check($files);
                switch ($system['file_storage']) {
                    case "0":
                        $disk = "public"; //存储在本地
                        $url = request()->domain() . "/storage";
                        break;
                    case "1":
                        $disk = "aliyun"; //存储在阿里云
                        $url = Config::get('filesystem.disks.aliyun.url');
                        break;
                    case "2":
                        $disk = "qcloud"; //存储在腾讯云
                        $url = Config::get('filesystem.disks.qcloud.cdn');
                        break;
                    case "3":
                        $disk = "qiniu"; //存储在七牛云
                        $url = Config::get('filesystem.disks.qiniu.url');
                        break;
                    default:
                        result(403, "请求错误！");
                }
                $saveName = [];
                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $f) {
                            // 获取文件扩展名作存储路径
                            $fileType = $f->extension();
                            $saveName[] = $url . "/" . Filesystem::disk($disk)->putFile($fileType, $f);
                        }
                    } else {
                        // 获取文件扩展名作存储路径
                        $fileType = $file->extension();
                        $saveName[] = $url . "/" . Filesystem::disk($disk)->putFile($fileType, $file);
                    }
                }
                result(200, "上传成功！", $saveName);
            } catch (\think\exception\ValidateException $e) {
                result(500, $e->getMessage());
            }
        }
    }

    /**
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
            $logo = $domain . $system['logo'];
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
}
