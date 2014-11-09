<?php
namespace Grout\Cyantree\UniversalConsoleModule\Types;

abstract class UniversalConsoleResponse
{
    public $redirectToCommand = null;
    public $redirectToDelay = 0;

    public function redirectTo($command, $delay = 0)
    {
        $this->redirectToCommand = $command;
        $this->redirectToDelay = $delay;
    }

    abstract public function showCommandLink($command, $title = null);

    abstract public function showSuccess($message);

    abstract public function showInfo($message);

    abstract public function showWarning($message);

    abstract public function showError($message);

    abstract public function showHeadline($text);
}
