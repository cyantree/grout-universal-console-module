<?php
namespace Grout\Cyantree\UniversalConsoleModule;

use Cyantree\Grout\App\App;
use Cyantree\Grout\App\GroutFactory;
use Cyantree\Grout\Tools\StringTools;
use Grout\AppModule\AppFactory;
use Grout\Cyantree\UniversalConsoleModule\Types\UniversalConsoleConfig;

// TODO: Remove dependency of AppFactory
class UniversalConsoleFactory extends AppFactory
{
    /** @var UniversalConsoleModule */
    public $module;

    /** @return UniversalConsoleFactory */
    public static function get(App $app = null, $moduleId = null)
    {
        /** @var UniversalConsoleFactory $factory */
        $factory = GroutFactory::getFactory($app, __CLASS__, $moduleId, 'Cyantree\UniversalConsoleModule');

        return $factory;
    }

    public function config()
    {
        if (!($tool = $this->retrieveTool(__FUNCTION__))) {
            /** @var UniversalConsoleConfig $tool */
            $tool = $this->app->configs->getConfig($this->module->id);

            $this->setTool(__FUNCTION__, $tool);
        }

        return $tool;
    }

    public function generateExecutionKey()
    {
        return StringTools::md5($this->app->getConfig()->internalAccessKey, '', 375);
    }
}
