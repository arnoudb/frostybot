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

        public function exec($sql) {
            return $this->conn->exec($sql);
        }

        public function query($sql) {
            $results = $this->conn->query($sql);
            $data = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $data[] = (object) $row;
            }
            return $data;
        }

    }

?>