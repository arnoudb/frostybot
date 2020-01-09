<?php

    // Secret Sauce Strategy

    class strategy_secretsauce extends strategy {

        private $daily;
        public $autotest = [
                                //'timeframe' => ['240','720','1440'],
                                'timeframe' => ['240'],
                                'emaLength' => ['min' => 5, 'max' => 25, 'step' => 1],
                                'tolerance' => ['min' => 0.5, 'max' => 3.0, 'step' => 0.1], 
                            ];
        public $required = ['timeframe','tolerance','emaLength','capital'];

        public function loadOHLCV($timeframe) {
            if (!isset($this->ohlcv[$timeframe])) {
                $db = new db('db/database.db');
                if ($result = $db->query("SELECT * FROM ohlcv WHERE pair='".strtoupper($this->params['pair'])."' AND timeframe='".$timeframe."' ORDER BY [time] ASC;")) {
                    $this->ohlcv[$timeframe] = $result;
                }
            }
            return $this->ohlcv[$timeframe];
        }

        private function tolerance($data, $tolerance) {
            $res = [];
            foreach($data as $item) {
                if (isset($item->ema)) {
                    $newitem = [
                        'upper' => $item->ema + ($item->ema * ($tolerance / 100)),
                        'lower' => $item->ema - ($item->ema * ($tolerance / 100)),  
                    ];
                    $res[date("Y-m-d H:i:s", $item->time)] = (object) $newitem;
                }
            }
            return $res;
        }


        public function trades($data) {
            //$this->daily = $this->tolerance(ema($this->bucketize($data, 1440), $this->params['emaLength']), $this->params['tolerance']);
            $this->daily = $this->tolerance(ema($this->loadOHLCV(1440), $this->params['emaLength']), $this->params['tolerance']);
            $res = [];
            $previtem = (object) ['close' => null];
            foreach($data as $item) {
                $date = date('Y-m-d H:i:s', floor($item->time / 1440) * 1440);
                if (isset($this->daily[$date])) {
                    $tolerance = $this->daily[$date];
                    if (isset($tolerance->upper) && isset($tolerance->lower)) {
                        $item->action = "HOLD";
                        if (($item->close > $tolerance->upper) && ($previtem->close < $tolerance->upper)) {
                            if (in_array($this->position,["SHORT","FLAT"])) {
                                $item->action = "BUY";
                                $this->buy($item->close);
                            }
                        }
                        if (($item->close < $tolerance->lower) && ($previtem->close > $tolerance->lower)) {
                            if (in_array($this->position,["LONG"])) {
                                $item->action = "SELL";
                                $this->sell($item->close);
                            }
                        }
                    }
                    $item->balance_base = $this->balance_base;
                    $item->balance_quote = $this->balance_base * $item->close;
                    $item->capital = $this->capital;
                    $item->trades_loss = $this->trades_loss;
                    $item->trades_win = $this->trades_win;
                    $item->total = $item->balance_quote + $item->capital;
                    $previtem = $item;
                    if ($item->action !== "HOLD") {
                        $res[] = $item;
                    }
                }
            }
            return $res;
        }

        
    }

?>