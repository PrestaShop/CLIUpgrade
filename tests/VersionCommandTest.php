<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace App\Tests;

use App\Application;
use PrestaShop\CliUpgradeBundle\Command\VersionCommand;
use PrestaShop\CoreUpgradeBundle\CoreUpgradeBundle;
use PrestaShop\CoreUpgradeBundle\Extractor\Github\CliRepositoryExtractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class VersionCommandTest extends KernelTestCase
{
    private Application $application;
    private CliRepositoryExtractor $cliRepositoryExtractorMock;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // Mock the github extractor
        $this->cliRepositoryExtractorMock = $this->getMockBuilder(CliRepositoryExtractor::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Setup cli application and command tester
        $this->application = new Application(self::bootKernel(), '1.2.3');
        $this->application->add(new VersionCommand($this->application, $this->cliRepositoryExtractorMock));
        $this->commandTester = new CommandTester($this->application->find('version'));
    }

    public function testWithoutNewVersions(): void
    {
        // Mock extractor to return the actual version
        $this->cliRepositoryExtractorMock->method('getLastVersion')->willReturn('1.2.3');

        // Execute the command
        $this->commandTester->execute([]);

        // Assertions
        $this->commandTester->assertCommandIsSuccessful();
        $outputDisplay = $this->commandTester->getDisplay();
        $this->assertStringContainsString('CLI App', $outputDisplay);
        $this->assertStringContainsString('Core Upgrade', $outputDisplay);
        $this->assertStringContainsString($this->application->getVersion(), $outputDisplay);
        $this->assertStringContainsString(CoreUpgradeBundle::VERSION, $outputDisplay);
        $this->assertStringNotContainsString('[WARNING] You are not using the latest version', $outputDisplay);
    }

    public function testWithNewVersionAvailable(): void
    {
        // Mock extractor to return a newer version available
        $this->cliRepositoryExtractorMock->method('getLastVersion')->willReturn('1.2.4');

        // Execute the command
        $this->commandTester->execute([]);

        // Assertions
        $this->commandTester->assertCommandIsSuccessful();
        $outputDisplay = $this->commandTester->getDisplay();
        $this->assertStringContainsString('CLI App', $outputDisplay);
        $this->assertStringContainsString('Core Upgrade', $outputDisplay);
        $this->assertStringContainsString($this->application->getVersion(), $outputDisplay);
        $this->assertStringContainsString(CoreUpgradeBundle::VERSION, $outputDisplay);
        $this->assertStringContainsString('[WARNING] You are not using the latest version (1.2.4)!', $outputDisplay);
    }
}