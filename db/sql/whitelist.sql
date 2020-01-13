--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 17:42:28 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: whitelist
DROP TABLE IF EXISTS whitelist;

CREATE TABLE whitelist (
    uid         INTEGER       PRIMARY KEY AUTOINCREMENT,
    ipAddress   VARCHAR (15)  UNIQUE
                              NOT NULL,
    description VARCHAR (100),
    canDelete   BOOLEAN       NOT NULL
                              DEFAULT (1) 
);

INSERT INTO whitelist (
                          uid,
                          ipAddress,
                          description,
                          canDelete
                      )
                      VALUES (
                          1,
                          '52.32.178.7',
                          'TradingView Server Address',
                          0
                      );

INSERT INTO whitelist (
                          uid,
                          ipAddress,
                          description,
                          canDelete
                      )
                      VALUES (
                          2,
                          '54.218.53.128',
                          'TradingView Server Address',
                          0
                      );

INSERT INTO whitelist (
                          uid,
                          ipAddress,
                          description,
                          canDelete
                      )
                      VALUES (
                          3,
                          '34.212.75.30',
                          'TradingView Server Address',
                          0
                      );

INSERT INTO whitelist (
                          uid,
                          ipAddress,
                          description,
                          canDelete
                      )
                      VALUES (
                          4,
                          '52.89.214.238',
                          'TradingView Server Address',
                          0
                      );


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
