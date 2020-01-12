<?php

    // Output normalizer for Bitmex exchange

    class normalizer_bitmex extends normalizer {

        public $orderSizing = 'quote';          // Base or quote

        // Get current balances
        public function fetch_balance($data) {
            $result = $data->result;
            $currency = 'BTC';
            $ticker = $this->ccxt->fetch_ticker('BTC/USD');
            $price = $ticker['ask'];
            $balanceFree = $result['BTC']['free'];
            $balanceUsed = $result['BTC']['used'];
            $balanceTotal = $result['BTC']['total'];
            $balances = [];
            $balances['BTC'] = new balanceObject($currency,$price,$balanceFree,$balanceUsed,$balanceTotal);
            return $balances;
        }

        // Get supported OHLCV timeframes
        public function fetch_timeframes() {
            return [
                '1'     => '1m',
                '5'     => '5m',
                '60'    => '1h',
                '1440'  => '1d',
            ];
        }

        // Get OHLCV data
        public function fetch_ohlcv($symbol, $timeframe, $count=100) {
            $markets = $this->ccxt->fetch_markets();
            foreach($markets as $market) {
                if ($market['symbol'] == $symbol) {
                    $id=$market['id'];
                    break;
                }
            }
            $tfs = $this->fetch_timeframes();
            $binSize = $tfs[$timeframe];
            $ohlcvurl = $this->ccxt->urls['api'].'/api/v1/trade/bucketed?binSize='.$binSize.'&partial=true&symbol='.$id.'&count='.$count.'&reverse=true';
            $ohlcv = [];
            if ($rawOHLCV = json_decode(file_get_contents($ohlcvurl))) {
                $rawOHLCV = array_reverse($rawOHLCV);
                foreach ($rawOHLCV as $rawEntry) {
                    $timestamp = strtotime($rawEntry->timestamp);
                    $open = $rawEntry->open;
                    $high = $rawEntry->high;
                    $low = $rawEntry->low;
                    $close = $rawEntry->close;
                    $volume = $rawEntry->volume;
                    $ohlcv[] = new ohlcvObject($symbol,$timeframe,$timestamp,$open,$high,$low,$close,$volume,$rawEntry);
                }
            }
            return $ohlcv;
        }

        // Get list of markets from exchange
        public function fetch_markets($data) {
            $result = $data->result;
            $markets = [];
            foreach($result as $market) {
                if (($market['type'] != 'option') && ($market['quote'] == 'USD') && ($market['active'] == true) && ($market['info']['typ'] == 'FFWCSX')) {
                    $id = $market['id'];
                    $symbol = $market['symbol'];
                    $quote = $market['quote'];
                    $base = $market['base'];
                    $expiration = (isset($market['info']['expiration']) ? $market['info']['expiration'] : null);
                    $bid = (isset($market['info']['bid']) ? $market['info']['bid'] : null);
                    $ask = (isset($market['info']['ask']) ? $market['info']['ask'] : null);
                    $contractSize = (isset($market['info']['contractSize']) ? $market['info']['contractSize'] : 1);
                    $marketRaw = $market;
                    $markets[] = new marketObject($id,$symbol,$base,$quote,$expiration,$bid,$ask,$contractSize,$marketRaw);
                }
            }
            return $markets;
        }

        // Get list of positions from exchange
        public function fetch_positions($markets) {
            $result = [];
            $positions = $this->ccxt->private_get_position();
            foreach ($positions as $positionRaw) {
                foreach ($markets as $market) {
                    if ($positionRaw['symbol'] == $market->id) {
                        $direction = $positionRaw['homeNotional']  == 0 ? 'flat' : ($positionRaw['homeNotional'] > 0 ? 'long' : ($positionRaw['homeNotional'] < 0 ? 'short' : 'null'));
                        $baseSize = $positionRaw['homeNotional'];
                        $quoteSize = $positionRaw['currentQty'];
                        $entryPrice = $positionRaw['avgEntryPrice'];
                        if (abs($baseSize) > 0) {
                            $result[] = new positionObject($market,$direction,$baseSize,$quoteSize,$entryPrice,$positionRaw);
                        }
                    }
                }
            }
            return $result;
        }

        // Get list of orders from exchange
        public function fetch_orders($markets) {
            $orders = $this->ccxt->private_get_order();
            $result = [];
            foreach ($orders as $order) {
                foreach ($markets as $market) {
                    if ($order['symbol'] === $market->id) {
                        $id = $order['orderID'];
                        $timestamp = strtotime($order['timestamp']);
                        $type = strtolower($order['ordType']);
                        $direction = (strtolower($order['side']) == 'buy' ? 'long' : 'short');
                        $price = (isset($order['price']) ? $order['price'] : 1);
                        $trigger = (isset($order['stopPx']) ? $order['stopPx'] : null);
                        $sizeBase = $order['orderQty'] / $price;
                        $sizeQuote = $order['orderQty'];
                        $filledBase = $order['cumQty'] / $price;
                        $filledQuote = is_null($order['cumQty']) ? 0 : $order['cumQty'];
                        $status = ((strtolower($order['ordStatus']) == 'new') ? 'open' : strtolower($order['ordStatus']));
                        $orderRaw = $order;
                        $result[] = new orderObject($market,$id,$timestamp,$type,$direction,$price,$trigger,$sizeBase,$sizeQuote,$filledBase,$filledQuote,$status,$orderRaw);
                    }
                }
            }
            return $result;
        }

       // Cancel order
       public function cancel_order($id) {
            $orders = $this->ccxt->private_get_order(['filter'=>'{"orderID":"'.$id.'"}']);
            $status = 'unknown';
            if (count($orders) > 0) {
                $order = $orders[0];
                if ($order['orderID'] == $id) {
                    $status = ((strtolower($order['ordStatus']) == 'new') ? 'open' : strtolower($order['ordStatus']));
                }
            }
            if ($status == 'open') {
                $result = $this->ccxt->private_delete_order(['orderID'=>$id]);
                if (isset($result[0]['ordStatus'])) {
                    if (strtolower($result[0]['ordStatus']) == 'cancelled') {
                        return true;
                    }
                }
            } else {
                logger::error('Cannot cancel order '.$id.': Invalid status');
            }
            return false;
        }

        // Cancel all orders
        public function cancel_all_orders($symbol = null) { 
            if (!is_null($symbol)) {
                $markets = $this->ccxt->fetch_markets();
                foreach($markets as $market) {
                    if ($market['symbol'] == $symbol) {
                        $symbol=$market['id'];
                        break;
                    }
                }
            }
            if (!is_null($symbol)) {
                $result = $this->ccxt->private_delete_order_all(['symbol'=>$symbol]);
            } else {
                $result = $this->ccxt->private_delete_order_all();
            }
            return true;
        }        

    }


?>