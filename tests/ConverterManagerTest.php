<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ParamConverter\Tests;

use ParamConverter\ConverterManager;
use ParamConverter\Annotation\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ConverterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testPriorities()
    {
        $manager = new ConverterManager();
        $this->assertEquals(array(), $manager->all());

        $high = $this->createParamConverterMock();
        $low = $this->createParamConverterMock();

        $manager->add($low);
        $manager->add($high, 10);

        $this->assertEquals(array($high, $low), $manager->all());
    }

    public function testApply()
    {
        $supported = $this->createParamConverterMock();
        $supported
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(true))
        ;
        $supported
            ->expects($this->once())
            ->method('apply')
            ->will($this->returnValue(false))
        ;

        $invalid = $this->createParamConverterMock();
        $invalid
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(false))
        ;
        $invalid
            ->expects($this->never())
            ->method('apply')
        ;

        $configurations = array(
            new ParamConverter(array(
                'name' => 'var',
            )),
        );

        $manager = new ConverterManager();
        $manager->add($supported);
        $manager->add($invalid);
        $manager->apply(new Request(), $configurations);
    }

    public function testApplyNamedConverter()
    {
        $converter = $this->createParamConverterMock();
        $converter
            ->expects($this->any())
            ->method('supports')
            ->will($this->returnValue(True))
        ;

        $converter
            ->expects($this->any())
            ->method('apply')
        ;

        $request = new Request();
        $request->attributes->set('param', '1234');

        $configuration = new ParamConverter(array(
            'name' => 'param',
            'class' => 'stdClass',
            'converter' => 'test',
        ));

        $manager = new ConverterManager();
        $manager->add($converter, 0, "test");
        $manager->apply($request, array($configuration));
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Converter 'test' does not support conversion of parameter 'param'.
     */
    public function testApplyNamedConverterNotSupportsParameter()
    {
        $converter = $this->createParamConverterMock();
        $converter
            ->expects($this->any())
            ->method('supports')
            ->will($this->returnValue(false))
        ;

        $request = new Request();
        $request->attributes->set('param', '1234');

        $configuration = new ParamConverter(array(
            'name' => 'param',
            'class' => 'stdClass',
            'converter' => 'test',
        ));

        $manager = new ConverterManager();
        $manager->add($converter, 0, "test");
        $manager->apply($request, array($configuration));
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage No converter named 'test' found for conversion of parameter 'param'.
     */
    public function testApplyNamedConverterNoConverter()
    {
        $request = new Request();
        $request->attributes->set('param', '1234');

        $configuration = new ParamConverter(array(
            'name' => 'param',
            'class' => 'stdClass',
            'converter' => 'test',
        ));

        $manager = new ConverterManager();
        $manager->apply($request, array($configuration));
    }

    public function testApplyNotCalledOnAlreadyConvertedObjects()
    {
        $converter = $this->createParamConverterMock();
        $converter
            ->expects($this->never())
            ->method('supports')
        ;

        $converter
            ->expects($this->never())
            ->method('apply')
        ;

        $request = new Request();
        $request->attributes->set('converted', new \stdClass);

        $configuration = new ParamConverter(array(
            'name' => 'converted',
            'class' => 'stdClass',
        ));

        $manager = new ConverterManager();
        $manager->add($converter);
        $manager->apply($request, array($configuration));
    }

    protected function createParamConverterMock()
    {
        return $this->getMock('ParamConverter\Converter\ConverterInterface');
    }
}
