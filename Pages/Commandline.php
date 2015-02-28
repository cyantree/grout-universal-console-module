<?php
namespace Grout\Cyantree\UniversalConsoleModule\Pages;

use Cyantree\Grout\App\Page;
use Cyantree\Grout\Filter\ArrayFilter;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleCommandlineCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleCommandlineResponse;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleRequest;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleWebCommand;
use Grout\Cyantree\UniversalConsoleModule\UniversalConsoleFactory;

class Commandline extends Page
{
    public function parseTask()
    {
        $args = $this->task->request->get;

        $command = $this->task->vars->get('command');

        $factory = UniversalConsoleFactory::get($this->app);
        $config = $factory->config();

        if (!$command) {
            $command = $config->defaultCommand;
        }

        $this->executeCommand($command, $args->getData());
    }

    private function executeCommand($command, $data)
    {
        $request = new UniversalConsoleRequest();
        $request->command = $command;
        $request->args = new ArrayFilter($data);
        $request->isWebRequest = false;

        $response = new UniversalConsoleCommandlineResponse();

        $config = UniversalConsoleFactory::get($this->app)->config();

        $command = str_replace('/', '\\', $request->command);

        if (!preg_match('!^[a-zA-Z0-9_\\\]+$!', $command)) {
            $response->showError('Command not found: ' . $command);

        } else {
            $found = false;

            $commandFile = null;
            $commandClass = null;

            foreach ($config->commandPaths as $commandNamespace => $commandPath) {
                $commandFile = $commandPath . $command . 'Command.php';

                if (is_file($commandFile)) {
                    $commandClass = $commandNamespace . $command . 'Command';
                    $found = true;
                    break;
                }
            }

            if ($found) {
                /** @var UniversalConsoleCommand $c */
                require_once($commandFile);


                $c = new $commandClass();

                if ($c instanceof UniversalConsoleWebCommand) {
                    $response->showError('Command not accessible: ' . $command);
                    return;
                }

                $c->request = $request;
                $c->task = $this->task;
                $c->app = $this->app;
                $c->page = $this;

                $c->response = $response;

                $c->init();

                $c->execute();

                $c->deInit();

                if ($response->redirectToCommand) {
                    usleep(1000000 * $response->redirectToDelay);
                    $request = UniversalConsoleRequest::createFromString($response->redirectToCommand);
                    $this->executeCommand($request->command, $request->args->getData());
                }

            } else {
                $response->showError('Command not found: ' . $command);
            }
        }
    }
}
