<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ParamConverter\Tests\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use ParamConverter\Converter\DateTimeConverter;
use ParamConverter\Annotation\ParamConverter;

class DateTimeConverterTest extends \PHPUnit_Framework_TestCase
{
    private $converter;

    public function setUp()
    {
        $this->converter = new DateTimeConverter();
    }

    public function testSupports()
    {
        $config = $this->createConfiguration("DateTime");
        $this->assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        $this->assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        $this->assertFalse($this->converter->supports($config));
    }

    public function testApply()
    {
        $request = new Request(array(), array(), array('start' => '2012-07-21 00:00:00'));
        $config = $this->createConfiguration("DateTime", "start");

        $this->converter->apply($request, $config);

        $this->assertInstanceOf("DateTime", $request->attributes->get('start'));
        $this->assertEquals('2012-07-21', $request->attributes->get('start')->format('Y-m-d'));
    }

    public function testApplyInvalidDate404Exception()
    {
        $request = new Request(array(), array(), array('start' => 'Invalid DateTime Format'));
        $config = $this->createConfiguration("DateTime", "start");

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', 'Invalid date given.');
        $this->converter->apply($request, $config);
    }

    public function testApplyWithFormatInvalidDate404Exception()
    {
        $request = new Request(array(), array(), array('start' => '2012-07-21'));
        $config = $this->createConfiguration("DateTime", "start", array(
            'format' => 'd.m.Y',
        ));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', 'Invalid date given.');

        $this->converter->apply($request, $config);
    }

    public function testApplyOptionalWithEmptyAttribute()
    {
        $request = new Request(array(), array(), array('start' => null));
        $config = $this->createConfiguration('DateTime', 'start');
        $config->setIsOptional(true);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get('start'));
    }

    public function createConfiguration($class = null, $name = null, $options = array())
    {
        $config = new ParamConverter();
        $config->setClass($class);
        $config->setName($name);
        $config->setOptions($options);

        return $config;
    }
}
