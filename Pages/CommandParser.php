<?php
namespace Grout\Cyantree\UniversalConsoleModule\Pages;

use Cyantree\Grout\App\Page;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleRequest;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleResponse;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleUniversalCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleWebCommand;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleWebResponse;
use Grout\Cyantree\UniversalConsoleModule\UniversalConsoleFactory;

class CommandParser extends Page
{
    /** @var UniversalConsoleResponse */
    private $response;

    public function parseTask()
    {
        $f = UniversalConsoleFactory::get($this->app);

        $request = UniversalConsoleRequest::createFromString(
            $this->task->request->post->asString('command')->asInput(),
            $this->task->request->post->get('unverifiedExecution') == 'true'
        );
        $request->isWebRequest = true;

        $this->response = new UniversalConsoleWebResponse();

        if ($this->task->request->post->get('key') != $f->generateExecutionKey()) {
            $this->response->showError('Invalid execution key.');

        } else {
            $this->processRequest($request);
        }

        $this->showResponse($this->response);
    }

    public function processRequest(UniversalConsoleRequest $request)
    {
        $factory = UniversalConsoleFactory::get($this->app);
        $config = $factory->config();

        $command = str_replace('/', '\\', $request->command);

        if (!preg_match('!^[a-zA-Z0-9_\\\]+$!', $command)) {
            $this->response->showError('Command not found: ' . $command);

        } else {
            $found = false;

            $commandFile = null;
            $commandClass = null;
            foreach ($config->productionCommandPaths as $commandNamespace => $commandPath) {
                $commandFile = $commandPath . $command . 'Command.php';

                if (is_file($commandFile)) {
                    $commandClass = $commandNamespace . $command . 'Command';
                    $found = true;
                    break;
                }
            }

            if (!$found && $this->app->getConfig()->developmentMode) {
                foreach ($config->developmentCommandPaths as $commandNamespace => $commandPath) {
                    $commandFile = $commandPath . $command . 'Command.php';

                    if (is_file($commandFile)) {
                        $commandClass = $commandNamespace . $command . 'Command';
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                /** @var UniversalConsoleCommand $c */
                require_once($commandFile);

                $c = new $commandClass();

                if (!$c instanceof UniversalConsoleUniversalCommand && !$c instanceof UniversalConsoleWebCommand) {
                    $this->response->showError('Command not accessible: ' . $command);
                    return;
                }

                $c->request = $request;
                $c->task = $this->task;
                $c->app = $this->app;
                $c->page = $this;

                $c->response = $this->response;
                $this->response->redirectToCommand = $request->args->get('redirect');

                $c->init();

                $c->execute();

                $c->deInit();

            } else {
                $this->response->showError('Command not found: ' . $command);
            }
        }
    }

    private function showResponse(UniversalConsoleWebResponse $response)
    {
        $this->setResult(
            json_encode(
                array(
                    'messages' => $response->messages,
                    'redirect' => array(
                        'command' => $response->redirectToCommand,
                        'delay' => $response->redirectToDelay
                    )
                )
            )
        );
    }

    public function parseError($code, $data = null)
    {
        $response = new UniversalConsoleWebResponse();
        $response->showError('An unknown error has occurred.');

        $this->showResponse($response);
    }
}
