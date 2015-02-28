<?php
namespace Grout\Cyantree\UniversalConsoleModule\Pages;

use Cyantree\Grout\App\Page;
use Grout\Cyantree\UniversalConsoleModule\UniversalConsoleFactory;

class Console extends Page
{
    public $command;
    public $executionKey;

    public function parseTask()
    {
        $factory = UniversalConsoleFactory::get($this->app);

        $this->command = $this->task->request->get->get('command');

        if ($this->command == '') {
            $this->command = $factory->config()->defaultCommand;
        }

        $this->executionKey = $factory->generateExecutionKey();

        $this->setResult($factory->templates()->load('.Cyantree\UniversalConsoleModule::console.html', null, false));
    }
}
