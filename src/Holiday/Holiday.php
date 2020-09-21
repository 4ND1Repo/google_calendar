<?php
namespace Gcalendar;

use Illuminate\Http\Request;
use Gcalendar\Config\{Datas};

class Holiday{

  private $data;

  protected static $params;

  public static function get($r){
    self::checkParams();
 
    $client = (new Datas($r));
    $client::setParams(self::$params);
    return $client->event();
  }

  public static function maxResult($num = null){
    self::checkParams();
    if($num){
      self::$params['maxResults'] = $num;
    }
  }

  public static function group($by = null){
    self::checkParams();
    if($by){
      if(in_array(strtolower($by),['year','month','day','y','m','d']))
        self::$params['groupBy'] = strtolower($by);
    }
  }

  public static function checkParams(){
    if(!self::$params) self::$params = [];
  }
}
