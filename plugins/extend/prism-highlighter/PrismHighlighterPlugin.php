<?php

namespace SunlightExtend\PrismHighlighter;

use Sunlight\Action\ActionResult;
use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Page\Page;
use Sunlight\Plugin\Action\ConfigAction;
use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Plugin\ExtendPlugin;
use Sunlight\Util\Form;
use Sunlight\WebState;

class PrismHighlighterPlugin extends ExtendPlugin
{

    /** @var array */
    private $types = [
        Page::SECTION => 'section',
        Page::CATEGORY => 'category',
        Page::BOOK => 'book',
        Page::GROUP => 'group',
        Page::FORUM => 'forum',
        Page::PLUGIN => 'plugin',
    ];



    public function onHead(array $args): void
    {
        global $_index, $_page;

        if (
            $_index->type === WebState::PAGE
            && isset($this->types[$_page['type']])
            && $this->getConfig()->offsetGet('in_' . $this->types[$_page['type']]
            )
            || ($_index->type === WebState::PLUGIN && $this->getConfig()->offsetGet('in_plugin'))
            || ($_index->type === WebState::MODULE && $this->getConfig()->offsetGet('in_module'))
        ) {

            $mode = ($this->getConfig()->offsetGet('mode_advanced') ? 'advanced' : 'basic');

            $args['css'][] = $this->getWebPath() . '/resources/styles/prism-' . $mode . '.css';
            $args['js'][] = $this->getWebPath() . '/resources/prism-' . $mode . '.js';

            // line-number plugin
            $args['css'][] = $this->getWebPath() . '/resources/styles/prism-linenumber.css';
            $args['js'][] = $this->getWebPath() . '/resources/prism-linenumber.js';

        }
    }

    public function onBbCode($args): void
    {
        $args['tags']['code'] = function ($argument, $buffer) {
            if ($buffer !== '') {
                $lang = "language-" . ($argument !== "" ? _e(mb_strtolower(trim($argument))) : "markup");
                return "<pre class='" . $lang . " line-numbers'><code class='" . $lang . "'>" . $buffer . "</code></pre>";
            }
        };
    }

    /**
     * ============================================================================
     *  EXTEND CONFIGURATION
     * ============================================================================
     */

    protected function getConfigDefaults(): array
    {
        return [
            'mode_advanced' => false,
            // stranky
            'in_section' => false,
            'in_category' => false,
            'in_book' => false,
            'in_group' => false,
            'in_forum' => true,
            'in_plugin' => false,
            'in_module' => false,

        ];
    }

    public function getAction(string $name): ?PluginAction
    {
        if ($name === 'config') {
            return new CustomConfig($this);
        }
        return parent::getAction($name);
    }
}

class CustomConfig extends ConfigAction
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
        $cfg = $this->plugin->getConfig();

        return [
            'mode_advanced' => [
                'label' => _lang('prism.mode_advanced'),
                'input' => '<input type="checkbox" name="config[mode_advanced]" value="1"' . Form::activateCheckbox($cfg->offsetGet('mode_advanced')) . '>',
                'type' => 'checkbox'
            ],
            'in_section' => [
                'label' => _lang('prism.in_section'),
                'input' => '<input type="checkbox" name="config[in_section]" value="1"' . Form::activateCheckbox($cfg->offsetGet('in_section')) . '>',
                'type' => 'checkbox'
            ],
            'in_category' => [
                'label' => _lang('prism.in_category'),
                'input' => '<input type="checkbox" name="config[in_category]" value="1"' . Form::activateCheckbox($cfg->offsetGet('in_category')) . '>',
                'type' => 'checkbox'
            ],
            'in_book' => [
                'label' => _lang('prism.in_book'),
                'input' => '<input type="checkbox" name="config[in_book]" value="1"' . Form::activateCheckbox($cfg->offsetGet('in_book')) . '>',
                'type' => 'checkbox'
            ],
            'in_group' => [
                'label' => _lang('prism.in_group'),
                'input' => '<input type="checkbox" name="config[in_group]" value="1"' . Form::activateCheckbox($cfg->offsetGet('in_group')) . '>',
                'type' => 'checkbox'
            ],
            'in_forum' => [
                'label' => _lang('prism.in_forum'),
                'input' => '<input type="checkbox" name="config[in_forum]" value="1"' . Form::activateCheckbox($cfg->offsetGet('in_forum')) . '>',
                'type' => 'checkbox'
            ],
            'in_plugin' => [
                'label' => _lang('prism.in_plugin'),
                'input' => '<input type="checkbox" name="config[in_plugin]" value="1"' . Form::activateCheckbox($cfg->offsetGet('in_plugin')) . '>',
                'type' => 'checkbox'
            ],
            'in_module' => [
                'label' => _lang('prism.in_module'),
                'input' => '<input type="checkbox" name="config[in_module]" value="1"' . Form::activateCheckbox($cfg->offsetGet('in_module')) . '>',
                'type' => 'checkbox'
            ],
        ];
    }
}
