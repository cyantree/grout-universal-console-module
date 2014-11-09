<?php
namespace Grout\Cyantree\UniversalConsoleModule;

use Cyantree\Grout\App\Module;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleConfig;
use Grout\Cyantree\WebConsoleModule\Types\WebConsoleConfig;

class UniversalConsoleModule extends Module
{
    public function init()
    {
        if ($this->app->isConsole) {
            $this->addRoute('App:::%%command,.*%%/', 'Pages\Commandline');

        } else {
            $this->addNamedRoute('console', '', 'Pages\Console');
            $this->addNamedRoute('console-parser', 'parser/', 'Pages\CommandParser');
        }

        $config = new UniversalConsoleConfig();
        $config->productionCommandPaths[$this->namespace . 'Commands\\'] = $this->path . 'Commands/';

        $this->app->configs->setDefaultConfig($this->id, $config);
    }
}
