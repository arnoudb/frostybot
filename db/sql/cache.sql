--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 17:41:08 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: cache
DROP TABLE IF EXISTS cache;

CREATE TABLE cache (
    uid       INTEGER      PRIMARY KEY AUTOINCREMENT,
    [key]     VARCHAR (32) UNIQUE
                           NOT NULL,
    permanent BOOLEAN      NOT NULL
                           DEFAULT (0),
    timestamp DATETIME     NOT NULL
                           DEFAULT (DATETIME('now') ),
    ttl       BIGINT       NOT NULL
                           DEFAULT (60),
    data      TEXT         NOT NULL
);


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
