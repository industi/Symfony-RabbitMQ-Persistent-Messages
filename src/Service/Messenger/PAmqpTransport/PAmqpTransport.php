<?php

namespace App\Service\Messenger\PAmqpTransport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\DecoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\EncoderInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;



class PAmqpTransport implements TransportInterface
{
    private $encoder;
    private $decoder;
    private $connection;
    private $receiver;
    private $sender;

    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder, Connection $connection)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function receive(callable $handler): void
    {
        ($this->receiver ?? $this->getReceiver())->receive($handler);
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): void
    {
        ($this->receiver ?? $this->getReceiver())->stop();
    }

    /**
     * {@inheritdoc}
     */
    public function send(Envelope $envelope): void
    {
        ($this->sender ?? $this->getSender())->send($envelope);
    }

    private function getReceiver()
    {
        return $this->receiver = new PAmqpReceiver($this->decoder, $this->connection);
    }

    private function getSender()
    {
        return $this->sender = new PAmqpSender($this->encoder, $this->connection);
    }
}
