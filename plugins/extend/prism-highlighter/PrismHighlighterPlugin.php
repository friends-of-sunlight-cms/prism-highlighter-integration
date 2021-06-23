<?php

namespace SunlightExtend\PrismHighlighter;

use Sunlight\Action\ActionResult;
use Sunlight\Database\Database as DB;
use Sunlight\Plugin\Action\ConfigAction;
use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Plugin\ExtendPlugin;
use Sunlight\Util\Form;

/**
 * PrismHighlighter plugin
 *
 * @author Jirka Daněk <jdanek.eu>
 */
class PrismHighlighterPlugin extends ExtendPlugin
{

    /** @var array */
    private $types = [
        _page_section => 'section',
        _page_category => 'category',
        _page_book => 'book',
        _page_group => 'group',
        _page_forum => 'forum',
        _page_plugin => 'plugin',
    ];

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

    /**
     * @param array $args
     */
    public function onHead(array $args): void
    {
        global $_index, $_page;

        if (
            $_index['type'] === _index_page
            && isset($this->types[$_page['type']])
            && $this->getConfig()->offsetGet('in_' . $this->types[$_page['type']]
            )
            || ($_index['type'] === _index_plugin && $this->getConfig()->offsetGet('in_plugin'))
            || ($_index['type'] === _index_module && $this->getConfig()->offsetGet('in_module'))
        ) {

            $mode = ($this->getConfig()->offsetGet('mode_advanced') ? 'advanced' : 'basic');

            $args['css'][] = $this->getWebPath() . '/Resources/styles/prism-' . $mode . '.css';
            $args['js'][] = $this->getWebPath() . '/Resources/prism-' . $mode . '.js';

            // line-number plugin
            $args['css'][] = $this->getWebPath() . '/Resources/styles/prism-linenumber.css';
            $args['js'][] = $this->getWebPath() . '/Resources/prism-linenumber.js';

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

    public function getAction(string $name): PluginAction
    {
        if ($name == 'config') {
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
        if (!_debug && (isset($_POST['save']) || isset($_POST['reset']))) {
            DB::update(_setting_table, "var=" . DB::val('cacheid'), ['val' => DB::raw('val+1')]);
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
