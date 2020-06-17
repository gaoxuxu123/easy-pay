##安装

```

composer require yii2-alipay/alipay dev-master

或者在composer.json中加入

 "require": {

        "yii2-alipay/alipay": "dev-master"
}

```
更新依赖 ``` composer update ```

##使用说明

##DEMO

```

//电脑支付 跳转网页
    public function actionAliPay()
    {
        $payRequestBuilder = new AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody('测试订单');
        $payRequestBuilder->setSubject('测试商品');
        $payRequestBuilder->setTotalAmount(0.01);
        $payRequestBuilder->setOutTradeNo('2018090554564651515645645'.time());

        $config = [
            //应用ID,您的APPID。
            'app_id' => "*",
            //商户私钥
            'merchant_private_key' => "*",
            //异步通知地址
            'notify_url' => "http://你的域名/pay/ali-notify",
            //同步跳转
            'return_url' => "http://你的域名/pay/ali-return",
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type'=>"RSA2",
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "*",
        ];
        $aop = new AlipayTradeService($config);

        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        //输出表单
        var_dump($response);
    }


```
```

//支付宝二维码支付 参考当面付
    public function actionAliScan()
    {
        $payRequestBuilder = new AlipayTradePrecreateContentBuilder();
        $payRequestBuilder->setBody('测试订单');
        $payRequestBuilder->setSubject('测试商品');
        $payRequestBuilder->setTotalAmount(0.01);
        $payRequestBuilder->setOutTradeNo('2018090554564651515645645'.time());
        $payRequestBuilder->setTimeExpress('30m'); // 支付超时，线下扫码交易定义为30分钟

        $config = [
            //应用ID,您的APPID。
            'app_id' => "*",
            //商户私钥
            'merchant_private_key' => "*",
            //异步通知地址
            'notify_url' => "http://你的域名/pay/ali-notify",
            //同步跳转
            'return_url' => "http://你的域名/pay/ali-return",
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type'=>"RSA2",
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "*",
        ];

        $aop = new AlipayTradeService($config);

        $response = $aop->qrcodePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //获取 到结果的 qr_code 使用phpqrcode 或者 qrcode.js生成二维码就行了
        /**
         * 注意扫码支付使用的使用需要在界面轮询订单的状态进行界面的跳转
         */
        $result['code_url'] = $response['qr_code'];
        return $this->render('pay',['data' => $result]);
    }

```
```

//微信扫码支付
    public function actionWxPay()
    {
        $input = new WxPayUnifiedOrder();
        $input->SetBody("测试订单");
        $input->SetAttach("测试商品");
        //微信只支持最多32为订单号
        $input->SetOut_trade_no(substr('2018090554564651515645645'.time(),0,32));
        $input->SetTotal_fee("1");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/notify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("123456789");

        $config = new WxPayConfig();
        $config->SetAppId('*');//appid
        $config->SetMerchantId('*');//商户号
        $config->SetKey('*');//key

        $result = WxPayApi::unifiedOrder($config, $input);
        return $this->render('pay',['data' => $result]);

    }

```
```
//二维码获取
    public function actionGetCode()
    {
        $url = urldecode($_GET["data"]);
        QRcode::png($url);
    }

```
```
 //支付宝同步 , 扫码支付是没有同步回调的
    public function actionAliReturn()
    {
        #file_put_contents(getcwd().'/log.txt',json_encode($_GET)."\r\n",FILE_APPEND);

        $parameter = [
            "out_trade_no"      => \Yii::$app->request->get('out_trade_no'), //商户订单编号；
            "trade_no"          => \Yii::$app->request->get('trade_no'),     //支付宝交易号；
            "total_amount"         =>  \Yii::$app->request->get('total_amount'),    //交易金额；
            "trade_status"      =>  \Yii::$app->request->get('trade_status'), //交易状态
            "notify_id"         =>  \Yii::$app->request->get('notify_id'),    //通知校验ID。
            "notify_time"       =>  \Yii::$app->request->get('notify_time'),  //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
            "buyer_logon_id"       =>  \Yii::$app->request->get('buyer_logon_id'),  //买家支付宝帐号；
        ];

        if (\Yii::$app->request->post('trade_status') == 'TRADE_SUCCESS') {


            //逻辑处理

            //跳转成功界面
            return $this->render('success');

        }else{

            //跳转失败界面
            return $this->render('fail');
        }

    }
    //支付宝异步
    public function actionAliNotify()
    {
        //日志记录 用于查看支付宝异步返回的数据
        #file_put_contents(getcwd().'/log.txt',json_encode($_POST)."\r\n",FILE_APPEND);

        $parameter = [
            "out_trade_no"      => \Yii::$app->request->post('out_trade_no'), //商户订单编号；
            "trade_no"          => \Yii::$app->request->post('trade_no'),     //支付宝交易号；
            "total_amount"         =>  \Yii::$app->request->post('total_amount'),    //交易金额；
            "trade_status"      =>  \Yii::$app->request->post('trade_status'), //交易状态
            "notify_id"         =>  \Yii::$app->request->post('notify_id'),    //通知校验ID。
            "notify_time"       =>  \Yii::$app->request->post('notify_time'),  //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
            "buyer_logon_id"       =>  \Yii::$app->request->post('buyer_logon_id'),  //买家支付宝帐号；
        ];

        if (\Yii::$app->request->post('trade_status') == 'TRADE_SUCCESS') {

            //逻辑处理

            //start



            //end


            //固定返回，请勿修改
            echo "success";
        }else{

            //固定返回，请勿修改
            echo "fail";
        }

    }

    //微信异步通知
    public function actionWxNotify()
    {
        $xmlData = file_get_contents('php://input');
        $data = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        //日志记录 用于查看微信支付异步返回的数据
        #file_put_contents(getcwd().'/log.txt',json_encode($data)."\r\n",FILE_APPEND);

        if($data['result_code'] == 'SUCCESS'){

            //逻辑处理
            //start
            $parameter = [

                'out_trade_no' => $data['out_trade_no'],
                'trade_no' => $data['transaction_id'],
                'total_fee' => $data['total_fee'],
                'sign' => $data['sign'],
                'openid' => $data['openid'],
                'mch_id' => $data['mch_id'],
                'time_end' => $data['time_end'],
                'bank_type' => $data['bank_type'],
                'nonce_str' => $data['nonce_str'],
                'fee_type'=> $data['fee_type'],
                'trade_type' => $data['trade_type']
            ];

            //end

            //固定返回，请勿修改
            $return = ['return_code'=>'SUCCESS','return_msg'=>'OK'];
            $xml = '<xml>';
            foreach($return as $k=>$v){
                $xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
            }
            $xml.='</xml>';
            echo $xml;
        }
    }

```

```

二维码界面代码
<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">支付宝扫码支付</div><br/>
<img alt="模式二扫码支付" src="<?=\yii\helpers\Url::toRoute(['/pay/get-code','data' => $data['code_url']])?>" style="width:150px;height:150px;"/>

<!--<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">微信扫码支付</div><br/>
<img alt="模式二扫码支付" src="<?/*=\yii\helpers\Url::toRoute(['/pay/get-code','data' => $data['code_url']])*/?>" style="width:150px;height:150px;"/>-->


```
