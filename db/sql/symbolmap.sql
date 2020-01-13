--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 17:42:16 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: symbolmap
DROP TABLE IF EXISTS symbolmap;

CREATE TABLE symbolmap (
    uid      INTEGER      PRIMARY KEY AUTOINCREMENT
                          NOT NULL,
    exchange VARCHAR (20) NOT NULL,
    symbol   VARCHAR (30) NOT NULL,
    mapping  VARCHAR (30) NOT NULL
);

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          1,
                          'bitmex',
                          'ETHUSD',
                          'ETH/USD'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          2,
                          'bitmex',
                          'BTCUSD',
                          'BTC/USD'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          3,
                          'bitmex',
                          'default',
                          'BTC/USD'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          4,
                          'deribit',
                          'ETHUSD',
                          'ETH-PERPETUAL'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          5,
                          'deribit',
                          'BTCUSD',
                          'BTC-PERPETUAL'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          6,
                          'deribit',
                          'default',
                          'BTC-PERPETUAL'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          7,
                          'ftx',
                          'ETHUSD',
                          'ETH-PERP'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          8,
                          'ftx',
                          'BTCUSD',
                          'BTC-PERP'
                      );

INSERT INTO symbolmap (
                          uid,
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          9,
                          'ftx',
                          'default',
                          'BTC-PERP'
                      );


-- Index: UQexsymbol
DROP INDEX IF EXISTS UQexsymbol;

CREATE UNIQUE INDEX UQexsymbol ON symbolmap (
    exchange ASC,
    symbol ASC
);


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
