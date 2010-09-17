<?php
require 'settings.php';

$cache = new Cache(EVENT_QUEUE);
Error::parray($cache->cache->_vd);
