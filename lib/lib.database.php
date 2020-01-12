<?php

    class db {

        private $database;
        private $conn;

        public function __construct($db = null) {
            $this->database = is_null($db) ? database : $db;
            $this->conn = new SQLite3($this->database);
            $this->conn->busyTimeout(5000);
            $this->conn->exec('PRAGMA journal_mode = wal;');
        }

        public function initialize() {
            foreach(glob('db/*.sql') as $sqlFile) {
                $filename = basename($sqlFile);
                $table = str_replace('.sql','',strtolower($filename));
                $this->exec('DROP TABLE '.$table.';');
                $sql = file_get_contents($sqlFile);
                logger::info('Initializing database: '.$sqlFile);
                $this->exec($sql);
            }     
            return true;   
        }

        public function exec($sql) {
            if ($result = $this->conn->exec($sql)) {
                return $result;
            }
            logger::error('SQLite Error: '.$this->conn->lastErrorMsg());
            return false;
        }

        public function query($sql) {
            $results = $this->conn->query($sql);
            if ($results !== false) { 
                $data = [];
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    $data[] = (object) $row;
                }
                return $data;
            }
            return false;
        }

        public function select($table, $data = []) {
            $data = (is_object($data) ? (array) $data : $data);
            $wherelist = [];
            foreach ($data as $key => $value) {
                $wherelist[] = "`".$key."`='".$value."'";
            }
            $sql = "SELECT * FROM `".$table."`".(count($wherelist) > 0 ? " WHERE ".implode(' AND ', $wherelist) : "").";";
            //logger::debug($sql);
            return $this->query($sql);
        }

        public function insert($table, $data) {
            $data = (is_object($data) ? (array) $data : $data);
            foreach ($data as $key => $value) {
                $collist[] = $key;
                $vallist[] = $value;
            }
            $sql = "INSERT INTO `".$table."` (`".implode("`,`", $collist)."`) VALUES ('".implode("','", $vallist)."');";
            //logger::debug($sql);
            return $this->exec($sql);
        }

        public function update($table, $data, $where) {
            $data = (is_object($data) ? (array) $data : $data);
            $where = (is_object($where) ? (array) $where : $where);
            $datalist = [];
            foreach ($data as $key => $value) {
                $datalist[] = "`".$key."`='".$value."'";
            }
            $wherelist = [];
            foreach ($where as $key => $value) {
                $wherelist[] = "`".$key."`='".$value."'";
            }
            $sql = "UPDATE `".$table."` SET ".implode(',', $datalist).(count($wherelist) > 0 ? " WHERE ".implode(' AND ', $wherelist) : "").";";
            //logger::debug($sql);
            return $this->exec($sql);
        }

        public function delete($table, $data = []) {
            $data = (is_object($data) ? (array) $data : $data);
            $wherelist = [];
            foreach ($data as $key => $value) {
                $wherelist[] = "`".$key."`='".$value."'";
            }
            $sql = "DELETE FROM `".$table."`".(count($wherelist) > 0 ? " WHERE ".implode(' AND ', $wherelist) : "").";";
            //logger::debug($sql);
            return $this->exec($sql);
        }

    }

?>