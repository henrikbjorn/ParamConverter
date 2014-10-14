<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ParamConverter\Tests\EventListener;

use ParamConverter\EventListener\ControllerListener;
use ParamConverter\Tests\EventListener\Fixture\FooControllerParamConverterAtClassAndMethod;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ControllerListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->listener = new ControllerListener(new AnnotationReader());
        $this->request = $this->createRequest();
    }

    public function tearDown()
    {
        $this->listener = null;
        $this->request = null;
    }

    public function testMultipleParamConverterAnnotationsOnMethod()
    {
        $paramConverter = new \ParamConverter\Annotation\ParamConverter(array());
        $controller = new FooControllerParamConverterAtClassAndMethod();
        $this->event = $this->getFilterControllerEvent(array($controller, 'barAction'), $this->request);
        $this->listener->onKernelController($this->event);

        $annotations = $this->request->attributes->get('_converters');
        $this->assertNotNull($annotations);
        $this->assertArrayHasKey(0, $annotations);
        $this->assertInstanceOf('ParamConverter\Annotation\ParamConverter', $annotations[0]);
        $this->assertEquals('test', $annotations[0]->getName());

        $this->assertArrayHasKey(1, $annotations);
        $this->assertInstanceOf('ParamConverter\Annotation\ParamConverter', $annotations[1]);
        $this->assertEquals('test2', $annotations[1]->getName());

        $this->assertEquals(2, count($annotations));
    }

    protected function createRequest(Cache $cache = null)
    {
        return new Request(array(), array(), array(
            '_cache' => $cache,
        ));
    }

    protected function getFilterControllerEvent($controller, Request $request)
    {
        $mockKernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');

        return new FilterControllerEvent($mockKernel, $controller, $request, HttpKernelInterface::MASTER_REQUEST);
    }
}
