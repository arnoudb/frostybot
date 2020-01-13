<?php

    // Functions to manage the HTTP/HTTPS request whitelist

    class whitelist {

        private static function list() {
            $db = new db();
            return $db->query('SELECT * FROM whitelist;');
        }

        private static function add($ipAddress, $description='') {
            $db = new db();
            $data = [
                'ipAddress'     => $ipAddress,
                'description'   => $description,
                'canDelete'     => 1
            ];
            $db->insert('whitelist', $data);
        }

        private static function remove($ipAddress) {
            $db = new db();
            $data = [
                'ipAddress'     => $ipAddress,
                'canDelete'     => 1
            ];
            $db->delete('whitelist', $data);
        }

        public static function manage($params) {
            $list = self::list();
            foreach($list as $ip)  {
                $canDelete[$ip->ipAddress] = (bool) $ip->canDelete;
            }
            $add = (isset($params['add']) ? $params['add'] : false);
            $remove = (isset($params['remove']) ? $params['remove'] : (isset($params['delete']) ? $params['delete'] : false));
            $description = (isset($params['description']) ? $params['description'] : '');
            if (($add !== false) && (!in_array($add, array_keys($canDelete)))) {
                logger::info('Adding '.$add.' to the IP whitelist');
                self::add($add, $description);
            }
            if (($remove !== false) && (in_array($remove, array_keys($canDelete)))) {
                if ($canDelete[$remove] === false) {
                    logger::warning('You cannot delete address '.$remove.' from the whitelist because it is protected');
                } else {
                    logger::info('Removing '.$remove.' from the IP whitelist');
                    self::remove($remove);
                }
            }
            return self::list();
        }

        public static function validate($requestIP) {
            $list = array_extract(self::list(), 'ipAddress');
            if (in_array($requestIP, $list)) {
                return true;
            }
            logger::error('Request received from invalid address: ' . $requestIP);
            return false;
        }

    }


?>