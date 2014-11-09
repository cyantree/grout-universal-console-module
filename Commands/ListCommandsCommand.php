<?php
namespace Grout\Cyantree\UniversalConsoleModule\Commands;

use Cyantree\Grout\Tools\FileTools;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleCommandlineCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleUniversalCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleWebCommand;

class ListCommandsCommand extends UniversalConsoleUniversalCommand
{
    public function execute()
    {
        $this->listCommands(array($this->request->command));
    }

    public function listCommands($excludes = null)
    {
        if ($this->app->getConfig()->developmentMode) {
            $commandPaths = array_merge(
                $this->factory()->config()->productionCommandPaths,
                $this->factory()->config()->developmentCommandPaths
            );

        } else {
            $commandPaths = $this->factory()->config()->developmentCommandPaths;
        }

        if ($excludes) {
            $excludes = array_flip($excludes);
        }

        foreach ($commandPaths as $namespace => $path) {
            $commands = FileTools::listDirectory($path, false);

            foreach ($commands as $command) {
                $commandDir = dirname($command);
                $command = ($commandDir != '.' ? dirname($command) . '/' : '') . pathinfo($command, PATHINFO_FILENAME);

                $commandName = str_replace('\\', '/', $command);

                if (!preg_match('!(.+)Command$!', $commandName, $commandNameData)) {
                    continue;
                }
                $commandName = $commandNameData[1];

                if ($excludes && array_key_exists($commandName, $excludes)) {
                    continue;
                }

                $commandClass = $namespace . str_replace('/', '\\', $commandName) . 'Command';

                $instance = new $commandClass();

                $include = false;

                if ($instance instanceof UniversalConsoleUniversalCommand) {
                    $include = true;

                } elseif ($this->request->isWebRequest && $instance instanceof UniversalConsoleWebCommand) {
                    $include = true;

                } elseif (!$this->request->isWebRequest && $instance instanceof UniversalConsoleCommandlineCommand) {
                    $include = true;
                }

                if ($include) {
                    $this->response->showCommandLink($commandName);
                }
            }
        }
    }
}
