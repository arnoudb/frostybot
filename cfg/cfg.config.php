<?php

    // General Settings

    const database = 'db/database.db';      // Database file location
    const logfile = 'log/frostybot.log';    // Log file location
    const debug = false;                    // Enable debugging (true/false)
    
    // Account settings

    const accounts = [                      
        'ftxmain' =>  [                                 // This is the account stub, which is used in <stub>:<command>, for example ftxmain:POSITIONS. NOTE: If you are using a subaccount on FTX, see the ftxsub config below instead.
            'description'   => 'FTX Main Account',      // This is a general description of the account, for your own information
            'exchange'      => 'ftx',                   // The exchange that this account is on (ftx/deribit/bitmex)
            'parameters'    =>  [
                'apiKey'    =>  '123CBA123CBA',         // Replace this with your actual FTX sub account API key (if you want to use it)
                'secret'    =>  'ABC123abc123',         // Replace this with your actual FTX sub account API secret (if you want to use it)
            ],
            'symbolmap'     =>  [                       // Symbol mappings to make commands consistent across all Exchanges
                'default'   =>  'BTC-PERP',             // This symbol is used when you don't include the "symbol=" parameter in your command
                'BTCUSD'    =>  'BTC-PERP',             // Map BTCUSD to BTC-PERP on the exchange
                'ETHUSD'    =>  'ETH-PERP',             // Map ETHUSD to ETH-PERP on the exchange
            ],
         ],
        'ftxsub' =>  [                                  // This is the account stub, which is used in <stub>:<command>, for example ftxsub:POSITIONS
            'description'   => 'FTX Sub Account',       // This is a general description of the account, for your own information
            'exchange'      => 'ftx',                   // The exchange that this account is on (ftx/deribit/bitmex)
            'parameters'    =>  [
                'apiKey'    =>  '123CBA123CBA',         // Replace this with your actual FTX sub account API key (if you want to use it)
                'secret'    =>  'ABC123abc123',         // Replace this with your actual FTX sub account API secret (if you want to use it)
                'headers'   => [
                    'FTX-SUBACCOUNT' => 'MySubAccount'  // Update this with the name of your sub account (if using a sub account). This is REQUIRED if using a sub account on FTX.
                ]
            ],
            'symbolmap'     =>  [                       // Symbol mappings to make commands consistent across all Exchanges
                'default'   =>  'BTC-PERP',             // This symbol is used when you don't include the "symbol=" parameter in your command
                'BTCUSD'    =>  'BTC-PERP',             // Map BTCUSD to BTC-PERP on the exchange
                'ETHUSD'    =>  'ETH-PERP',             // Map ETHUSD to ETH-PERP on the exchange
            ],
        ],
        'deribitmain' =>  [                             // This is the account stub, which is used in <stub>:<command>, for example deribitmain:POSITIONS
            'description'   => 'Deribit Main Account',  // This is a general description of the account, for your own information
            'exchange'      => 'deribit',               // The exchange that this account is on (ftx/deribit/bitmex)
            'parameters'    =>  [
                'apiKey'    =>  '123CBA123CBA',         // Replace this with your Deribit testnet account API key (if you want to use it)
                'secret'    =>  'ABC123abc123',         // Replace this with your Deribit testnet account API secret (if you want to use it)
            ],
            'symbolmap'     =>  [                       // Symbol mappings to make commands consistent across all Exchanges
                'default'   =>  'BTC-PERPETUAL',        // This symbol is used when you don't include the "symbol=" parameter in your command
                'BTCUSD'    =>  'BTC-PERPETUAL',        // Map BTCUSD to BTC-PERPETUAL on the exchange
                'ETHUSD'    =>  'ETH-PERPETUAL',        // Map ETHUSD to ETH-PERPETUAL on the exchange
            ],
        ],
        'deribittest' =>  [                             // This is the account stub, which is used in <stub>:<command>, for example deribittest:POSITIONS
            'description'   => 'Deribit Test Account',  // This is a general description of the account, for your own information
            'exchange'      => 'deribit',               // The exchange that this account is on (ftx/deribit/bitmex)
            'parameters'    =>  [
                'apiKey'    =>  '123CBA123CBA',         // Replace this with your Deribit testnet account API key (if you want to use it)
                'secret'    =>  'ABC123abc123',         // Replace this with your Deribit testnet account API secret (if you want to use it)
                'urls'      =>  [
                    'api'   =>  'https://test.deribit.com'   // Override the URL to use the testnet like this
                ]
            ],
            'symbolmap'     =>  [                       // Symbol mappings to make commands consistent across all Exchanges
                'default'   =>  'BTC-PERPETUAL',        // This symbol is used when you don't include the "symbol=" parameter in your command
                'BTCUSD'    =>  'BTC-PERPETUAL',        // Map BTCUSD to BTC-PERPETUAL on the exchange
                'ETHUSD'    =>  'ETH-PERPETUAL',        // Map ETHUSD to ETH-PERPETUAL on the exchange
            ],
        ],
        'bitmexmain' =>  [                              // This is the account stub, which is used in <stub>:<command>, for example bitmexmain:POSITIONS
            'description'   => 'Bitmex Main Account',   // This is a general description of the account, for your own information
            'exchange'      => 'bitmex',                // The exchange that this account is on (ftx/deribit/bitmex)
            'parameters'    =>  [
                'apiKey'    =>  '123CBA123CBA',         // Replace this with your Bitmex testnet account API key (if you want to use it)
                'secret'    =>  'ABC123abc123',         // Replace this with your Bitmex testnet account API secret (if you want to use it)
            ],
            'symbolmap'     =>  [                       // Symbol mappings to make commands consistent across all Exchanges
                'default'   =>  'BTC/USD',              // This symbol is used when you don't include the "symbol=" parameter in your command
                'BTCUSD'    =>  'BTC/USD',              // Map BTCUSD to BTC/USD on the exchange
                'ETHUSD'    =>  'ETH/USD',              // Map ETHUSD to ETH/USD on the exchange
            ],            
        ],
        'bitmextest' =>  [                              // This is the account stub, which is used in <stub>:<command>, for example bitmextest:POSITIONS
            'description'   => 'Bitmex Test Account',   // This is a general description of the account, for your own information
            'exchange'      => 'bitmex',                // The exchange that this account is on (ftx/deribit/bitmex)
            'parameters'    =>  [
                'apiKey'    =>  '123CBA123CBA',         // Replace this with your Bitmex testnet account API key (if you want to use it)
                'secret'    =>  'ABC123abc123',         // Replace this with your Bitmex testnet account API secret (if you want to use it)
                'urls'      =>  [
                    'api'   =>  'https://testnet.bitmex.com'    // Override the URL to use the testnet like this
                ]
            ],
            'symbolmap'     =>  [                       // Symbol mappings to make commands consistent across all Exchanges
                'default'   =>  'BTC/USD',              // This symbol is used when you don't include the "symbol=" parameter in your command
                'BTCUSD'    =>  'BTC/USD',              // Map BTCUSD to BTC/USD on the exchange
                'ETHUSD'    =>  'ETH/USD',              // Map ETHUSD to ETH/USD on the exchange
            ],            
        ],
    ];


?>
