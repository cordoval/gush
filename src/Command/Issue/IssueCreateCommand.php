<?php

/**
 * This file is part of Gush package.
 *
 * (c) 2013-2014 Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Command\Issue;

use Gush\Command\BaseCommand;
use Gush\Helper\EditorHelper;
use Gush\Feature\GitRepoFeature;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Creates an issue
 */
class IssueCreateCommand extends BaseCommand implements GitRepoFeature
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('issue:create')
            ->setDescription('Creates an issue')
            ->addOption('issue_title', null, InputOption::VALUE_REQUIRED, 'Issue Title')
            ->addOption('issue_body', null, InputOption::VALUE_REQUIRED, 'Issue Body')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command creates a new issue for either the current or the given organization
and repository:

    <info>$ gush %command.name%</info>

EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adapter = $this->getIssueTracker();
        $emptyValidator = function ($string) {
            if (trim($string) == '') {
                throw new \Exception('This value can not be empty');
            }

            return $string;
        };

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        if (!$title = $input->getOption('issue_title')) {
            $title = $questionHelper->ask(
                $input,
                $output,
                (new Question('Issue title: '))->setValidator($emptyValidator)
            );
        }

        if (!$body = $input->getOption('issue_body')) {
            /** @var EditorHelper $editor */
            $editor = $this->getHelper('editor');
            $body = $editor->fromString('');
        }

        $issue = $adapter->openIssue($title, $body);

        $url = $adapter->getIssueUrl($issue);
        $output->writeln("Created issue {$url}");

        return self::COMMAND_SUCCESS;
    }
}
