<?php
namespace Gcalendar\Config;

use Gcalendar\Config\{Account};

use Google_Service_Calendar;
use Google_Service_Calendar_CalendarList;
use DateTime;

class Datas {

  protected static $connection;

  protected static $params;

  public function __construct($r=null){
    try{
      self::$connection = (new Account($r))->get();
    } catch(\Exception $e){
      throw new \Exception("Something wrong when getting connection");
    }
  }

  public static function setParams($params = null){
    if($params){
      self::$params = $params;
    }
  }

  public static function event(){
    $service = new Google_Service_Calendar(self::$connection);

    // Print the next 10 events on the user's calendar.
    $calendarId = env("GOOGLE_CALENDAR_ID");
    $optParams = array(
      'maxResults' => self::$params['maxResults']??50,
      'orderBy' => 'startTime',
      'singleEvents' => true,
      'timeMin' => date('c'),
    );
    $results = $service->events->listEvents($calendarId, $optParams);
    $getEvents = $results->getItems();
    $events = [];

    if (!empty($getEvents)) {
        foreach ($getEvents as $event) {
          $start = $event->start->dateTime;
          if (empty($start)) {
            $start = $event->start->date;
          }
          $date = new DateTime($start." 00:00:00");
          if(isset(self::$params['groupBy'])){
            if(in_array(self::$params['groupBy'],["year", "y"])){
              $events[$date->format("Y")][] = [
                "event" => $event->getSummary(),
                "date" => $start
              ];
            } else if(in_array(self::$params['groupBy'],["month", "m"])){
              $events[$date->format("Y")][$date->format("m")][] = [
                "event" => $event->getSummary(),
                "date" => $start
              ];
            } else if(in_array(self::$params['groupBy'],["day", "d"])){
              $events[$date->format("Y")][$date->format("m")][$date->format("d")] = [
                "event" => $event->getSummary(),
                "date" => $start
              ];
            } else
              $events[] = [
                "event" => $event->getSummary(),
                "date" => $start
              ];
          } else 
            $events[] = [
              "event" => $event->getSummary(),
              "date" => $start
            ];
        }
    }

    return $events;
  }
}
