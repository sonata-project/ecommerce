<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\Command;

use Sonata\ProductBundle\Command\GenerateProductCommand;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class GenerateProductCommandTest extends PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $cmd = $this->getCommandInstance();
        $this->assertInstanceOf('Sonata\ProductBundle\Command\GenerateProductCommand', $cmd);
    }

    public function testExecute()
    {
        $cmd = $this->getCommandInstance();
        $cmdTester = new CommandTester($cmd);

        try {
            $cmdTester->execute(array('command' => $cmd->getName()));
            $this->fail('The command without arguments should throw a \RuntimeException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);
        }

        try {
            $cmdTester->execute(array(
                'command' => $cmd->getName(),
                'product' => 'Test',
            ));
            $this->fail('The command without "service_id" argument should throw a \RuntimeException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);
        }

        /*
        try {
            $cmdTester->execute(array(
                'command' => $cmd->getName(),
                'product' => 'Test',
                'service_id' => 2
            ));
        } catch (\Exception $e) {
            $this->fail('No exception should be thrown when all arguments are provided');
        } */
    }

    /**
     * @return \Symfony\Component\Console\Command\Command
     */
    private function getCommandInstance()
    {
        $kernel = $this->createMock('Symfony\Component\HttpKernel\Kernel');
        $app = new Application($kernel);
        $app->add(new GenerateProductCommand());

        return $app->find('sonata:product:generate');
    }
}
