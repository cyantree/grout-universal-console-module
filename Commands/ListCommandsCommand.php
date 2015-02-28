<?php
namespace Grout\Cyantree\UniversalConsoleModule\Commands;

use Cyantree\Grout\Tools\FileTools;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleCommandlineCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleWebCommand;

class ListCommandsCommand extends UniversalConsoleCommand
{
    public function execute()
    {
        $this->listCommands(array($this->request->command));
    }

    public function listCommands($excludes = null)
    {
        $commandPaths = $this->factory()->config()->commandPaths;

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


                if ($instance instanceof UniversalConsoleWebCommand) {
                    $include = $this->request->isWebRequest;

                } elseif ($instance instanceof UniversalConsoleCommandlineCommand) {
                    $include = !$this->request->isWebRequest;

                } elseif ($instance) {
                    $include = true;
                }

                if ($include) {
                    $this->response->showCommandLink($commandName);
                }
            }
        }
    }
}
