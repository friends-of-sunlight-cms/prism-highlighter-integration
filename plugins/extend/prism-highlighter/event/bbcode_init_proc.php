<?php

return function (array $args) {
    $args['tags']['code'] = function ($argument, $buffer) {
        if ($buffer !== '') {
            $lang = "language-" . ($argument !== "" ? _e(mb_strtolower(trim($argument))) : "markup");
            return "<pre class='" . $lang . " line-numbers'><code class='" . $lang . "'>" . $buffer . "</code></pre>";
        }
        return '';
    };
};
