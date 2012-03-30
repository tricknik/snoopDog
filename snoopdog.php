<?php

Class SnoopDog {

  public static $server;
  public static $pong;

  public static function run($uri, $callback) {
    SnoopDog::$server = stream_socket_client($uri);
    SnoopDog::$pong = function($ping) {
      $pong = explode(':',$ping);
      SnoopDog::command('PONG '.$pong[1]);
    };
    $callback();
    fclose(SnoopDog::$server);
  }
 
  public static function command($data, $callback=false) {
    echo $data."\n";
    fwrite(SnoopDog::$server, $data."\n");
    if ($callback) {
      $callback(fread(SnoopDog::$server, 256));
    }
  }

  public static function listen($callback) {
    while (!feof(SnoopDog::$server)) {
      $data = fgets(SnoopDog::$server, 256);
      if ($data) {
        $line = explode(' ', $data); 
        switch ($line[0]) {
          case 'PING':
            $pong = SnoopDog::$pong;
            $pong($data);
          break;
          default:
            $callback($line);
          break;
        }
      }
    }
  }
}

