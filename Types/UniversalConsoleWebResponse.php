<?php
namespace Grout\Cyantree\UniversalConsoleModule\Types;

class UniversalConsoleWebResponse extends UniversalConsoleResponse
{
    public $messages = array();

    public function showCommandLink($command, $title = null)
    {
        $this->messages[] = array(
            'type' => 'command',
            'command' => $command,
            'title' => $title ? $title : $command
        );
    }

    public function showSuccess($message)
    {
        $this->messages[] = array(
                'type' => 'success',
                'message' => strval($message)
        );
    }

    public function showInfo($message)
    {
        $this->messages[] = array(
                'type' => 'info',
                'message' => strval($message)
        );
    }

    public function showWarning($message)
    {
        $this->messages[] = array(
                'type' => 'warning',
                'message' => strval($message)
        );
    }

    public function showError($message)
    {
        $this->messages[] = array(
                'type' => 'error',
                'message' => strval($message)
        );
    }

    public function showHeadline($text)
    {
        $this->messages[] = array(
            'type' => 'headline',
            'message' => $text
        );
    }
}
