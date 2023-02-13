#!/bin/sh

#at linux, $r variable should be wrapped in ''; because $r is not shell variable in '';
queue=`php -r '$r = (include "config/queue.php");echo $r["default"];'`
delay=2
memory=128
sleep=3
tries=2
timeout=600

echo "kill queue:work : $queue"
ps -aux|grep $queue|grep -v 'start-queue'|grep -v 'grep'|cut -c 9-15|xargs kill -9

echo "start $queue"
nohup php think queue:listen --queue $queue --delay $delay --memory $memory --sleep $sleep --tries $tries --timeout $timeout 2>&1

ps -aux | grep $queue