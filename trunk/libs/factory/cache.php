<?php
require LIBPATH.'/system/Cache.php';
$_c = new Cache(CACHE_URL);
$cache = $_c->cache;
unset($_c);