<?php
namespace Grout\Cyantree\UniversalConsoleModule\Types;

use Cyantree\Grout\App\App;
use Cyantree\Grout\App\Task;

use Grout\Cyantree\UniversalConsoleModule\Pages\Console;
use Grout\Cyantree\UniversalConsoleModule\UniversalConsoleFactory;

abstract class UniversalConsoleCommand
{
    /** @var Task */
    public $task;

    /** @var App */
    public $app;

    /** @var Console */
    public $page;

    /** @var UniversalConsoleRequest */
    public $request;

    /** @var UniversalConsoleResponse */
    public $response;

    public $isUnverifiedExecution = false;
    public $allowUnverifiedExecution = false;

    /** @return UniversalConsoleFactory */
    public function factory()
    {
        return UniversalConsoleFactory::get($this->app);
    }

    public function init()
    {

    }

    public function deInit()
    {

    }

    public function execute()
    {

    }

    public function onError()
    {

    }
}
