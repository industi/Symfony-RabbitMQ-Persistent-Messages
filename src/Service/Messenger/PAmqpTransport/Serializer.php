<?php

namespace App\Service\Messenger\PAmqpTransport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\DecoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\EncoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerConfiguration;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class Serializer implements DecoderInterface, EncoderInterface
{
    private $serializer;
    private $format;
    private $context;

    public function __construct(SerializerInterface $serializer, string $format = 'json', array $context = array())
    {
        $this->serializer = $serializer;
        $this->format = $format;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        if (empty($encodedEnvelope['body']) || empty($encodedEnvelope['headers'])) {
            throw new \InvalidArgumentException('Encoded envelope should have at least a `body` and some `headers`.');
        }

        if (empty($encodedEnvelope['headers']['type'])) {
            throw new \InvalidArgumentException('Encoded envelope does not have a `type` header.');
        }

        $envelopeItems = isset($encodedEnvelope['headers']['X-Message-Envelope-Items']) ? unserialize($encodedEnvelope['headers']['X-Message-Envelope-Items']) : array();

        $context = $this->context;
        /** @var SerializerConfiguration|null $serializerConfig */
        if ($serializerConfig = $envelopeItems[SerializerConfiguration::class] ?? null) {
            $context = $serializerConfig->getContext() + $context;
        }

        $message = $this->serializer->deserialize($encodedEnvelope['body'], $encodedEnvelope['headers']['type'], $this->format, $context);

        return new Envelope($message, $envelopeItems);
    }

    /**
     * {@inheritdoc}
     */
    public function encode(Envelope $envelope): array
    {
        $context = $this->context;
        /** @var SerializerConfiguration|null $serializerConfig */
        if ($serializerConfig = $envelope->get(SerializerConfiguration::class)) {
            $context = $serializerConfig->getContext() + $context;
        }

        $headers = array('type' => \get_class($envelope->getMessage()));
        if ($configurations = $envelope->all()) {
            $headers['X-Message-Envelope-Items'] = serialize($configurations);
        }

        return array(
            'body' => $this->serializer->serialize($envelope->getMessage(), $this->format, $context),
            'headers' => $headers,
        );
    }
}
