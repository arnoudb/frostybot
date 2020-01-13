--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 17:51:19 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: cron
DROP TABLE IF EXISTS cron;

CREATE TABLE cron (
    uid      INTEGER       PRIMARY KEY AUTOINCREMENT
                           NOT NULL,
    created  TIMESTAMP     DEFAULT (CURRENT_TIMESTAMP) 
                           NOT NULL,
    modified TIMESTAMP,
    interval               DEFAULT (0) 
                           NOT NULL,
    expiry   TIMESTAMP,
    status   INTEGER       DEFAULT (0),
    command  VARCHAR (200) NOT NULL,
    result   TEXT
);


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
