<?php
namespace Grout\Cyantree\UniversalConsoleModule;

use Cyantree\Grout\App\Module;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleConfig;

class UniversalConsoleModule extends Module
{
    public function init()
    {
        if ($this->app->isConsole) {
            $this->addRoute(':::%%command,.*%%/', 'Pages\Commandline');

        } else {
            $this->addNamedRoute('console', '', 'Pages\Console');
            $this->addNamedRoute('console-parser', 'parser/', 'Pages\CommandParser');
        }

        $config = new UniversalConsoleConfig();
        $config->commandPaths[$this->definition->namespace . 'Commands\\'] = $this->definition->path . 'Commands/';

        $this->app->configs->setDefaultConfig($this->id, $config);
    }
}
