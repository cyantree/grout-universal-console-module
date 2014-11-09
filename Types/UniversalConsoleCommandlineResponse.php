<?php
namespace Grout\Cyantree\UniversalConsoleModule\Types;

class UniversalConsoleCommandlineResponse extends UniversalConsoleResponse
{
    public function showCommandLink($command, $title = null)
    {
        $c = '[LNK] ';

        if ($title) {
            $c .= $title . ': ';
        }

        echo $c . $command . PHP_EOL;
    }

    public function showSuccess($message)
    {
        echo '[SUC] ' . $message . PHP_EOL;
    }

    public function showInfo($message)
    {
        echo '[INF] ' . $message . PHP_EOL;
    }

    public function showWarning($message)
    {
        echo '[WAR] ' . $message . PHP_EOL;
    }

    public function showError($message)
    {
        echo '[ERR] ' . $message . PHP_EOL;
    }

    public function showHeadline($text)
    {
        echo $text . PHP_EOL
                . str_repeat('-', mb_strlen($text)) . PHP_EOL;
    }
}
