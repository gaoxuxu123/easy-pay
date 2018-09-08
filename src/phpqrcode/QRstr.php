<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/8
 * Time: 0:04
 */

namespace easy\pay\phpqrcode;


class QRstr extends QRbase
{
    public static function set(&$srctab, $x, $y, $repl, $replLen = false) {
        $srctab[$y] = substr_replace($srctab[$y], ($replLen !== false)?substr($repl,0,$replLen):$repl, $x, ($replLen !== false)?$replLen:strlen($repl));
    }
}