<?php
// 应用公共文件
use think\exception\HttpResponseException;
use think\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * 邮件发送
 * @param string $email 登录邮箱
 * @param string $emailpaswsd 安全码/授权码
 * @param string $smtp 邮箱的服务器地址
 * @param string $sll 端口
 * @param string $emname 发件人昵称
 * @param string $title 邮件主题
 * @param string $content 邮件内容
 * @param string $toemail 收件人邮箱
 * @return bool
 */
function sendEmail(string $email, string $emailpaswsd, string $smtp, string $sll, string $emname, string $title, string $content, string $toemail)
{
    $mail = new PHPMailer(true);// Passing `true` enables exceptions
    try {
        //设定邮件编码
        $mail->CharSet = "UTF-8";
        // 调试模式输出
        $mail->SMTPDebug = 0;
        // 使用SMTP
        $mail->isSMTP();
        // SMTP服务器
        $mail->Host = $smtp;
        // 允许 SMTP 认证
        $mail->SMTPAuth = true;
        // SMTP 用户名  即邮箱的用户名
        $mail->Username = $email;
        // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->Password = $emailpaswsd;
        // 允许 TLS 或者ssl协议
        $mail->SMTPSecure = 'ssl';
        // 服务器端口 25 或者465 具体要看邮箱服务器支持
        $mail->Port = $sll;
        //发件人
        $mail->setFrom($email, $emname);
        // 收件人
        $mail->addAddress($toemail);
        // 可添加多个收件人
        //$mail->addAddress('ellen@example.com');
        //回复的时候回复给哪个邮箱 建议和发件人一致
        $mail->addReplyTo($toemail, $emname);
        //抄送
        //$mail->addCC('cc@example.com');
        //密送
        //$mail->addBCC('bcc@example.com');

        //发送附件
        // $mail->addAttachment('../xy.zip');// 添加附件
        // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');// 发送附件并且重命名

        // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $content;
        $mail->AltBody = '当前邮件客户端不支持HTML，请用浏览器登录邮箱查看内容！';
        // 发送邮件 返回状态
        $status = $mail->send();
        return $status;
    } catch (Exception $e) {
        echo '邮件发送失败: ', $mail->ErrorInfo;
    }
}


/**
 * 返回封装后的API数据到客户端
 * @param mixed $data 要返回的数据
 * @param integer $code 返回的code
 * @param mixed $msg 提示信息
 * @param string $type 返回数据格式
 * @param array $header 发送的Header信息
 * @return Response
 */
function result(int $code = 0, $msg = '', $data = [], string $type = '', array $header = []): Response
{
    $result = [
        'code' => $code,
        'msg' => $msg,
        'time' => time(),
        'data' => $data,
    ];

    $type = $type ?: 'json';
    $response = Response::create($result, $type)->header($header);

    throw new HttpResponseException($response);
}

/**
 * @param String $user 短信宝账号
 * @param String $pass MD5加密的密码
 * @param String $content 短信内容
 * @param string $phone 手机号码
 * @return string
 */
function sendSms(string $user, string $pass, string $content, string $phone)
{
    $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );
    $smsapi = "http://www.smsbao.com/"; //短信网关
    $sendurl = $smsapi . "sms?u=" . $user . "&p=" . $pass . "&m=" . $phone . "&c=" . urlencode($content);
    $result = file_get_contents($sendurl);
    return $statusStr[$result];
}

/**
 * 生成6位随机验证码
 * @param int $type 验证码类型，1为邮件验证码，2为短信验证码
 * @return false|string
 */
function codeStr($type = 1)
{
    //邮件验证码
    if ($type == 1) {
        $arr = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        shuffle($arr);
        $arr = array_flip($arr);
        $arr = array_rand($arr, 6);
        $res = '';
        foreach ($arr as $v) {
            $res .= $v;
        }
        return $res;
    } elseif ($type == 2) {//短信验证码
        $arr = array_merge(range('0', '9'));
        shuffle($arr);
        $arr = array_flip($arr);
        $arr = array_rand($arr, 6);
        $res = '';
        foreach ($arr as $v) {
            $res .= $v;
        }
        return $res;
    }
    return false;
}

