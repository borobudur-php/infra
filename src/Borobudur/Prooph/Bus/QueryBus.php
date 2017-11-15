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
use Borobudur\Infrastructure\Prooph\Message\MessageEnvelope;
use Prooph\ServiceBus\QueryBus as BaseQueryBus;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class QueryBus implements MessageBusInterface
{
    /**
     * @var BaseQueryBus
     */
    private $bus;

    public function __construct(BaseQueryBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(MessageInterface $message)
    {
        $envelope = new MessageEnvelope($message);
        $return = null;

        $this->bus->dispatch($envelope)->then(
            function ($result) use (&$return) {
                $return = $result;
            }
        );

        return $return;
    }
}
