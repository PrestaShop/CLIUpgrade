<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA.
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

namespace PrestaShop\CliUpgradeBundle\Command;

use App\Application;
use PrestaShop\CoreUpgradeBundle\CoreUpgradeBundle;
use PrestaShop\CoreUpgradeBundle\Extractor\Github\CliRepositoryExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Display the current version of this PrestaShop Upgrade Assistant Core and Cli App.
 * It also checks if the current version is the latest one.
 */
class VersionCommand extends Command
{
    protected static $defaultName = 'version';
    protected static $defaultDescription = 'Display the current version of this PrestaShop Upgrade Assistant';

    public function __construct(
        private readonly CliRepositoryExtractor $cliRepositoryExtractor
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $table = $io->createTable();
        $table->setStyle('compact');
        $table->setRows([
            ['CLI App', "<info>" . Application::VERSION . "</info>"],
            ['Core Upgrade', "<info>" . CoreUpgradeBundle::VERSION . "</info>"],
        ]);
        $table->render();

        $lastVersionAvailable = $this->cliRepositoryExtractor->getLastVersion();
        if ($lastVersionAvailable !== null && version_compare(Application::VERSION, $lastVersionAvailable, '<')) {
            $io->warning(sprintf('You are not using the latest version (%s)!', $lastVersionAvailable));
        }

        return Command::SUCCESS;
    }
}
