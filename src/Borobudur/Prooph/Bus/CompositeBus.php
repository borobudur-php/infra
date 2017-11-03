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

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CompositeBus implements MessageBusInterface
{
    /**
     * @var MessageBusInterface[]
     */
    private $buses = [];

    /**
     * Constructor.
     *
     * @param MessageBusInterface[] $buses
     */
    public function __construct(array $buses)
    {
        foreach ($buses as $type => $bus) {
            $this->add($type, $bus);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(MessageInterface $message)
    {
        $type = $message->getMessageType();
        if (!isset($this->buses[$type])) {
            throw new \InvalidArgumentException(
                sprintf('Bus with type "%s" is not registered', $type)
            );
        }

        return $this->buses[$type]->dispatch($message);
    }

    /**
     * Register a bus to this composite.
     *
     * @param string              $type
     * @param MessageBusInterface $bus
     */
    public function add(string $type, MessageBusInterface $bus): void
    {
        $this->buses[$type] = $bus;
    }
}
