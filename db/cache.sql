--
-- File generated with SQLiteStudio v3.2.1 on Sun Jan 12 20:03:45 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: cache
DROP TABLE IF EXISTS cache;

CREATE TABLE cache (
    [key]     VARCHAR (32) PRIMARY KEY
                           UNIQUE
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
