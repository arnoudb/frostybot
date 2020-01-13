<?php

    // Scheduled task runner to take care of background tasks 

    class cron {

        // Run the crontab
        public static function run() {
            logger::info('Running cron...');
        }

        // Add a crob job
        public static function add($command, $interval = null, $expiry = null) {
            $db = new db();
            $data = [
                'command'   => $command,
                'expiry'    => $expiry,
                'interval'  => $interval
            ];
            return $db->insert('cron', $data);
        }

    }

?>