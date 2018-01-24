<?php
/**
 * This file is part of the Borobudur package.
 *
 * (c) 2017 Borobudur <http://borobudur.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Borobudur\Infrastructure\Prooph\Bus;

use Borobudur\Component\Messaging\Bus\MessageBusInterface;
use Borobudur\Component\Messaging\Message\MessageInterface;
use Borobudur\Component\Messaging\Message\PayloadMessageInterface;
use Borobudur\Component\Messaging\Message\ReturnableMessageInterface;
use Borobudur\Infrastructure\Prooph\Message\MessageEnvelope;
use Prooph\ServiceBus\CommandBus as BaseCommandBus;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CommandBus implements MessageBusInterface
{
    /**
     * @var BaseCommandBus
     */
    private $bus;

    public function __construct(BaseCommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(MessageInterface $message)
    {
        $envelope = new MessageEnvelope($message);

        $this->bus->dispatch($envelope);

        if ($message instanceof ReturnableMessageInterface) {
            return $message->getMessageReturn();
        }

        if ($message instanceof PayloadMessageInterface) {
            return $message->getMessagePayload()->all();
        }

        return $message;
    }
}
