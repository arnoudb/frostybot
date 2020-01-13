--
-- File generated with SQLiteStudio v3.2.1 on Sun Jan 12 21:32:34 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: exchanges
DROP TABLE IF EXISTS exchanges;

CREATE TABLE exchanges (
    exchange    VARCHAR (20)  PRIMARY KEY
                              NOT NULL
                              UNIQUE,
    description VARCHAR (100) NOT NULL
);

INSERT INTO exchanges (
                          exchange,
                          description
                      )
                      VALUES (
                          'ftx',
                          'FTX'
                      );

INSERT INTO exchanges (
                          exchange,
                          description
                      )
                      VALUES (
                          'deribit',
                          'Deribit'
                      );

INSERT INTO exchanges (
                          exchange,
                          description
                      )
                      VALUES (
                          'bitmex',
                          'Bitmex'
                      );


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
