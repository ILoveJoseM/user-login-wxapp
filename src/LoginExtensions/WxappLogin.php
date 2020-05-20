<?php
/**
 * Created by PhpStorm.
 * User: chenyu
 * Date: 2020-05-04
 * Time: 10:05
 */

namespace JoseChan\UserLogin\Wechat\MiniProgram\LoginExtensions;


use Illuminate\Database\Eloquent\Model;
use JoseChan\Wechat\MiniProgram\Application;
use JoseChan\UserLogin\Handler\LoginAbstract;

class WxappLogin extends LoginAbstract
{

    protected $auto_register = true;

    protected $info = [];

    public function login(array $form): Model
    {
        //获取配置
        $config = config("user_login");

        if (!$config || !isset($config['mini_program'])) {
            throw new \Exception("系统错误");
        }

        //获取用户信息
        $mini_program = new Application($config['mini_program']['app_id'], $config['mini_program']['app_secret']);

        if (!$info = $mini_program->login($form["code"])) {
            throw new \Exception("微信授权信息获取失败");
        }

        $this->info = $info;

        //获取账号表中的信息
        /** @var Model $account_model */
        $account_model = $config['mini_program']['login_model'];
        if (!class_exists($account_model)) {
            throw new \Exception("系统错误");
        }

        $user = $account_model::query()->where("open_id", "=", $info['openid'])->first();

        $user_id = $user->user_id;

        /** @var Model $user_model */
        $user_model = $config['jwt']['user_model'];
        if (!class_exists($user_model)) {
            throw new \Exception("系统错误");
        }

        $user = $user_model::query()->find($user_id);

        return $user;

    }

    public function register(array $form): Model
    {
        //获取配置
        $config = config("user_login");

        if (!$config || !isset($config['mini_program'])) {
            throw new \Exception("系统错误");
        }

        $user_info = $this->info;
        if(empty($user_info)){
            throw new \Exception("微信授权信息获取失败");
        }

        $account_model = $config['mini_program']['login_model'];
        $user_model = $config['jwt']['user_model'];
        if (!class_exists($account_model) || !class_exists($user_model)) {
            throw new \Exception("系统错误");
        }

        $data = [
            "nickname" => isset($user_info['nickname']) ? $user_info['nickname'] : "小程序用户" . time(),
            "sex" => isset($user_info['sex']) ? $user_info['sex'] : 0,
            "language" => isset($user_info['language']) ? $user_info['language'] : "未知",
            "city" => isset($user_info['city']) ? $user_info['city'] : "未知",
            "country" => isset($user_info['country']) ? $user_info['country'] : "未知",
            "province" => isset($user_info['province']) ? $user_info['province'] : "未知",
            "headimgurl" => isset($user_info['headimgurl']) ? $user_info['headimgurl'] : "",
            "phone" => isset($user_info['phone_number']) ? $user_info['phone_number'] : "",
            "channel_id" => isset($_SESSION['channel']) ? $_SESSION['channel'] : 1,
        ];

        $account = new $account_model();
        $account->openid = $user_info['openid'];
        $account->unionid = isset($user_info['unionid']) ? $user_info['unionid'] : "";

        /** @var Model $user */
        $user = new $user_model($data);

        $connection = $user->getConnection();
        $connection->beginTransaction();
        if ($user->save()) {
            $account->user_id = $user->id;
            if($account->save()){
                $connection->commit();
                return $user;
            }
        }

        $connection->rollBack();

        throw new \Exception("注册失败");
    }

    public function loginValidate(): array
    {
        return [
            'code' => 'required',
        ];
    }

    public function registerValidate(): array
    {
        return [];
    }

    public function getLoginData(array $form): array
    {
        return $form;
    }

    public function getRegisterData(array $form): array
    {
        return $this->info;
    }
}
