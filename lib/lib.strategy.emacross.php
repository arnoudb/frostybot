<?php

    // EMA Cross Strategy

    class strategy_emacross extends strategy {

        private $daily;
        public $autotest = [
                                'timeframe' => ['240','720','1440'],
                                'emaFast' => ['min' => 3, 'max' => 21, 'step' => 1],
                                'emaSlow' => ['min' => 30, 'max' => 80, 'step' => 1]
                            ];
        public $required = ['timeframe','capital','emaFast','emaSlow'];

        public function loadOHLCV($timeframe) {
            if (!isset($this->ohlcv[$timeframe])) {
                $db = new db('db/database.db');
                if ($result = $db->query("SELECT * FROM ohlcv WHERE pair='".strtoupper($this->params['pair'])."' AND timeframe='".$timeframe."' ORDER BY [time] ASC;")) {
                    $this->ohlcv[$timeframe] = $result;
                }
            }
            return $this->ohlcv[$timeframe];
        }

        public function trades($data) {
            $data = ema($data, $this->params['emaFast'], 'close', 'emaFast');
            $data = ema($data, $this->params['emaSlow'], 'close', 'emaSlow');
            $res = [];
            $previtem = (object) ['close' => null];
            foreach($data as $item) {
                $item->action = "HOLD";
                if (isset($item->emaFast) && isset($item->emaSlow) && isset($previtem->emaFast) && isset($previtem->emaSlow)) {
                    if (($item->emaFast > $item->emaSlow) && ($previtem->emaFast <= $previtem->emaSlow)) {
                        if (in_array($this->position,["SHORT","FLAT"])) {
                            $item->action = "BUY";
                            $this->buy($item->close);
                        }
                    }
                    if (($item->emaFast < $item->emaSlow) && ($previtem->emaFast >= $previtem->emaSlow)) {
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
            return $res;
        }

        
    }

?>