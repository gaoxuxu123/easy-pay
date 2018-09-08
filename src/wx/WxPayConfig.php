<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 23:19
 */

namespace easy\pay\wx;


class WxPayConfig
{
    public $values;

    public function SetAppId($value)
    {
        $this->values['appid'] = $value;
    }

    public function GetAppId()
    {
        return $this->values['appid'];
    }

    public function SetMerchantId($value)
    {
        $this->values['mch_id'] = $value;
    }

    public function GetMerchantId()
    {
        return $this->values['mch_id'];
    }

    public function SetKey($value)
    {
        $this->values['key'] = $value;
    }

    public function GetKey()
    {
        return $this->values['key'];
    }
    public function GetNotifyUrl()
    {
        return "";
    }
    public function GetSignType()
    {
        return "HMAC-SHA256";
    }
    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    public function GetReportLevenl()
    {
        return 1;
    }
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var unknown_type
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
    }
}