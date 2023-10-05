<?php 
namespace Erahma\FutureFramework;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class MessageBroker 
{
    
    protected AMQPStreamConnection $connection;
    protected $channel;

    public function __construct($host = 'rabbitmq', $port = 5672, $user = 'guest', $pass = 'guest') {
        
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
        $this->channel = $this->connection->channel();
    }    

    function sendMessage( $message = '', $channelName = 'hello' ) {
        $this->channel->queue_declare($channelName , false, false, false, false);

        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, '', $channelName );
        return $this;
    }
    function close() {
        $this->channel->close();
        $this->connection->close();
    }
}
