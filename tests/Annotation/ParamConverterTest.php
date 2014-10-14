<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ParamConverter\Tests\Annotation;

use ParamConverter\Annotation\ParamConverter;

class ParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testUndefinedSetterThrowsException()
    {
        new ParamConverter(array(
            'doesNotExists' => true,
        ));
    }
}
