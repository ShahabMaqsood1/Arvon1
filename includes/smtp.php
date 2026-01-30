<?php
// Simple SMTP Mailer for Spacemail
class SMTPMailer {
    public $host;
    public $port;
    public $username;
    public $password;
    public $from;
    public $fromName;
    public $to;
    public $subject;
    public $body;
    public $isHTML = true;
    public $replyTo = null;
    public $replyToName = null;
    
    private $socket;
    
    public function send() {
        try {
            $this->connect();
            $this->auth();
            $this->sendMessage();
            $this->disconnect();
            return true;
        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }
    
    private function connect() {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 30);
        
        if (!$this->socket) {
            throw new Exception("Failed to connect to SMTP server: $errstr ($errno)");
        }
        
        $this->getResponse();
        $this->sendCommand("EHLO " . $_SERVER['SERVER_NAME']);
        $this->sendCommand("STARTTLS");
        
        stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $this->sendCommand("EHLO " . $_SERVER['SERVER_NAME']);
    }
    
    private function auth() {
        $this->sendCommand("AUTH LOGIN");
        $this->sendCommand(base64_encode($this->username));
        $this->sendCommand(base64_encode($this->password));
    }
    
    private function sendMessage() {
        $this->sendCommand("MAIL FROM: <{$this->from}>");
        $this->sendCommand("RCPT TO: <{$this->to}>");
        $this->sendCommand("DATA");
        
        $headers = "From: {$this->fromName} <{$this->from}>\r\n";
        $headers .= "To: <{$this->to}>\r\n";
        
        // Add Reply-To if set
        if ($this->replyTo) {
            if ($this->replyToName) {
                $headers .= "Reply-To: {$this->replyToName} <{$this->replyTo}>\r\n";
            } else {
                $headers .= "Reply-To: {$this->replyTo}\r\n";
            }
        }
        
        $headers .= "Subject: {$this->subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($this->isHTML) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        $message = $headers . "\r\n" . $this->body . "\r\n.";
        
        fwrite($this->socket, $message . "\r\n");
        $this->getResponse();
    }
    
    private function disconnect() {
        $this->sendCommand("QUIT");
        fclose($this->socket);
    }
    
    private function sendCommand($command) {
        fwrite($this->socket, $command . "\r\n");
        return $this->getResponse();
    }
    
    private function getResponse() {
        $response = '';
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return $response;
    }
}
?>
