![logo](https://i.imgur.com/YAME6yT.png "#FrostyBot")

## Installation Guide

This document is aimed at being a comprehensive installation guide to get you upand running on FrostyBot.

### Prerequisites

If order to use Frostybot, you will need the following:
* A Linux server which is publicly accessible over the Internet (either by public IP address, DDNS or DNAT). You must be able to access the web server publicly for Frostybot to work. We recommend using the free Ubuntu server available on [Amazon Lightsail](https://lightsail.aws.amazon.com). They also offer a free public IP address. There is also [a handy guide here](https://www.airix.net/en/projects/the-virtual-private-server-in-the-cloud) which will show you how to set up your Lightsail VPS and all the prerequisite software.
* Apache2/Nginx (whichever you prefer). We highly recommend that you secure it with HTTPS (using [LetsEncrypt](https://www.digitalocean.com/community/tutorials/how-to-secure-apache-with-let-s-encrypt-on-ubuntu-18-04) or something similar). It's free, so there's really no excuse for having an insecure web server.
* PHP 7.2 or higher. You will need the following PHP modules installed (php-curl, php-mbstring, php-json, php-sqlite3, php-cli), and you will need to ensure that PHP is configured on your web server.
* If you want to clone Frostybot from Github, you will need to have git installed.

We must stress that the setup and configuration of Linux/Apache/Nginx/PHP is **not** within the scope of this guide. If you need help configuring those things, there is a vast knowlegebase of information and guides available on the Internet.

*Note:* This procedure assumes you are using Ubuntu Linux. If you are using a different distribution, the commands may differ.

You have a choice of two simple ways to download FrostyBot. You can eithe ruse git or you can download and unzip the zip file. Both procedures are provided.

### Install Using Git

* Firstly, ensure that you have the git program installed

      sudo apt install -y git

* Next, change to your home directory. This will ensure that you have write permission to download the source

    cd ~

* Next, run git clone to download the software

      git clone https://github.com/CryptoMF/frostybot.git
    
  You should see output similar to the following:
  
  ![screenshot1](https://i.imgur.com/5rm1bMX.png "Screenshot")
 
* Next, move the frostybot directory to your /var/www/html/ directory using the following command. You will need sudo privileges to complete this step.

      sudo mv frostybot /var/www/html/

### Install Using Zip File

If you would prefer not to use git, you can also manually download and unzip the package as shown below:

* Firstly, ensure that you have the unzip package installed

      sudo apt install -y unzip
       
* Next, change to your home directory. This will ensure that you have write permission to download the source

      cd ~

* Next, run wget to download the software

      wget https://github.com/CryptoMF/frostybot/archive/master.zip
  
  You can then proceed to unzip the master.zip package
  
      unzip master.zip
    
  You should see output similar to the following:
  
  ![screenshot2](https://i.imgur.com/ViAiBWY.png "Screenshot")
 
* Next, move the frostybot directory to your /var/www/html/ directory using the following command. You will need sudo privileges to complete this step.

      sudo mv frostybot-master /var/www/html/frostybot

### Post-Install Configuration

* Next we need to configure directory ownership and permissions. Run the following commands:

      cd /var/www/html
      sudo chgrp -R www-data frostybot
      sudo chmod -R 640 frostybot
      sudo chmod -R 660 frostybot/cache
      sudo chmod -R 660 frostybot/db
      sudo chmod -R 660 frostybot/log
      sudo chmod 750 frostybot/frostybot
      sudo chmod -R +X frostybot
      
      This should provide access to both your account and the web server.
      
* At this stage you should already be able to access the bot by browsing to http://yourdomain.com/frostybot/  - You should receive a response similar to this:
    
      {
        "results": {
            "code": 900,
            "message": "Request received from invalid address: 123.69.123.69",
            "type": "ERROR"
        },
        "messages": []
      }

 * What this response tells us is that the bot is responding as expected over http. At this point in time you can install an SSL certificate or configure a custom domain for your bot if you like. The actual procedures for doing this are not in this guide, but there are plenty tutorials available on the internet [like this one](https://www.digitalocean.com/community/tutorials/how-to-secure-apache-with-let-s-encrypt-on-ubuntu-18-04) to help you complete these tasks.
 
 * Next, you need to configure your exchange API keys on FrostyBot. Edit the bot configuration file using the following command:
 
       sudo nano /var/ww/html/frostybot/cfg/cfg.config.php
       
 * Once the editor is open, scroll down until you find the accounts section and modify it with your API keys as necessary. Here is a sample with all three supported exchanges configured with both mainnet and testnet configs (the apiKeys and secrets are for illustration purposes only, please replace them with your own):
 
```php
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
```

* Take note of your configured stubs (the shorthand key for each exchange, such as bitmexmain or deribittest) in your config file, as you will need to use these when sending orders to FrostyBot.

* Now, using the CLI, test if you can list the available markets on your exchange

      cd /var/www/html/frostybot
      ./frostybot deribitmain:markets
      
  If you have configured FrostyBot correctly, you should receive a list of all the available instruments that you can trade on the exchange, such as the example below:
  
  ![screenshot3](https://i.imgur.com/tdk6kaL.png "Screenshot")
  
  *Note:* All output from FrostyBot is in JSON format. This allows for easier integration and scripting, should you which to use FrostyBot in order ways.

### Tradingview Alerts and Webhook Configuration

  Once you've confirmed that Frostybot is responding over the internet, you can start creating Tradingview alerts:

* In Tradingview, create a new alert.
* The Webhook URL should point to your bot address.
* The message box of the Tradingview alert should have the bot command(s), here are some exmaples:
         
**Tradingview Alert Messagebox Examples:**

    deribit:long symbol=BTC-PERPETUAL size=1000         ($1000 market buy on Deribit BTC-PERPETUAL)
    ftx:short symbol=BTC-PERP size=2000 price=7600      ($2000 limit sell at $7600 on FTX BTC-PERP)
    bitmex:long symbol=BTC/USD size=3x                  (3x long market buy on Bitmex BTC/USD)
    ftx:close size=50% symbol=ETH-PERP                  (Market close 50% of oposition on FTX ETH-PERP)
    deribit:close symbol=ETH-PERPETUAL                  (Close entire ETH-PERPETUAL position on Deribit)
    ftx:short size=200% symbol=BTC-PERP                 (Market sell 200% of your account / 2x short)
      
*NOTE:* Anything in the message box of the Tradingview alert is interpreted as a command, so do not use the message box for a general description! Also note, you can provide multiple commands in the message box, as long as they are each on a new line. For example, you can close all your open stop loss orders before entering a new position.

Here is an example of a Tradingview alert showing multiple commands:

![TV Alert](https://i.imgur.com/p8YFTah.png)
     
### Command Line Interface (CLI) Usage

Other than Tradingview firing off webhooks, you can also communicate manually with Frostybot using the commandline interface (CLI). Here are some CLI examples that you can try out.
 
**CLI Examples:** 

    ./frostybot deribit:long symbol=BTC-PERPETUAL size=1000     ($1000 market buy on Deribit BTC-PERPETUAL)
    ./frostybot ftx:short symbol=BTC-PERP size=2000 price=7600  ($2000 limit sell at $7600 on FTX BTC-PERP)
    ./frostybot bitmex:long symbol=BTC/USD size=3x              (3x market buy on Bitmex BTC/USD)
    ./frostybot ftx:close size=50% symbol=ETH-PERP              (Market close 50% of position on FTX ETH-PERP)
    ./frostybot deribit:close symbol=ETH-PERPETUAL              (Close entire ETH-PERPETUAL position on Deribit)
    ./frostybot ftx:short size=200% symbol=BTC-PERP             (Market sell 200% of your account / 2x short)
    ./frostybot ftx:markets                                     (Show avaiable markets on FTX exchange)
    ./frostybot bitmex:market symbol=BTC/USD                    (Show market data for BTC/USD on Bitmex)
    ./frostybot ftx:trades                                      (Recent trades, oldest listed first)
    ./frostybot deribit:balance                                 (Current account balance on Deribit)
    ./frostybot bitmex:orders status=open                       (Open orders on Bitmex)
    ./frostybot ftx:cancel id=<orderid>                         (Cancel a specific order on FTX)
    ./frostybot deribit:cancel id=all                           (Cancel all open orders on Deribit)
    ./frostybot bitmex:cancelall                                (Cancel all open orders on Bitmex)
    ./frostybot ftx:cancel symbol=ADA-PERP                      (Cancel all ADA-PERP orders on FTX)
    ./frostybot bitmex:positions                                (Show current positions on Bitmex, if any)
    ./frostybot deribit:position symbol=BTC-PERPETUAL           (Show current BTC-PERPETUAL position on Deribit)
    ./frostybot ftx:ohlcv symbol=BTC-PERP timeframe=1h          (Show current hourly OHLCV data for FTX BTC-PERP)
    ./frostybot ftx:ohlcv symbol=BTC/USD timeframe=720          (Show current 12hr OHLCV data for Bitmex BTC/USD)
    ./frostybot config                                          (Show your current configuration)
    ./frostybot log                                             (Show log file for bot, last 20 lines by default)
    ./frostybot log lines=50                                    (Show the last 50 log file lines)
    ./frostybot log filter=error                                (Show all lines containing errors)
    ./frostybot log clear=true                                  (Clear the log file)
    ./frostybot flushcache                                      (Flush the cache file, for troubleshooting)

*NOTE:* The command syntax for Tradingview is exactly the same as for the CLI (except for the addition of ./frostybot in front of the command on the CLI). Any command that you can run on the CLI you can also run using webhooks. While this may not necessarily be useful for Tradingview, it can come in handy if you want to integrate something else with Frostybot. All Frostybot output is in JSON, which makes external integration, like scripting and charting quite simple.

