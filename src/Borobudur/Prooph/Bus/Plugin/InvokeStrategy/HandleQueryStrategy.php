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
use Prooph\ServiceBus\QueryBus;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class HandleQueryStrategy extends AbstractMessageBusInvokeStrategy
{
    /**
     * Dispatch and handle message.
     *
     * @param MessageEnvelope $message
     * @param ActionEvent     $actionEvent
     */
    protected function dispatch(MessageEnvelope $message, ActionEvent $actionEvent): void
    {
        $deferred = $actionEvent->getParam(QueryBus::EVENT_PARAM_DEFERRED);
        $handler = $actionEvent->getParam(
            MessageBus::EVENT_PARAM_MESSAGE_HANDLER
        );

        $handler->handle($message->getMessage(), $deferred);
    }
}
