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

namespace Borobudur\Infrastructure\Doctrine;

use Borobudur\Component\Ddd;
use Borobudur\Component\Identifier\Identifier;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Model implements Ddd\Model
{
    /**
     * @var Identifier
     */
    protected $id;

    /**
     * @return Identifier|mixed
     */
    public function getId(): Identifier
    {
        return $this->id;
    }
}
