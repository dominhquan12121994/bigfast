<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('system_email_template')->insert([
            'name'    => 'Reset Password',
            'subject' => 'Reset Password',
            'content' => 
                '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
                    <meta name="x-apple-disable-message-reformatting">
                    <title>Example</title>
                    <style>
                        body {
                            background-color:#fff;
                            color:#222222;
                            margin: 0px auto;
                            padding: 0px;
                            height: 100%;
                            width: 100%;
                            font-weight: 400;
                            font-size: 15px;
                            line-height: 1.8;
                        }
                        .continer{
                            width:600px;
                            margin-left:auto;
                            margin-right:auto;
                            background-color:#efefef;
                            padding:30px;
                        }
                        .btn{
                            padding: 5px 15px;
                            display: inline-block;
                        }
                        .btn-primary{
                            border-radius: 3px;
                            background: #0b3c7c;
                            color: #fff;
                            text-decoration: none;
                        }
                        .btn-primary:hover{
                            border-radius: 3px;
                            background: #4673ad;
                            color: #fff;
                            text-decoration: none;
                        }
                    </style>
                </head>
                <body>
                    <div class="continer">
                        <h4>Xin chào! {__TEN_SHOP__}</h4>
                        <p>
                            Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.
                        </p>
                        <p>
                             Vui lòng bấm vào link liên kết sau: <a href="{__LINK_RESET_PASSWORD__}" >Ấn vào đây</a>.
                        </p>
                        <p>
                            Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.
                        </p>
                         <p>
                            Xin cảm ơn,
                        </p>
                         <p>
                            BigFast
                        </p>
                    </div>
                </body>
                </html>',
        ]);
    }
}
