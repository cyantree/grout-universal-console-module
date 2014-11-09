<?php
namespace Grout\Cyantree\UniversalConsoleModule\Types;

use Cyantree\Grout\Filter\ArrayFilter;
use Cyantree\Grout\Tools\ServerTools;
use Grout\Cyantree\UniversalConsoleModule\UniversalConsoleTools;

class UniversalConsoleRequest
{
    public $fullCommand;

    public $command;

    /** @var ArrayFilter */
    public $args;

    public $isWebRequest;

    public function __construct()
    {
        $this->args = new ArrayFilter();
    }

    public static function createFromString($command)
    {
        if ($command == '') {
            return null;
        }

        $request = new UniversalConsoleRequest();

        $args = ServerTools::parseCommandlineString($command);

        $request->command = $args[0];

        $get = array();
        if (count($args) > 1) {
            $args = array_splice($args, 1);
            foreach ($args as $arg) {
                if (substr($arg, 0, 2) == '--') {
                    $get[substr($arg, 2)] = true;

                } elseif (substr($arg, 0, 1) == '-') {
                    $s = explode('=', $arg, 2);

                    $get[substr($s[0], 1)] = count($s) == 2 ? $s[1] : '';

                } else {
                    $get[] = $arg;
                }
            }
        }

        $request->args = new ArrayFilter($get);
        $request->fullCommand = UniversalConsoleTools::constructCommandString($request->command, $request->args);

        return $request;
    }

    public function toNewCommandString($mergeArgs)
    {
        return UniversalConsoleTools::constructCommandString($this->command, array_merge($this->args->getData(), $mergeArgs));
    }
}
