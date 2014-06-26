<?php

/**
 * This file is part of Gush package.
 *
 * (c) 2013-2014 Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Subscriber;

use Gush\Event\CommandEvent;
use Gush\Event\GushEvents;
use Gush\Feature\GitRepoFeature;
use Gush\Helper\GitHelper;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GitRepoSubscriber implements EventSubscriberInterface
{
    /** @var \Gush\Helper\GitHelper */
    private $gitHelper;

    public function __construct(GitHelper $gitHelper)
    {
        $this->gitHelper = $gitHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            GushEvents::DECORATE_DEFINITION => 'decorateDefinition',
            GushEvents::INITIALIZE => 'initialize',
        ];
    }

    public function decorateDefinition(CommandEvent $event)
    {
        $command = $event->getCommand();

        if (!$command instanceof GitRepoFeature) {
            return;
        }

        $command
            ->addOption(
                'org',
                'o',
                InputOption::VALUE_REQUIRED,
                'Name of the Git Package organization',
                $this->gitHelper->getVendorName()
            )
            ->addOption(
                'repo',
                'r',
                InputOption::VALUE_REQUIRED,
                'Name of the Git Package repository',
                $this->gitHelper->getRepoName()
            )
        ;
    }

    public function initialize(ConsoleEvent $event)
    {
        $command = $event->getCommand();

        /** @var \Gush\Command\BaseCommand $command */
        if ($command instanceof GitRepoFeature) {
            $input = $event->getInput();

            /** @var \Gush\Adapter\BaseAdapter $adapter */
            $adapter = $command->getAdapter();
            $adapter
                ->setRepository($input->getOption('repo'))
                ->setUsername($input->getOption('org'))
            ;

            /** @var \Gush\Adapter\BaseIssueTracker $issueTracker */
            $issueTracker = $command->getIssueTracker();
            $issueTracker
                ->setRepository($input->getOption('repo'))
                ->setUsername($input->getOption('org'))
            ;
        }
    }
}
