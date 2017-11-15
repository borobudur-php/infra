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

namespace Borobudur\Infrastructure\Prooph\Message;

use Borobudur\Component\Messaging\Message\MessageInterface;
use Prooph\Common\Messaging\HasMessageName;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageEnvelope implements MessageInterface, HasMessageName
{
    /**
     * @var MessageInterface
     */
    private $message;

    /**
     * @var ReflectionClass
     */
    private $reflection;

    public function __construct(MessageInterface $message)
    {
        $this->message = $message;
        $this->reflection = new ReflectionClass(get_class($message));
    }

    /**
     * {@inheritdoc}
     */
    public function messageName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType(): string
    {
        return $this->message->getMessageType();
    }

    /**
     * @return MessageInterface|mixed
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }
}
