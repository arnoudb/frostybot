--
-- File generated with SQLiteStudio v3.2.1 on Mon Jan 13 00:31:38 2020
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

INSERT INTO accounts (
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         'ftxmain',
                         'FTX Main Account',
                         'ftx',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123"
}'
                     );

INSERT INTO accounts (
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
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
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         'deribitmain',
                         'Deribit Main Account',
                         'deribit',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123"
}'
                     );

INSERT INTO accounts (
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
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
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
                         'bitmexmain',
                         'Bitmex Main Account',
                         'bitmex',
                         '{
    "apiKey": "123CBA123CBA",
    "secret": "ABC123abc123"
}'
                     );

INSERT INTO accounts (
                         stub,
                         description,
                         exchange,
                         parameters
                     )
                     VALUES (
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
