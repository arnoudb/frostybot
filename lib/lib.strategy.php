<?php

    abstract class strategy {

        protected $ohlcv;
        protected $params;
        protected $timeframe = 240;
        protected $pair = 'btcusd';
        protected $capital = 1000;
        protected $prevcapital = 1000;
        protected $balance_quote = 0;
        protected $balance_base = 0;
        protected $position = 'FLAT';
        protected $trades_total = 0;
        protected $trades_win = 0;
        protected $trades_loss = 0;
        public $required;

//        private $allowed_timeframes = ['1m', '5m', '15m', '30m', '1h', '4h', '12h', '1d'];

        // Put OHLCV data into buckets for a different timeframe 
        protected function bucketize($ohlcv, $timeframe) {
            $output = [];
            foreach ($ohlcv as $record) {
                $ts = (floor(strtotime($record->date) / $timeframe)) * $timeframe;
                $bucket = date('Y-m-d H:i:s', $ts);
                //$bucket = call_user_func($function, $record);
                if (!isset($output[$bucket])) {
                    $output[$bucket] = (object) [
                        'bucket' => $bucket,
                        'open' => $record->open,
                        'high' => $record->high,
                        'low'  => $record->low,
                        'close' => $record->close,
                        'volume' => $record->volume
                    ];
                } else {
                    $output[$bucket]->high = $record->high > $output[$bucket]->high ? $record->high : $output[$bucket]->high;
                    $output[$bucket]->low = $record->low < $output[$bucket]->low ? $record->low : $output[$bucket]->low;
                    $output[$bucket]->close = $record->close;
                    $output[$bucket]->volume += $record->volume;
                }
            }
            return array_values($output);
        }

        // Backtest buy simulation
        protected function buy($price) {
            $this->balance_base = round(($this->capital * 0.99) / $price, 5);
            $this->balance_quote = $this->balance_base * $price;
            $this->prevcapital = $this->capital;
            $this->capital -= $this->balance_quote;
            $this->total = $this->balance_quote + $this->capital;
            $this->position = "LONG";
        }

        // Backtest sell simulation
        protected function sell($price) {
            $this->balance_quote = $this->balance_base * $price;
            $this->balance_base = 0;
            $this->capital += $this->balance_quote;
            if ($this->capital > $this->prevcapital) {
                $this->trades_win++;
            }
            if ($this->capital < $this->prevcapital) {
                $this->trades_loss++;
            }
            $this->trades_total++;
            $this->total = $this->balance_quote + $this->capital;
            $this->position = "SHORT";
        }

        // Output a backtest result
        public function output($result) {
            $fields = [
                'start' => 'Start Date',
                'end' => 'End Date',
                'firsttrade' => 'First Trade',
                'lasttrade' => 'Last Trade',
                'avgperweek' => 'Avg Trades Per Week',
                'winratio' => 'Win Ratio',
                'trades_total' => 'Total Trades',
                'trades_win' => 'Winning Trades',
                'trades_loss' => 'Losing Trades',
                'pnlamt' => 'PNL ($)',
                'pnlpct' => 'PNL (%)',
                'score' => 'Score',
            ];
            if (is_array($result)) {        // Output is an array from autotest
                echo str_repeat("-", 70).PHP_EOL;
                foreach (array_slice($result,0,3) as $item) {
                    $this->output($item);
                    echo str_repeat("-", 70).PHP_EOL;
                }
            } else {                        // Output is the result of a single backtest
                $resultarr = (array) $result;
                echo PHP_EOL;
                foreach ($result->params as $key => $value) {
                    echo str_pad('Param: '.$key.':', 25, " ", STR_PAD_RIGHT).$value.PHP_EOL;
                }
                foreach ($fields as $key => $fieldname) {
                    if (isset($resultarr[$key])) {
                        echo str_pad($fieldname.':', 25, " ", STR_PAD_RIGHT).$resultarr[$key].PHP_EOL;
                    }
                }
                echo PHP_EOL;
            }
        }

        // Perform a backtest
        public function backtest($params) {
            $this->output($this->execBacktest($params));
            return true;
        }

        // Execute a backtest simulation
        private function execBacktest($params) {
            $params = requiredParams($params, $this->required);
            $this->params = $params;
            $start = isset($params['start']) ? $params['start'] : '2018-01-01 00:00';
            $end =  isset($params['end']) ? $params['end'] : date('Y-m-d H:i');
            $this->timeframe = $params['timeframe'];
            $this->start_capital = $params['capital'];
            $this->capital = $params['capital'];
            $this->balance_base = 0;
            $this->balance_quote = $this->capital;
            $this->total = $this->capital;
            $this->prevcapital = null;
            $this->trades_total = 0;
            $this->trades_win = 0;
            $this->trades_loss = 0;
            $this->loadOHLCV($this->timeframe);
            $ohlcv = [];
            $totalOhlcvRecords = count($this->ohlcv[$this->timeframe]);
            foreach($this->ohlcv[$this->timeframe] as $record) {
                if (($record->date >= $start) && ($record->date <= $end)) {
                    $ohlcv[] = $record;
                }
            }
            $this->trades = $this->trades($ohlcv);
            $output = new stdClass();
            $output->params = [];
            foreach($params as $key => $value) {
                if (!in_array($key,['stub','command','silent'])) {
                    $output->params[$key] = $value;
                }
            }
            $output->start = $start;
            $output->end = $end;
            $winratio = $this->trades_total > 0 ? round(($this->trades_win / $this->trades_total) * 100) : 0;
            $pnlpct = round((($this->capital - $this->start_capital) / $this->start_capital) * 100);
            if (count($this->trades) > 1) {
                $output->firsttrade = $this->trades[0]->date;
                $output->lasttrade = $this->trades[count($this->trades)-1]->date;
                $output->avgperweek = round(count($this->trades) / (($this->trades[count($this->trades)-1]->time - $this->trades[0]->time) / 604800),2);
                $output->winratio = $winratio.'%';
            }
            $output->trades_total = $this->trades_total;
            $output->trades_win = $this->trades_win;
            $output->trades_loss = $this->trades_loss;
            $output->pnlamt = '$'.number_format($this->capital - $this->start_capital, 2, '.', '');
            $output->pnlpct = $pnlpct.'%';
            $output->score = ($pnlpct * 2) + ($winratio > 50 ? $winratio * 2 : 0);
            return $output;
        }

        // Build a list of autotest parameters
        private function buildAutotestParams() {
            $autotest = $this->autotest;
            $optionslist = [];
            $totalsize = 1;
            foreach ($autotest as $name => $options) {
                $paramsize = 0;
                $optionslist[$name] = [];
                if (isset($options['min']) && isset($options['max']) && isset($options['step'])) {
                    for($option = $options['min']; $option <= $options['max']; $option+=$options['step']) {
                        $optionslist[$name][] = $option;
                        $paramsize++;
                    }
                    $params[$name] = $options['min'];
                } else {
                    $optionslist[$name] = $options;
                    $paramsize = count($options);
                }
                $totalsize *= $paramsize;
            }
            $ret = [];
            foreach(array_keys($optionslist) as $paramname) {
                $k = 0;
                for($i=0; $i < $totalsize / count($optionslist[$paramname]); $i++) {
                    foreach($optionslist[$paramname] as $optionvalue) {
                        $k++;
                        if (!isset($ret[$k])) { $ret[$k] = []; }
                        $ret[$k][$paramname] = $optionvalue;
                    }
                }
            };
            return $ret;
        }

        // Perform a automated array of backtests using ranges of predefined paramters to determine the top 3 best performing parameter sets
        public function autotest($params) {
            $i = 0;
            $oldcomplete = 0;
            $autotestparams = $this->buildAutotestParams();
            $total = count($autotestparams);
            $results = [];
            foreach($autotestparams as $testparams) {
                $params = array_merge($params, $testparams);
                $i++;
                $result = $this->execBacktest($params);
                $score = $result->score;
                $complete = round($i / $total, 2) * 100;
                if ($complete % 10 == 0) {
                    if ($complete > $oldcomplete) {
                        echo $complete.'% complete....'.PHP_EOL;
                        $oldcomplete = $complete;
                    }
                }
                if ($score > 0) {
                    $results[$score] = $result;
                }
            }
            echo "TOTAL TESTS: ".$i.PHP_EOL;
            krsort($results);
            $this->output(array_values($results));
            return true;
        }


    }

?>