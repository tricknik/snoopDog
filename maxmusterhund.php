<?php
/*
  An example Bot using the snoopDog IRCBot Microframework
  Dmytri Kleiner <dk@trick.ca>

  This program is free software. 
  It comes without any warranty, to the extent permitted by
  applicable law. You can redistribute it and/or modify it under the
  terms of the Do What The Fuck You Want To Public License v2.
  See http://sam.zoy.org/wtfpl/COPYING for more details. 
*/

require('./snoopdog.php');

SnoopDog::run("tcp://irc.freenode.com:6667", function() {
  SnoopDog::command('USER snoopDog bot github.com/tricknik/snoopDog :IRC Logger Bot');
  SnoopDog::command('NICK MaxMusterHund', SnoopDog::$pong);
  SnoopDog::command('JOIN #snoopdog');
  SnoopDog::command('PRIVMSG #snoopdog :WHAT UP DOGS?!');
  SnoopDog::listen(function($line) {
     echo ">> " . implode('|',$line);
     if ($line && is_array($line)) {
       switch ($line[1]) {
        case 'PRIVMSG': 
          # can I haz log?
          $user = SnoopDog::parse_nick($line[0]);
          $logline = "<b>${user}</b>" . trim(implode(" ", array_slice($line, 3))) . "<br />\n";
          $logfile = dirname(__FILE__) . '/' . $line[2] . '_' . date("Y-m-d");
          echo ":: " . $logfile;
          file_put_contents($logfile, $logline, FILE_APPEND | LOCK_EX);
        case 'JOIN': 
          # holla at my homies
          $user = SnoopDog::parse_nick($line[0]);
          if ($user != 'MaxMusterHund') {
  	    SnoopDog::command('PRIVMSG #snoopdog :' . $user . ': What\'s Happening!!?');
          }
        break;
      }
    }
  });
});

