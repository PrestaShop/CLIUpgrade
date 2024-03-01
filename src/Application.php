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

namespace App;

use Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Application extends BaseApplication
{
    public const VERSION = '@git-version@';

    /** @var string */
    protected $defaultCommand = 'list';

    public function __construct(KernelInterface $kernel, ?string $forceVersion = null)
    {
        parent::__construct($kernel);
        $cliAppVersion = $forceVersion ?? self::VERSION;
        $this->setVersion($cliAppVersion);
        ConsoleApplication::__construct('PrestaShop Upgrade Assistant', $cliAppVersion);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display help for the given command. When no command is given display help for the <info>' . $this->defaultCommand . '</info> command'),
            new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Do not output any message'),
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption('--ansi', '', InputOption::VALUE_NEGATABLE, 'Force (or disable --no-ansi) ANSI output', null),
            new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        // Remove the env option from the list of global options
        $options = $this->getDefinition()->getOptions();
        unset($options['env']);
        $this->getDefinition()->setOptions($options);

        // Run asked command
        return parent::run($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    public function getLongVersion(): string
    {
        return ConsoleApplication::getLongVersion() . ' (@datetime@)'
            . "\nby <comment>PrestaShop SA and Contributors</comment>";
    }
}
