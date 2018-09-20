<?php
/**
 * Created by PhpStorm.
 * User: msxiu
 * Date: 2018/3/12
 * Time: 下午3:17
 */
return [
//    'ServerUrl'=>'http://120.78.64.2:11112/',
    'ServerUrl'=>'http://119.23.18.135:11114/',
    'getAllInfoUrl'=>'http://119.23.18.135:11114/',
    'msgId'=>strtolower(\RandString\Serial::GetRandStr(8)),
    'msgArr'=>['msgId'=>strtolower(\RandString\Serial::GetRandStr(8))],
    'dir'=>'/usr/local/var/ota/charger_adms/',
    'ServerApiName'=>[
        'setChargerStart'=>'command/setChargingStart',
        'setChargerEnd'=>'command/setChargingEnd',
        'getChargingInfo'=>'command/getChargingInfo',
        'getChargerStatus'=>'command/getChargerStatus',
        'getAllInfo'=>'command/getAllInfo',
        'createNewVersion'=>'version/createNewVersion',
        'changeDeviceVersion'=>'command/changeDeviceVersion',
        'getCurVersion'=>'command/getCurVersion',
    ],
];