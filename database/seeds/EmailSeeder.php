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
                        <h4>Xin ch??o! {__TEN_SHOP__}</h4>
                        <p>
                            B???n nh???n ???????c email n??y v?? ch??ng t??i ???? nh???n ???????c y??u c???u ?????t l???i m???t kh???u cho t??i kho???n c???a b???n.
                        </p>
                        <p>
                             Vui l??ng b???m v??o link li??n k???t sau: <a href="{__LINK_RESET_PASSWORD__}" >???n v??o ????y</a>.
                        </p>
                        <p>
                            N???u b???n kh??ng y??u c???u ?????t l???i m???t kh???u, b???n kh??ng c???n th???c hi???n th??m h??nh ?????ng n??o.
                        </p>
                         <p>
                            Xin c???m ??n,
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
