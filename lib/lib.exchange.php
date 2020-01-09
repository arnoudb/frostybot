<?php

    // Base exchange class wrapper (combines the CCXT lib and the normalizer)

    class exchange {

        private $ccxt;                          // CCXT class
        private $normalizer;                    // Normalizer class
        private $settings = [                   // Default settings
                    //'mode' => 'test',
                ]; 
        private $exchange;
        private $params;
        private $strategy;

        // Construct backend CCXT exchange object, and instanciate the appropriate output normalizer if it exists
        public function __construct($exchange, $options) {
            $this->settings = array_merge($this->settings, $options);
            $this->exchange = strtolower($exchange);
            $class = "\\ccxt\\" . strtolower($exchange);
            $normalizer = 'normalizer_'.strtolower($exchange);
            if (class_exists($class)) {
                $this->ccxt = new $class($options);
            }
            if (class_exists($normalizer)) {
                $this->normalizer = new $normalizer($this->ccxt, $options);
            }
        }

        // Execute a particular CCXT method, 
        // If a normalizer method by the same name exists, execute that too, passing all the CCXT data to the normalizer
        public function __call($name, $params) {
            $result = false;
            if (method_exists($this->ccxt, $name)) {
                $result = call_user_func([$this->ccxt, $name], $params);
            }
            if (method_exists($this->normalizer, $name)) {
                $result = call_user_func([$this->normalizer, $name], (object) ['params' => $params, 'result'=> $result, 'settings' => $this->settings]);
            }
            return $result;
        }
        
        // Get CCXT info
        public function ccxtinfo($params) {
            $function = isset($params['function']) ? $params['function'] : null;
            if (!is_null($function)) {
                if (isset($this->ccxt->defined_rest_api[$function])) {
                    return $this->ccxt->defined_rest_api[$function];
                } else {
                    logger::error('Function "'.$function.'" not implemented in the exchange API');
                    return false;
                }
            } else {
                $info = [];
                $keys = ['id','has','api','status','timeframes','urls'];
                foreach($this->ccxt as $key => $val) {
                    if (in_array($key,$keys)) {
                        $info[$key] = $val;
                    }
                }
                return $info;
            }
        }

        // Get OHLCV data
        public function ohlcv($params) {
            $symbol = $params['symbol'];
            $timeframe = timeframeToMinutes(isset($params['timeframe']) ? $params['timeframe'] : '1h');
            $cacheTime = $timeframe / 2;
            $count = isset($params['count']) ? $params['count'] : 100;
            $ohlcvTimeframes = [];
            $tfs = $this->normalizer->fetch_timeframes();
            foreach ($tfs as $tfkey => $tf) {
                $tfmin = timeframeToMinutes($tf);
                if (!is_null($tfmin)) {
                    $ohlcvTimeframes[timeframeToMinutes($tf)] = $tfkey;
                }
            }
            if (!array_key_exists($timeframe, $ohlcvTimeframes)) {
                $maxtf = 1;
                foreach (array_keys($ohlcvTimeframes) as $tf) {
                    if (($tf < $timeframe) && ($tf > $maxtf) && ($timeframe % $tf == 0)) {
                        $maxtf = $tf;
                    }
                }
                $gettf = (isset($ohlcvTimeframes[$maxtf]) ? $ohlcvTimeframes[$maxtf] : $timeframe);
                $bucketize = true;
                $multiplier = floor($timeframe / $maxtf);
            } else {
                $gettf = $ohlcvTimeframes[$timeframe];
                $bucketize = false;
                $multiplier = 1;
            }
            $qty = $count * $multiplier;
            if ($qty > 1000) { $qty = 1000; }
            $period = ((floor((time() / 60) / $timeframe) * $timeframe) * 60) + ($timeframe * 60);
            $cacheTime = (count($ohlcvTimeframes) == 0 ? 60 : $period - time());  // Reduce cache time if we are using trade data to generatw OHLCV (no OHLCV support on exchange API)
            $key = $this->exchange.':ohlcv:'.$symbol.':'.$timeframe.':'.$period.':'.$qty;
            if ($cacheResult = getCache($key,$cacheTime)) {
                $ohlcv = $cacheResult;
            } else {
                $ohlcv = $this->normalizer->fetch_ohlcv($symbol,$gettf,$qty);
                setCache($key,$ohlcv);
            }
            if ($bucketize !== false) {
                $ohlcv = bucketize($ohlcv, $timeframe);
            }
            $ohlcv = array_slice($ohlcv,(0-$count));
            $result = [
                'ohlcv'     => $ohlcv,
                'count'     => count($ohlcv),
                'symbol'    => $symbol,
                'timeframe' => $timeframe,
            ];
            return $result;
        }

        // Get market data for specific symbol
        public function market($params) {
            $symbol = (is_array($params) ? $params['symbol'] : $params);
            $markets = $this->markets(false,120);
            foreach($markets as $market) {
                if ($market->symbol === $symbol) {
                    if (is_null($market->bid) || is_null($market->ask)){
                        $ticker = $this->ccxt->fetch_ticker($symbol);
                        $market->bid = $ticker['bid'];
                        $market->ask = $ticker['ask'];
                    }
                    return $market;
                }
            }
            logger::error('Invalid market symbol');
            return false;
        }

        // Get market data for all markets
        public function markets($tickers = true, $cachetime = 0) {
            $key = $this->exchange.':markets';
            if ($cacheResult = getCache($key,$cachetime)) {
                return $cacheResult;
            } else {
                $markets = $this->fetch_markets();
                $ret = [];
                foreach($markets as $market) {
                    $symbol = $market->symbol;
                    if (($tickers !== false) && (is_null($market->bid) || is_null($market->ask))){
                        $ticker = $this->ccxt->fetch_ticker($symbol);
                        $market->bid = $ticker['bid'];
                        $market->ask = $ticker['ask'];
                    }
                    $ret[] = $market;
                }
                setCache($key,$ret);
                return $ret;
            }
        }

        // Get position for specific symbol
        public function position($params) {
            $symbol = (is_array($params) ? $params['symbol'] : $params);
            $suppress = (isset($params['suppress']) ? $params['suppress'] : false);
            $positions = $this->positions();
            foreach($positions as $position) {
                if ($symbol == $position->market->symbol) {
                    return $position;
                }
            }           
            if ($suppress !== true) { 
                logger::notice('You do not currently have a position on '.$symbol);
            }
            return false;
        }

        // Get current positions
        public function positions() {
            $markets = $this->markets(true,0);
            return $this->normalizer->fetch_positions($markets);
        }

        // Get order data for a specific order ID
        public function order($params) {
            $id = is_array($params) ? $params['id'] : $params;
            $orders = $this->orders();
            foreach ($orders as $order) {
                if ($order->id == $id) {
                    return $order;
                }
            }
            logger::error('Invalid order or order not found');
            return false;
        }

        // Get all orders
        public function orders($settings = []) {
            $markets = $this->markets(true,300);
            $filters = [];
            if (isset($settings['id'])) { $filters['id'] = $settings['id']; }
            if (isset($settings['symbol'])) { $filters['symbol'] = $settings['symbol']; }
            if (isset($settings['type'])) { $filters['type'] = $settings['type']; }
            if (isset($settings['direction'])) { $filters['direction'] = $settings['direction']; }
            if (isset($settings['status'])) { 
                $filters['status'] = $settings['status']; 
                $onlyOpen = ($settings['status'] == "open" ? true : false); 
            } else {
                $onlyOpen = false;
            }
            $result = [];
            $orders = $this->normalizer->fetch_orders($markets,$onlyOpen);
            foreach ($orders as $order) {
                if (count($filters) > 0) {
                    $filter = false;
                    foreach($filters as $key => $value) {
                        if ($key == 'symbol') {
                            if ($order->market->symbol !== $value) {
                                $filter = true;
                            }    
                        } else {
                            if ($order->$key !== $value) {
                                $filter = true;
                            }
                        }
                    }
                    if (!$filter) {
                        $result[] = $order;                                
                    }
                } else {
                    $result[] = $order;
                }
            }
            return $result;
        }

        // Cancel order(s)
        public function cancel($params) {
            $id =  (isset($params['id']) ? $params['id'] : null);
            $symbol = (isset($params['symbol']) ? $params['symbol'] : null);
            if ($id == 'all') {
                if (method_exists($this->normalizer,'cancel_all_orders')) {
                    return $this->normalizer->cancel_all_orders($symbol);
                } else {
                    $orders = ((!is_null($symbol)) ? $this->orders(['status'=>'open','symbol'=>$symbol]) : $this->orders(['status'=>'open']));
                    foreach($orders as $order) {
                        $result = $this->cancel(['id'=>$order->id]);
                        if (!in_array($result->status,['canceled','cancelled'])) {       // Deribit don't know how to spell cancelled
                            logger::error('Not all open orders were able to be cancelled');
                            return false;
                        }

                    }
                    return true;
                }
            } else {
                if (method_exists($this->normalizer,'cancel_order')) {
                    $result = $this->normalizer->cancel_order($id);
                    return (object) $result;
                } else {
                    $order = $this->order($id);
                    if (!is_null($order)) {
                        if ($order->status == "open") {
                            $result = $this->ccxt->cancel_order($id);
                            return (object) $result;
                        } 
                    }
                }
            }
            logger::error('Failed to cancel order: '.$id);
            return false;
        }

        // Get free balance in USD value using current market data
        public function available_balance_usd() {
            $balances = $this->fetch_balance();
            $usd_free = 0;
            foreach ($balances as $balance) {
                $usd_free += $balance->balance_usd_free;
            }
            return $usd_free;
        }

        // Get total balance in USD value using current market data
        public function total_balance_usd() {
            $balances = $this->fetch_balance();
            $usd_free = 0;
            foreach ($balances as $balance) {
                $usd_free += $balance->balance_usd_total;
            }
            return $usd_free;
        }

        // Perform Trade
        private function trade($dir, $params) {
            $symbol = $params['symbol'];
            $size = $params['size'];
            $orderSizing = (isset($this->normalizer->orderSizing) ? $this->normalizer->orderSizing : 'quote');
            if (strtolower(substr($size,-1)) == 'x') {             // Position size given in x
                $size = $this->total_balance_usd() * str_replace('x','',strtolower($size));
            }
            if (strtolower(substr($size,-1)) == '%') {             // Position size given in %
                $size = $this->total_balance_usd() * (str_replace('%','',strtolower($size) / 100));
            }
            $usdSize = $size;
            $market = $this->market(['symbol' => $symbol]);
            $price = $dir == 'buy' ? $market->ask : $market->bid;
            $contract_size = (($orderSizing == 'quote') ? round($size / $market->contract_size,0) : ($size / $price));
            $price = isset($params['price']) ? $params['price'] : null;
            $type = is_null($price) ? 'market' : 'limit';
            $position = $this->position(['symbol' => $symbol, 'suppress' => true]);
            if (is_object($position)) {           // If already in a position
                // Flip position if required
                if ((($dir == 'buy') && ($position->direction == 'short')) || (($dir == 'sell') && ($position->direction == 'long'))) {         
                    $contract_size += ($orderSizing == 'quote' ? round($position->size_quote / $market->contract_size,0) : $position->size_base);

                }
                // Extend position if required
                if ((($dir == 'buy') && ($position->direction == 'long')) || (($dir == 'sell') && ($position->direction == 'short'))) {  
                    if ($contract_size >= ($orderSizing == 'quote' ? round($position->size_quote / $market->contract_size,0) : $position->size_base)) {
                        $contract_size -= ($orderSizing == 'quote' ? round($position->size_quote / $market->contract_size,0) : $position->size_base);
                    } else {
                        logger::error('Already '.($dir == 'buy'? 'long' : 'short').' more contracts than requested');
                        $contract_size = 0;
                    }
                }
            }
            if ($contract_size > 0) {
                $balance = $this->total_balance_usd();
                logger::info('TRADE:'.($dir == 'buy' ? 'LONG ' : 'SHORT').' | Symbol: '.$symbol.' | Direction: '.$dir.' | Type: '.$type.' | Size: '.($contract_size * $market->contract_size).' | Price: '.($price == "" ? 'Market' : $price).' | Balance: '.$balance);
                return $this->ccxt->create_order($symbol, $type, $dir, abs($contract_size), $price);
            }
            return false;
        }

        // Long Trade
        public function long($params) {
            return $this->trade('buy', $params);
        }

        // Short Trade
        public function short($params) {
            return $this->trade('sell', $params);
        }

        // Close Position
        public function close($params) {
            $symbol = $params['symbol'];
            $size = str_replace('%', '', isset($params['size']) ? $params['size'] : '100%');
            $orderSizing = (isset($this->normalizer->orderSizing) ? $this->normalizer->orderSizing : 'quote');
            $position = $this->position(['symbol' => $symbol, 'suppress' => true]);
            if (is_object($position)) {
                $dir = $position->direction == 'long' ? 'sell' : ($position->direction == 'short' ? 'buy' : null);
                $market = $position->market;
                $contract_size = ($orderSizing == 'quote' ? (round($position->size_quote * ($size / 100),0) / $market->contract_size) : ($position->size_base * ($size / 100)));
                $type = 'market';
                //$price = ($dir == "buy" ? $market->ask : $market->bid);
                $price = null;
                if ($contract_size > 0) {
                    $balance = $this->total_balance_usd();
                    logger::info('TRADE:CLOSE | Symbol: '.$symbol.' | Direction: '.$dir.' | Type: '.$type.' | Size: '.($contract_size * $market->contract_size).' | Price: '.(is_null($price) ? 'Market' : $price).' | Balance: '.$balance);
                    return $this->ccxt->create_order($symbol, $type, $dir, abs($contract_size), $price);
                }
            } else {
                logger::error("You do not currently have a position on ".$symbol);
            }
        }

        // Backtest strategy on exchange
        public function backtest($params) {
            $strategy = $params['strategy'];
            $class = 'strategy_'.$strategy;
            $this->strategy = new $class();
            return $this->strategy->backtest($params);
        }

        // Autotest strategy on exchange using predefined sets of settings to see which is best
        public function autotest($params) {
            $strategy = $params['strategy'];
            $class = 'strategy_'.$strategy;
            $this->strategy = new $class();
            return $this->strategy->autotest($params);
        }

        // Settings Getter (did not use __get because I want more control over what can be get)
        public function get($param) {
            if (isset($this->settings[$param])) {
                return $this->settings[$param];
            } 
            return null;
        }

        // Settings Setter (did not use __set because I want more control over what can be set)
        public function set($param, $value) {
            $this->settings[$param] = $value;
            return $this->settings[$param];
        }

    }


?>