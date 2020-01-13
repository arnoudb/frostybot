--
-- File generated with SQLiteStudio v3.2.1 on Sun Jan 12 20:04:04 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: accounts
DROP TABLE IF EXISTS accounts;

CREATE TABLE accounts (
    stub        VARCHAR (20)  UNIQUE
                              NOT NULL
                              PRIMARY KEY,
    description VARCHAR (100),
    exchange    VARCHAR (20)  NOT NULL,
    parameters  TEXT          NOT NULL
);


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
