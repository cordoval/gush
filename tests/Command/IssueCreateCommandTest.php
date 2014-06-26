<?php

/**
 * This file is part of Gush package.
 *
 * (c) 2013-2014 Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Command;

use Gush\Command\Issue\IssueCreateCommand;

class IssueCreateCommandTest extends BaseTestCase
{
    const ISSUE_TITLE = 'bug title';
    const ISSUE_DESCRIPTION = 'not working!';

    public function testCommand()
    {
        $questionHelper = $this->expectDialog();
        $editor = $this->expectEditor();
        $tester = $this->getCommandTester($command = new IssueCreateCommand());
        $command->getHelperSet()->set($questionHelper, 'question');
        $command->getHelperSet()->set($editor, 'editor');
        $tester->execute(['--org' => 'gushphp', '--repo' => 'gush'], ['interactive' => false]);

        $this->assertEquals('Created issue https://github.com/gushphp/gush/issues/77', trim($tester->getDisplay(true)));
    }

    public function testCommandWithTitleAndBodyOptions()
    {
        $tester = $this->getCommandTester($command = new IssueCreateCommand());
        $tester->execute(
            [
                '--org' => 'gushphp',
                '--repo' => 'gush',
                '--issue_title' => self::ISSUE_TITLE,
                '--issue_body' => self::ISSUE_DESCRIPTION
            ],
            [
                'interactive' => false
            ]
        );

        $this->assertEquals('Created issue https://github.com/gushphp/gush/issues/77', trim($tester->getDisplay(true)));
    }

    private function expectDialog()
    {
        $questionHelper = $this->getMock(
            'Symfony\Component\Console\Helper\QuestionHelper',
            ['ask']
        );
        $questionHelper->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(self::ISSUE_TITLE))
        ;

        return $questionHelper;
    }

    private function expectEditor()
    {
        $editor = $this->getMock(
            'Gush\Helper\EditorHelper',
            ['fromString']
        );
        $editor->expects($this->at(0))
            ->method('fromString')
            ->will($this->returnValue(self::ISSUE_DESCRIPTION))
        ;

        return $editor;
    }
}
