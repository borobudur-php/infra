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

namespace Borobudur\Infrastructure\Symfony\Exception;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class UnknownDriverException extends \Exception
{
    public function __construct($driver)
    {
        parent::__construct(sprintf('Unknown driver "%s".', $driver));
    }
}
