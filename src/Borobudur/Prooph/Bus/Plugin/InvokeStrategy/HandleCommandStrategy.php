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

namespace Borobudur\Infrastructure\Prooph\Bus\Plugin\InvokeStrategy;

use Borobudur\Infrastructure\Prooph\Message\MessageEnvelope;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use ReflectionMethod;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class HandleCommandStrategy extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public function attachToMessageBus(MessageBus $messageBus): void
    {
        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_DISPATCH,
            function (ActionEvent $actionEvent) {
                if ($this->isMessageHandled($actionEvent)) {
                    return;
                }

                $this->dispatch($actionEvent);
            },
            MessageBus::PRIORITY_INVOKE_HANDLER
        );
    }

    /**
     * Dispatch the message.
     *
     * @param ActionEvent $actionEvent
     */
    private function dispatch(ActionEvent $actionEvent): void
    {
        $message = $actionEvent->getParam(
            MessageBus::EVENT_PARAM_MESSAGE
        );

        if (false === $message instanceof MessageEnvelope) {
            throw new RuntimeException(
                sprintf(
                    'Message "%s" should encapsulated with "MessageEnvelope"',
                    get_class($message)
                )
            );
        }

        $handler = $actionEvent->getParam(
            MessageBus::EVENT_PARAM_MESSAGE_HANDLER
        );

        $return = $handler->handle($message->getMessage());

        $actionEvent->setParam(
            MessageBus::EVENT_PARAM_MESSAGE_HANDLED,
            true
        );

        $this->call($message, 'setReturn', $return);
    }

    /**
     * Check whether message has been handled.
     *
     * @param ActionEvent $actionEvent
     *
     * @return bool
     */
    private function isMessageHandled(ActionEvent $actionEvent): bool
    {
        return false !== $actionEvent->getParam(
                MessageBus::EVENT_PARAM_MESSAGE_HANDLED,
                false
            );
    }

    /**
     * Call a method from specified object.
     *
     * @param object $object
     * @param string $method
     * @param array  ...$arguments
     */
    private function call(object $object, string $method, ...$arguments): void
    {
        $setReturn = new ReflectionMethod(get_class($object), $method);
        $setReturn->setAccessible(true);
        $setReturn->invoke($object, ...$arguments);
    }
}
