<?php

    // Command Handler

    class command {

        private $params = [];
        private $exchange;
        private $commandStr;

        // Override constructor (for internal used only)
        public function __construct($commandStr = null) {
            $this->commandStr = $commandStr;
        }

        // Execute multiple commands if provided
        private function _execMultiple($commandstr) {
            $commandstr = str_replace("\n",'|',$commandstr);
            if (strpos($commandstr,'|')) {
                $commands = explode('|',$commandstr);
                $overallResult = true;
                $allResults = [];
                foreach ($commands as $command) {
                    $commandObj = new command($command);
                    $result = $commandObj->execute();
                    if ($result !== false) {
                        $allResults[] = testResult(0,'SUCCESS',$result);
                    } else {
                        $overallResult = false;
                        $allResults[] = testResult(999,'ERROR',false);
                    }
                }
                if ($overallResult === true) {
                    outputResult(0,'SUCCESS',$allResults);
                } else {
                    outputResult(999,'ERROR',$allResults);
                }
                die;
            }
        }

        // Find out if we're using CLI or URL, and pass it to the correct parsing method
        private function _parseParams() {
            if (!is_null($this->commandStr)) {
                $this->_parseInternal();
                logger::debug("Internal command issued: ".str_replace('__frostybot__:','',$this->params['stub'].':'.$this->params['command']));
            } else {
                $execname = isset($_SERVER['argv'][0]) ? basename($_SERVER['argv'][0]) : '';
                if ($execname == 'frostybot') {
                    $this->_parseCLI();
                    logger::debug("CLI command issued: ".str_replace('__frostybot__:','',$this->params['stub'].':'.$this->params['command']));
                } else {
                    $this->_parseURL();
                    logger::debug("URL command issued: ".str_replace('__frostybot__:','',$this->params['stub'].':'.$this->params['command']));
                }
            }
        }

        // Parse URL GET/POST paramters (from TradingView)
        private function _parseURL() {
            // Check that request is coming from an authorised Trading View IP
            if (isset($_SERVER['REMOTE_ADDR']) && (!in_array($_SERVER['REMOTE_ADDR'], whitelist))) {
                logger::error('Request received from invalid address: ' . $_SERVER['REMOTE_ADDR']);
            }
            $rawPostText = file_get_contents('php://input');
            $postArgs = [];
            if (isset($_GET['command'])) {
                $exchangeCommand = $_GET['command'];
                if(strpos($exchangeCommand,':') === false) {
                    $stub = "__frostybot__";
                    $command = $exchangeCommand;
                } else {
                    list($stub,$command) = explode(":", $exchangeCommand);
                }
                $this->params['stub'] = strtolower($stub);
                $this->params['command'] = strtolower($command);
                $params = $_GET;
                unset($params['command']);
                foreach($params as $key => $value) {
                    $this->params[strtolower($key)] = $value;
                }
            }
            if ($rawPostText !== "") {
                $this->_execMultiple($rawPostText);
                $postArgs = explode(" ", $rawPostText);
                if (!isset($exchangeCommand)) {
                    $exchangeCommand = $postArgs[0];
                    if(strpos($exchangeCommand,':') === false) {
                        $stub = "__frostybot__";
                        $command = $exchangeCommand;
                    } else {
                        list($stub,$command) = explode(":", $exchangeCommand);
                    }
                    $this->params['stub'] = strtolower($stub);
                    $this->params['command'] = strtolower($command);
                    array_shift($postArgs);
                } 
                $params = $postArgs;
                foreach($params as $param) {
                    list($key, $value) = explode("=", $param);
                    $this->params[strtolower($key)] = $value;
                }
            } 
        }

        // Parse CLI parameters
        private function _parseCLI() {
            $args = $_SERVER['argv'];
            if (isset($args[1])) {
                $exchangeCommand = $args[1];
                $this->_execMultiple($exchangeCommand);
                if(strpos($exchangeCommand,':') === false) {
                    $stub = "__frostybot__";
                    $command = $exchangeCommand;
                } else {
                    list($stub,$command) = explode(":", strtolower($args[1]));
                }
                $this->params['stub'] = $stub;
                $this->params['command'] = $command;
            } else {
                die(PHP_EOL.'USAGE:   '.$args[0].' <stub>:<command> param=val param=val'.PHP_EOL.PHP_EOL.'EXAMPLE: '.$args[0].' deribit:POSITION symbol=BTC-PERPETUAL'.PHP_EOL.PHP_EOL);
            }
            $params = isset($args[2]) ? array_slice($args,2) : [];
            foreach($params as $param) {
                list($key, $value) = explode("=", $param);
                $this->params[$key] = $value;
            }
        }

        // Parse Internal parameters
        private function _parseInternal() {
            $args = explode(" ",$this->commandStr);
            if (isset($args[0])) {
                $exchangeCommand = $args[0];
                if(strpos($exchangeCommand,':') === false) {
                    $stub = "__frostybot__";
                    $command = $exchangeCommand;
                } else {
                    list($stub,$command) = explode(":", strtolower($args[0]));
                }
                $this->params['stub'] = $stub;
                $this->params['command'] = $command;
            } else {
                die(PHP_EOL.'USAGE:   '.$args[0].' <stub>:<command> param=val param=val'.PHP_EOL.PHP_EOL.'EXAMPLE: '.$args[0].' deribit:POSITION symbol=BTC-PERPETUAL'.PHP_EOL.PHP_EOL);
            }
            $params = isset($args[1]) ? array_slice($args,1) : [];
            foreach($params as $param) {
                list($key, $value) = explode("=", $param);
                $this->params[$key] = $value;
            }
        }

        // Execute the command, and ensure that the necessary parameters have been given
        public function execute($output = false) {
            $this->_parseParams();
            if (requiredParams($this->params,['stub','command']) !== false) {
                $stub = $this->params['stub'];
                $command = $this->params['command'];
                $accounts = array_change_key_case(accounts, CASE_LOWER);
                if (($stub == '__frostybot__') || (array_key_exists($stub, $accounts))) {
                    if ($stub !== '__frostybot__') {
                        $config = $accounts[$stub];
                        $symbolmap = (isset($config['symbolmap']) ? $config['symbolmap'] : []);
                        $defaultsymbol = (isset($symbolmap['default']) ? $symbolmap['default'] : null);
                        if (isset($this->params['symbol'])) {
                            $symbol = $this->params['symbol'];
                            if (array_key_exists($symbol, $symbolmap)) {
                                $this->params['symbol'] = $symbolmap[$symbol];
                            }
                        } else {
                            $this->params['symbol'] = $defaultsymbol;
                        }
                        $this->exchange = new exchange($config['exchange'],$config['parameters']);
                    }
                    switch (strtoupper($command)) {
                        case 'CONFIG'       :   $result = censorConfig($accounts);
                                                break;
                        case 'LOG'          :   $result = logger::get($this->params);
                                                break;
                        case 'FLUSHCACHE'   :   $result = flushCache(0, (isset($this->params['permanent']) ? $this->params['permanent'] : false));
                                                break;
                        case 'UNITTESTS'    :   $result = unitTests::runTests(requiredParams($this->params,['group']));
                                                break;
                        case 'CCXTINFO'     :   $result = $this->exchange->ccxtinfo($this->params);
                                                break;
                        case 'BALANCE'      :   $result = $this->exchange->fetch_balance();
                                                break;
                        case 'BALANCEUSD'   :   $result = $this->exchange->total_balance_usd();
                                                break;
                        case 'MARKET'       :   $result = $this->exchange->market(requiredParams($this->params,['symbol']));
                                                break;
                        case 'MARKETS'      :   $result = $this->exchange->markets();
                                                break;
                        case 'OHLCV'        :   $result = $this->exchange->ohlCv(requiredParams($this->params,['symbol']));
                                                break;
                        case 'POSITION'     :   $result = $this->exchange->position(requiredParams($this->params,['symbol']));
                                                break;
                        case 'POSITIONS'    :   $result = $this->exchange->positions();
                                                break;
                        case 'ORDER'        :   $result = $this->exchange->order(requiredParams($this->params,['id']));
                                                break;
                        case 'ORDERS'       :   $result = $this->exchange->orders(requiredParams($this->params,[]));
                                                break;
                        case 'CANCEL'       :   $result = $this->exchange->cancel(requiredParams($this->params,['id']));
                                                break;
                        case 'CANCELALL'    :   $result = $this->exchange->cancel(array_merge($this->params,['id'=>'all']));
                                                break;
                        case 'LONG'         :   $result = $this->exchange->long(requiredParams($this->params,['symbol','size']));
                                                break;
                        case 'SHORT'        :   $result = $this->exchange->short(requiredParams($this->params,['symbol','size']));
                                                break;
                        case 'CLOSE'        :   $result = $this->exchange->close(requiredParams($this->params,['symbol']));
                                                break;
                        case 'BACKTEST'     :   $result = $this->exchange->backtest(requiredParams($this->params,['strategy','pair','timeframe']));
                                                break;
                        case 'AUTOTEST'     :   $result = $this->exchange->autotest(requiredParams($this->params,['strategy','pair']));
                                                break;
                        default             :   logger::error('Unknown command: '.$command);
                                                $result = false;
                                                break;
                    }
                    //print_r($result);
                    //die;

                } else {
                    $result = false;
                    logger::error('Account not configured: '.$stub);
                }
                if ($output === true) {
                    global $__outputs__;
                    if ($result !== false) {
                        outputResult(0,"SUCCESS",$result);
                    } else {
                        outputResult(999,"ERROR",$false);
                    }
                    //$__outputs__->outputResults();
                } else {
                    return $result;
                }
            }
        }

    }


    $command = new command();
    $command->execute(true);

?>