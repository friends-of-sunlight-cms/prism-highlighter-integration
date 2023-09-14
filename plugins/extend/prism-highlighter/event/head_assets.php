<?php

use Sunlight\Page\Page;
use Sunlight\WebState;

return function (array $args) {
    global $_index, $_page;

    if (
        (
            $_index->type === WebState::PAGE
            && isset(Page::TYPES[$_page['type']])
            && $this->getConfig()['in_' . Page::TYPES[$_page['type']]]
        )
        || ($_index->type === WebState::PLUGIN && $this->getConfig()['in_plugin'])
        || ($_index->type === WebState::MODULE && $this->getConfig()['in_module'])
    ) {

        $mode = ($this->getConfig()['mode_advanced'] ? 'advanced' : 'basic');

        $args['css'][] = $this->getAssetPath('public/styles/prism-' . $mode . '.css');
        $args['js'][] = $this->getAssetPath('public/prism-' . $mode . '.js');

        // line-number plugin
        $args['css'][] = $this->getAssetPath('public/styles/prism-linenumber.css');
        $args['js'][] = $this->getAssetPath('public/prism-linenumber.js');
    }
};
