<?php

namespace SunlightExtend\PrismHighlighter;

use Fosc\Feature\Plugin\Config\FieldGenerator;
use Sunlight\Action\ActionResult;
use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Plugin\Action\ConfigAction as BaseConfigAction;

class ConfigAction extends BaseConfigAction
{

    protected function execute(): ActionResult
    {
        // automatic increment cache (enforce reload css)
        if (!Core::$debug && (isset($_POST['save']) || isset($_POST['reset']))) {
            DB::update('setting', "var=" . DB::val('cacheid'), ['val' => DB::raw('val+1')]);
        }
        return parent::execute();
    }

    protected function getFields(): array
    {
        $langPrefix = "%p:prism.config";

        $gen = new FieldGenerator($this->plugin);
        $gen->generateFields([
            'mode_advanced',
            'in_section',
            'in_category',
            'in_book',
            'in_group',
            'in_forum',
            'in_plugin',
            'in_module',
        ], $langPrefix, '%checkbox');

        return $gen->getFields();
    }
}