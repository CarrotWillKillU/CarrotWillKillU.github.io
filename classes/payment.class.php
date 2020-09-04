<?php
if (!defined("TKM")) {
    die("<pre>Access denied!</pre>");
}

class Payment
{
    private $payment_isset, $data;
    public $payment_id;

    public function select($paymentID, $summ = "-")
    {
        global $link, $config;
        $sth = $link->prepare("SELECT * FROM `AD_PAYMENTS` WHERE `id` = ?");
        $sth->execute(array($paymentID));
        $row = $sth->fetch();
        if (empty($row['username'])) {
            return "Payment not found!";
        }

        $data = json_decode($row['data'], true);

        if ($summ != '-' && (int)$summ != (int)$data['cost']) {
            return "Not valid cost! Gived: {$summ}, Valid: {$data['cost']}";
        }

        if ($row['server'] <= (count($config['servers']) - 1) &&
            $row['server'] >= 0
        ) {
            $server = $row['server'];
        } else {
            return "Server not found for issuance!";
        }

        $data = json_decode($row['data'], true);
        $sth = $link->prepare("SELECT * FROM `AD_GOODS` WHERE `id` = ?");
        $sth->execute(array($data['goods_id']));
        $row2 = $sth->fetch();

        if (empty($row2['id'])) {
            return "Package not founded! (ID: {$data['goods_id']})";
        }

        $this->payment_isset = true;
        $this->data = $row;
        $this->data2 = $row2;
        $this->payment_id = $paymentID;

        return true;
    }

    public function give($admin = false)
    {
        global $link, $config;

        if (!$this->payment_isset) {
            return "Payment not founded!";
        }

        $data = json_decode($this->data['data'], true);

        $commands = json_decode($this->data2['commands'], true);

        if ($config['main']['withdraw'] === 'rcon') {
            $rcon = new RCON($this->data['server']);

            if (!@$rcon->connect()) {
                $link->prepare("UPDATE `AD_PAYMENTS` SET `status` = 2, `log` = 'No connection to the server!' WHERE `id` = '{$this->payment_id}'")->execute();

                if (!empty($config['discord']['webhook'])) {
                    $mCount = 1;

                    foreach ($config['discord']['webhook'] as $id) {
                        if (($mCount % 3) == 0) {
                            sleep(1);
                        }

                        $webhookurl = $config['discord']['webhook'];

                        //=======================================================================================================
                        // Compose message. You can use Markdown
                        // Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
                        //========================================================================================================

                        $msg = ':red_circle: Connection Lost (RCON). Order: #' . $this->payment_id;

                        $json_data = array('content' => "$msg");
                        $make_json = json_encode($json_data);

                        $ch = curl_init($webhookurl);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $make_json);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                        $response = curl_exec($ch);
                        $mCount++;
                    }
                }
                return "No connection to the server!";
            }

            for ($i = 0; $i < count($commands); $i++) {
                $command = str_replace("{user}", $this->data['username'], $commands[$i]);
                $rcon->send_command($command);
                $log[] = $rcon->get_response();
            }

            $rcon->disconnect();
        } else if ($config['main']['withdraw'] === 'plugin') {
            for ($i = 0; $i < count($commands); $i++) {
                if (($fp = fsockopen($config['servers'][$this->data['server']]['host_websocket'], $config['servers'][$this->data['server']]['port_websocket'], $errno, $errstr, 3)) === FALSE) {
                    $link->prepare("UPDATE `AD_PAYMENTS` SET `status` = 2, `log` = 'No connection to the server!' WHERE `id` = '{$this->payment_id}'")->execute();

                    if (!empty($config['discord']['webhook'])) {
                        $mCount = 1;

                        foreach ($config['discord']['webhook'] as $id) {
                            if (($mCount % 3) == 0) {
                                sleep(1);
                            }

                            $webhookurl = $config['discord']['webhook'];

                            //=======================================================================================================
                            // Compose message. You can use Markdown
                            // Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
                            //========================================================================================================

                            $msg = ':red_circle: Connection Lost (PLUGIN). Order: #' . $this->payment_id;

                            $json_data = array('content' => "$msg");
                            $make_json = json_encode($json_data);

                            $ch = curl_init($webhookurl);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $make_json);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                            $response = curl_exec($ch);
                            $mCount++;
                        }
                    }
                    return "No connection to the server!";
                }

                $command = str_replace("{user}", $this->data['username'], $commands[$i]);
                $cmd = $config['servers'][$this->data['server']]['password_websocket'] . " " . $command;
                fwrite($fp, $cmd);
                $log[] = "log";

                fclose($fp);
            }
        }

        $log = json_encode($log, JSON_UNESCAPED_UNICODE);

        $link->prepare("UPDATE `AD_PAYMENTS` SET `status` = 1, `log` = '{$log}' WHERE `id` = '{$this->payment_id}'")->execute();
        $link->prepare("INSERT INTO `AD_BUYERS` VALUES (NULL, :username, :good, :ctime, :exptime, :cost, :server)")->execute(array('username' => $this->data['username'], 'good' => $this->data2['id'], 'ctime' => time(), 'exptime' => 0, 'cost' => $data['cost'], 'server' => $this->data['server']));

        if (!empty($config['discord']['webhook'])) {
            $mCount = 1;

            //foreach ($config['discord']['webhook'] as $id) {
            if (($mCount % 3) == 0) {
                sleep(1);
            }

            $webhookurl = $config['discord']['webhook'];

            //=======================================================================================================
            // Compose message. You can use Markdown
            // Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
            //========================================================================================================

            $msg = ':green_circle: ' . $this->data['username'] . ' bought rank for ' . $data['cost'] . '$. Order: #' . $this->payment_id;

            $json_data = array('content' => "$msg");
            $make_json = json_encode($json_data);

            $ch = curl_init($webhookurl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $make_json);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            $mCount++;
            //}
        }

        return true;
    }

    function create($username, $data, $server)
    {
        global $link;

        $sql = $link->prepare("INSERT INTO AD_PAYMENTS (`username`, `data`, `time`, `status`, `server`) VALUES (:username, :data, '" . time() . "', 0, '{$server}')");
        $sql->execute(array("username" => $username, "data" => $data));
        $this->payment_id = $link->lastInsertId();

        return true;
    }
}