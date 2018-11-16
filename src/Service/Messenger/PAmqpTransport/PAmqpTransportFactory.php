<?php

namespace App\Service\Messenger\PAmqpTransport;

use Symfony\Component\Messenger\Transport\Serialization\DecoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\EncoderInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class PAmqpTransportFactory implements TransportFactoryInterface
{
    private $encoder;
    private $decoder;
    private $debug;

    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder, bool $debug)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->debug = $debug;
    }

    public function createTransport(string $dsn, array $options): TransportInterface
    {
        return new PAmqpTransport($this->encoder, $this->decoder, Connection::fromDsn($dsn, $options, $this->debug));
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'pamqp://');
    }
}
