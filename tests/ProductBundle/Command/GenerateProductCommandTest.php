<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\Command\GenerateProductCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class GenerateProductCommandTest extends TestCase
{
    public function testConfigure(): void
    {
        $cmd = $this->getCommandInstance();
        static::assertInstanceOf(GenerateProductCommand::class, $cmd);
    }

    public function testExecute(): void
    {
        $cmd = $this->getCommandInstance();
        $cmdTester = new CommandTester($cmd);

        try {
            $cmdTester->execute(['command' => $cmd->getName()]);
            static::fail('The command without arguments should throw a \RuntimeException');
        } catch (\Exception $e) {
            static::assertInstanceOf(\RuntimeException::class, $e);
        }

        try {
            $cmdTester->execute([
                'command' => $cmd->getName(),
                'product' => 'Test',
            ]);
            static::fail('The command without "service_id" argument should throw a \RuntimeException');
        } catch (\Exception $e) {
            static::assertInstanceOf(\RuntimeException::class, $e);
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
        $app = new Application();
        $env = $this->createMock(KernelInterface::class);
        $app->add(new GenerateProductCommand($env));

        return $app->find('sonata:product:generate');
    }
}
