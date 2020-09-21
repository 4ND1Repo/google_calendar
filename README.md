# google_calendar

This library for laravel

Spefication :
- php ^7.0
- laravel ^5
- php-curl

Environment :

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_PROJECT_ID=
GOOGLE_REDIRECT_URI=
GOOGLE_CALENDAR_ID=

How to use :

in Controller/Route add this line at the same function you call :
<?php
...
use Gcalendar\Holiday;
...


Function you can call :
- Holiday::get((\Illuminate\Http\Request)->all())
  Notes :
  - Please make sure this (\Illuminate\Http\Request)->all() to get request data


Option for get function (Use this option before get funtion) :
- Holiday::maxResult(10)
  Notes : 
  - This for max result of event from google calendar
- Holiday::group('year')
  Notes : 
  - This for group in array format (list : ['year', 'month', 'day', 'y', 'm', 'd'])


Notes :
- To get environment : 
  1. Go to : https://developers.google.com/calendar/quickstart/php
  2. Click "Enable the Google Calendar API"

- To get environment "GOOGLE_CALENDAR_ID" :
  1. Go to : https://developers.google.com/calendar/v3/reference/calendarList/list
  2. Insert all what you want in "Try this API" sidebar
  3. Scroll Down and Execute
