<?php
require 'settings.php';
$php->autoload('event');

for($i=0;$i<10;$i++)
{
    echo $i;
	$php->event->raise('test',$i,'say');
}
