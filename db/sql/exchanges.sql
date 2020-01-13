--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 17:42:05 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: exchanges
DROP TABLE IF EXISTS exchanges;

CREATE TABLE exchanges (
    uid         INTEGER       PRIMARY KEY AUTOINCREMENT,
    exchange    VARCHAR (20)  NOT NULL
                              UNIQUE,
    description VARCHAR (100) NOT NULL
);

INSERT INTO exchanges (
                          uid,
                          exchange,
                          description
                      )
                      VALUES (
                          1,
                          'ftx',
                          'FTX'
                      );

INSERT INTO exchanges (
                          uid,
                          exchange,
                          description
                      )
                      VALUES (
                          2,
                          'deribit',
                          'Deribit'
                      );

INSERT INTO exchanges (
                          uid,
                          exchange,
                          description
                      )
                      VALUES (
                          3,
                          'bitmex',
                          'Bitmex'
                      );


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
