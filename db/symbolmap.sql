--
-- File generated with SQLiteStudio v3.2.1 on Sun Jan 12 19:50:54 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: symbolmap
DROP TABLE IF EXISTS symbolmap;

CREATE TABLE symbolmap (
    exchange VARCHAR (20) NOT NULL,
    symbol   VARCHAR (30) NOT NULL,
    mapping  VARCHAR (30) NOT NULL
);

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'bitmex',
                          'ETHUSD',
                          'ETH/USD'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'bitmex',
                          'BTCUSD',
                          'BTC/USD'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'bitmex',
                          'default',
                          'BTC/USD'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'deribit',
                          'ETHUSD',
                          'ETH-PERPETUAL'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'deribit',
                          'BTCUSD',
                          'BTC-PERPETUAL'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'deribit',
                          'default',
                          'BTC-PERPETUAL'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'ftx',
                          'ETHUSD',
                          'ETH-PERP'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
                          'ftx',
                          'BTCUSD',
                          'BTC-PERP'
                      );

INSERT INTO symbolmap (
                          exchange,
                          symbol,
                          mapping
                      )
                      VALUES (
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
