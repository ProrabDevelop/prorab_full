<?php

namespace core\engine;

use app\std_models\user;

class sms{

    private static $smsc_login = 'FreeZaWeb';
    private static $smsc_pass = 'Nm001526';

    public static function init(user $user, $action){

        $code = str_pad(rand(0,999999), 6, 0, STR_PAD_LEFT);

        $message = 'код: '.$code.' Никому не сообщайте его!';

        $sms = \ORM::for_table('sms_codes')->create();
        $sms->user_id = $user->id;
        $sms->hash = uniqid();
        $sms->action = $action;
        $sms->code = $code;
        $sms->status = 0;
        $sms->save();

        //self::sender($user->phone, $message);

        return ['hash'=>$sms->hash,'code'=>$sms->code];

    }

    public static function confirm($hash, $code){

        $sms = \ORM::for_table('sms_codes')->where('hash', $hash)->find_one();

        if($sms){
            if($sms->code == $code){

                $sms->status = 1;
                $sms->save();
                return [
                    'user_id' => $sms->user_id,
                    'action' => $sms->action,
                ];
            }
        }

        return false;
    }


    protected static function sender($phone, $message){

        $addr = 'https://smsc.ru/sys/send.php?login='.self::$smsc_login.'&psw='.self::$smsc_pass.'&phones='.$phone.'&mes='.urlencode($message);

    }




}