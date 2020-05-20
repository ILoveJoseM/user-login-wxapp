## 配置

````php
<?php
return [
    "user_login" => [
        "mini_program" => [
            "app_id" => env("MINI_PROGRAM_APP_ID", ""), //app_id
            "app_secret" => env("MINI_PROGRAM_APP_SECRET", ""), //app_secret
            "login_model" => UserWxapp::class
        ],
    ]
];

````

## 使用

````php
<?php
//入参
$params = [];
$response = \JoseChan\UserLogin\Handler\Login::register("wechat_mini_program", $params);
````