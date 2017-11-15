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
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractMessageBusInvokeStrategy extends AbstractPlugin
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

                $message = $actionEvent->getParam(
                    MessageBus::EVENT_PARAM_MESSAGE
                );

                $this->assertMessage($message);

                $this->dispatch($message, $actionEvent);

                $actionEvent->setParam(
                    MessageBus::EVENT_PARAM_MESSAGE_HANDLED,
                    true
                );
            },
            MessageBus::PRIORITY_INVOKE_HANDLER
        );
    }

    /**
     * Assert message instance of MessageEnvelope
     *
     * @param mixed $message
     */
    protected function assertMessage($message): void
    {
        if (false === $message instanceof MessageEnvelope) {
            throw new RuntimeException(
                sprintf(
                    'Message "%s" should encapsulated with "MessageEnvelope"',
                    get_class($message)
                )
            );
        }
    }

    /**
     * Check whether message has been handled.
     *
     * @param ActionEvent $actionEvent
     *
     * @return bool
     */
    protected function isMessageHandled(ActionEvent $actionEvent): bool
    {
        return false !== $actionEvent->getParam(
                MessageBus::EVENT_PARAM_MESSAGE_HANDLED,
                false
            );
    }

    /**
     * Dispatch and handle message.
     *
     * @param MessageEnvelope $message
     * @param ActionEvent     $actionEvent
     */
    abstract protected function dispatch(MessageEnvelope $message, ActionEvent $actionEvent): void;
}
