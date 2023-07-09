<?php

namespace SunlightExtend\PrismHighlighter;

use Sunlight\Page\Page;
use Sunlight\Plugin\ExtendPlugin;
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

            $args['css'][] = $this->getAssetPath('public/styles/prism-' . $mode . '.css');
            $args['js'][] = $this->getAssetPath('public/prism-' . $mode . '.js');

            // line-number plugin
            $args['css'][] = $this->getAssetPath('public/styles/prism-linenumber.css');
            $args['js'][] = $this->getAssetPath('public/prism-linenumber.js');

        }
    }

    public function onBbCode($args): void
    {
        $args['tags']['code'] = function ($argument, $buffer) {
            if ($buffer !== '') {
                $lang = "language-" . ($argument !== "" ? _e(mb_strtolower(trim($argument))) : "markup");
                return "<pre class='" . $lang . " line-numbers'><code class='" . $lang . "'>" . $buffer . "</code></pre>";
            }
            return '';
        };
    }
}


