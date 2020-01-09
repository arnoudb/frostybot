<?php

    function getCache($key,$timeout=false) {
        $cachefile = trim(cachedir,'/').'/'.($timeout === false ? 'perm' : 'temp').'.'.md5($key).'.json';
        if (file_exists($cachefile)) {
            $filetime = filemtime($cachefile);
            $curtime = time();
            $fileage = $curtime - $filetime;
            if (($fileage <= $timeout) || ($timeout === false)) {
                logger::debug('Cache hit: '.$key. ' ('.md5($key).')');
                return json_decode(file_get_contents($cachefile));
            } else {
                unlink($cachefile);
            }
        }
        logger::debug('Cache miss: '.$key);
        return false;
    }

    function setCache($key,$data,$permanent=false) {
        $cachefile = trim(cachedir,'/').'/'.($permanent === false ? 'temp' : 'perm').'.'.md5($key).'.json';
        if ((!file_exists($cachefile)) || ($permanent === true)) {
            file_put_contents($cachefile,json_encode($data,JSON_PRETTY_PRINT));
            return true;
        } 
        return false;
    }

    function flushCache($days, $permanent=false) {
        $total = 0;
        foreach(glob(trim(cachedir,'/').'/'.($permanent !== true ? 'temp.' : '').'*.json') as $cachefile) {      // Exchange output normalizers
            $filetime = filemtime($cachefile);
            $curtime = time();
            $fileage = $curtime - $filetime;
            $dayage = $fileage / 86400;
            if ($dayage > $days) {
                $total++;
                unlink($cachefile);
            }
        }
        logger::debug('Cache flush completed: '.$total.' files deleted');
        return $total;
    }

    // Run random garbage collection for cache files older than 5 days
    $randomgc = rand(0,100);
    if ($randomgc >= (100 - cachegcpct)) {
        logger::debug('Garbage collection triggered, flushing cache older than '.cachegcage.' days...');
        flushCache(cachegcage);
    }

?>