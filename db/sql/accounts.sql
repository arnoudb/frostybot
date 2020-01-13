--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 17:45:33 2020
--
-- Text encoding used: System
--
PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: accounts
DROP TABLE IF EXISTS accounts;

CREATE TABLE accounts (
    uid         INTEGER       PRIMARY KEY AUTOINCREMENT,
    stub        VARCHAR (20)  UNIQUE
                              NOT NULL,
    description VARCHAR (100),
    exchange    VARCHAR (20)  NOT NULL,
    parameters  TEXT          NOT NULL
);

INSERT INTO accounts (
                         uid,
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         1,
                         'ftxmain',
                         'FTX Main Account',
                         'ftx',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123"
}'
                     );

INSERT INTO accounts (
                         uid,
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         2,
                         'ftxsub',
                         'FTX Sub Account',
                         'ftx',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123",
    "headers": {
        "FTX-SUBACCOUNT": "MySubAccount"
    }
}'
                     );

INSERT INTO accounts (
                         uid,
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         3,
                         'deribitmain',
                         'Deribit Main Account',
                         'deribit',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123"
}'
                     );

INSERT INTO accounts (
                         uid,
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         4,
                         'deribittest',
                         'Deribit Test Account',
                         'deribit',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123",
    "urls": {
        "api": "https:\/\/test.deribit.com"
    }
}'
                     );

INSERT INTO accounts (
                         uid,
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         5,
                         'bitmexmain',
                         'Bitmex Main Account',
                         'bitmex',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123"
}'
                     );

INSERT INTO accounts (
                         uid,
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         6,
                         'bitmextest',
                         'Bitmex Test Account',
                         'bitmex',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123",
    "urls": {
        "api": "https:\/\/testnet.bitmex.com"
    }
}'
                     );


COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
