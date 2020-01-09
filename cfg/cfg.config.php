<?php

    // General Settings

    const database = 'db/database.db';      // Database file location
    const logfile = 'log/frostybot.log';    // Log file location
    const cachedir = 'cache/';              // Directory to store cache files
    const cachegcage = 5;                   // Delete cache files older than 5 days during garbage collection
    const cachegcpct = 20;                  // Garbage collection probability percentage
    const debug = false;                    // Enable debugging (true/false)
    const whitelist = [                     // Whitelist IP addresses for URL requests to bot (does not affect CLI requests). Tradingview addresses here by default. DO NOT remove the default addresses.
        //'127.0.0.1',                      // Uncomment this line if you want to use local CURL requests
        //'<your ip address>',              // Uncomment and replace with your home public IP address to run remove CURL or browser requests. Use https://www.whatismyip.com/ to find your local public IP.
        '52.89.214.238',                    // This is a Tradingview webhook server address - DO NOT DELETE 
        '34.212.75.30',                     // This is a Tradingview webhook server address - DO NOT DELETE 
        '54.218.53.128',                    // This is a Tradingview webhook server address - DO NOT DELETE 
        '52.32.178.7',                      // This is a Tradingview webhook server address - DO NOT DELETE 
    ];
    const cryptodatum_key = 'bb2d5fb0-04c1-11ea-8760-0e5f332f7b42';   // This is for a future capability. It can be ignored for now.

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
