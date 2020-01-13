--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 22:33:41 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: linkedorders
DROP TABLE IF EXISTS linkedorders;

CREATE TABLE linkedorders (
    uid      INTEGER      PRIMARY KEY AUTOINCREMENT,
    created  TIMESTAMP    DEFAULT (CURRENT_TIMESTAMP) 
                          NOT NULL,
    modified TIMESTAMP,
    id       VARCHAR (32) UNIQUE
                          NOT NULL,
    status   INTEGER      DEFAULT (0),
    stub     VARCHAR (20) NOT NULL,
    symbol   VARCHAR (30) NOT NULL,
    orders   TEXT
);


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