/**
 * @param string $domain 网站域名，带http/https
 * @param string $logo 网站LOGO
 * @param string $user 接收邮件用户名（本站）
 * @param string $content 邮件内容
 * @param string $name 网站名称
 * @return mixed
 */
function emailHtml(string $domain, string $logo, string $user, string $content, string $name)
{
    $html = <<<EOT
<div style="position:relative;font-size:14px;height:auto;padding:15px 15px 10px 15px;z-index:1;zoom:1;line-height:1.7;"
	class="body">
	<div id="qm_con_body">
		<div id="mailContentContainer" class="qmbox qm_con_body_content qqmail_webmail_only" style="">
			<table style="font-family:'Microsoft YaHei';" width="800" cellspacing="0" cellpadding="0" border="0"
				bgcolor="#ffffff" align="center">
				<tbody>
					<tr>
						<td>
							<table style="font-family:'Microsoft YaHei';" width="800" height="48" cellspacing="0"
								cellpadding="0" border="0" bgcolor="#409EFF" align="center">
								<tbody>
									<tr>
										<td border="0" style="padding-left:20px;" height="48"
											align="center">
											<a href="{$domain}" target="_blank">
											<img src="{$logo}" alt="{$name}">
											</a>
										</td>
									</tr>
								</tbody>
							</table>

						</td>
					</tr>

					<tr>
						<td>
							<table
								style=" border:1px solid #edecec; border-top:none; padding:0 20px;font-size:14px;color:#333333;"
								width="800" cellspacing="0" cellpadding="0" border="0" align="left">
								<tbody>
									<tr>
										<td border="0" colspan="2"
											style=" font-size:16px;vertical-align:bottom;font-family:'Microsoft YaHei';"
											width="760" height="56" align="left">尊敬的
											<a target="_blank"
												style="font-size:16px; font-weight:bold;text-decoration: none;">{$user}</a>：
										</td>
									</tr>
									<tr>
										<td border="0" colspan="2" width="760" height="30" align="left">&nbsp;</td>
									</tr>
									<tr>
										<td border="0"
											style=" width:40px; text-align:left;vertical-align:middle; line-height:32px; float:left;"
											width="40" valign="middle" height="32" align="left"></td>
										<td border="0"
											style=" width:720px; text-align:left;vertical-align:middle;line-height:32px;font-family:'Microsoft YaHei';"
											width="720" valign="middle" height="32" align="left">
											{$content}
										</td>
									</tr>

									<tr>
										<td colspan="2"
											style="padding-bottom:16px; border-bottom:1px dashed #e5e5e5;font-family:'Microsoft YaHei';text-align: right;"
											width="720" height="14">{$name}</td>
									</tr>
									<tr>
										<td colspan="2"
											style="padding:8px 0 28px;color:#999999; font-size:12px;font-family:'Microsoft YaHei';"
											width="720" height="14">此为系统邮件请勿回复</td>
									</tr>
								</tbody>
							</table>

						</td>
					</tr>
				</tbody>
			</table>

			<style type="text/css">
				.qmbox style,
				.qmbox script,
				.qmbox head,
				.qmbox link,
				.qmbox meta {
					display: none !important;
				}

				.qmbox body {
					margin: 0 auto;
					padding: 0;
					font-family: Microsoft Yahei, Tahoma, Arial;
					color: #333333;
					background-color: #fff;
					font-size: 12px;
				}

				.qmbox a {
					color: #00a2ca;
					line-height: 22px;
					text-decoration: none;
				}

				.qmbox a:hover {
					text-decoration: underline;
					color: #00a2ca;
				}

				.qmbox td {
					font-family: 'Microsoft YaHei';
				}

				#mailContentContainer .txt {
					height: auto;
				}
			</style>
		</div>
	</div>
</div>
EOT;
    return $html;
}

/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name)
{
    $result = false;
    if(is_dir($dir_name)){
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DIRECTORY_SEPARATOR. $item)) {
                        delete_dir_file($dir_name . DIRECTORY_SEPARATOR. $item);
                    } else {
                        unlink($dir_name . DIRECTORY_SEPARATOR. $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }
    return $result;
}