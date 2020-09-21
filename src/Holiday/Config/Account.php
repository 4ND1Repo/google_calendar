<?php
namespace Gcalendar\Config;

use Google_Client;
use Google_Service_Calendar;

class Account {

  protected $connection;

  protected $path;

  protected $client;

  public function __construct($r){
    $this->connection = [
      'client_id' => NULL,
      'client_secret' => NULL,
      'project_id' => NULL,
      'redirect_uris' => [env("GOOGLE_REDIRECT_URI"),'urn:ietf:wg:oauth:2.0:oob'],
      'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
      'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
      'token_uri' => 'https://oauth2.googleapis.com/token',
    ];

    $this->path = [
      'dir' => realpath(__DIR__."/../../../../../.."),
      'folder' => "/storage/app",
      'file' => "credentials.json",
      'token_file' => 'token.json'
    ];

    // get data connection
    if(!$this->__init($r)){
      throw new \Exception("Please check your environment data. [GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_PROJECT_ID]", 401);
    }
  }

  private function __init($r){
    $this->connection = array_merge($this->connection,[
      'client_id' => env("GOOGLE_CLIENT_ID",NULL),
      'client_secret' => env("GOOGLE_CLIENT_SECRET",NULL),
      'project_id' => env("GOOGLE_PROJECT_ID",NULL),
    ]);

    if(!is_null($this->connection['client_id']) && !is_null($this->connection['client_secret']) && !is_null($this->connection['project_id'])){
      $client = new Google_Client();
      $client->setApplicationName('Get Holiday Calendar');
      $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
      if(!file_exists(implode("/", [$this->path['dir'], $this->path['folder'], $this->path['file']])))
        file_put_contents(implode("/", [$this->path['dir'], $this->path['folder'], $this->path['file']]), str_replace("\/","/",json_encode(["installed" => $this->connection])));
      $client->setAuthConfig(implode("/", [$this->path['dir'], $this->path['folder'], $this->path['file']]));
      $client->setAccessType('offline');
      $client->setPrompt('select_account consent');

      $tokenPath = str_replace("//","/",implode("/", [$this->path['dir'], $this->path['folder'], $this->path['token_file']]));
      if (file_exists($tokenPath)) {
          $accessToken = json_decode(file_get_contents($tokenPath), true);
          $client->setAccessToken($accessToken);
      }

      // If there is no previous token or it's expired.
      if ($client->isAccessTokenExpired()){
          // Refresh the token if possible, else fetch a new one.
          if ($client->getRefreshToken()) {
              $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
          } else {
              // Request authorization from the user.
              $authUrl = $client->createAuthUrl();
              if(!isset($r['code'])){
                header("Location: ".$authUrl);
                die;
              }
              $authCode = $r['code'];

              // Exchange authorization code for an access token.
              $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
              $client->setAccessToken($accessToken);

              // Check to see if there was an error.
              if (array_key_exists('error', $accessToken)) {
                  throw new Exception(join(', ', $accessToken));
              }
          }

          // Save the token to a file.
          if (!file_exists(dirname($tokenPath))) {
              mkdir(dirname($tokenPath), 0700, true);
          }
          file_put_contents($tokenPath, json_encode($client->getAccessToken()));
      }
      if($client)
        $this->client = $client;

      return true;
    }
  }

  public function get(){
    return $this->client;
  }
  
}
