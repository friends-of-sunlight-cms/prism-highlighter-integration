<?php

use Sunlight\Page\Page;
use Sunlight\WebState;

return function (array $args) {
    global $_index, $_page;

    $config = $this->getConfig();

    if (
        (
            $_index->type === WebState::PAGE
            && isset(Page::TYPES[$_page['type']])
            && isset($config['in_' . Page::TYPES[$_page['type']]])
            && $config['in_' . Page::TYPES[$_page['type']]]
        )
        || ($_index->type === WebState::PLUGIN && $config['in_plugin'])
        || ($_index->type === WebState::MODULE && $config['in_module'])
    ) {

        $mode = ($config['mode_advanced'] ? 'advanced' : 'basic');

        $args['css'][] = $this->getAssetPath('public/styles/prism-' . $mode . '.css');
        $args['js'][] = $this->getAssetPath('public/prism-' . $mode . '.js');

        // line-number plugin
        $args['css'][] = $this->getAssetPath('public/styles/prism-linenumber.css');
        $args['js'][] = $this->getAssetPath('public/prism-linenumber.js');
    }
};
