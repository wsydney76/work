<?php

// Copy this file to your config folder as work.php

return [
    // renderer class name:
    //     Text renderers: Context, JsonText, Unified
    //     HTML renderers: Combined, Inline, JsonHtml, SideBySide
    // 'diffRendererName' => 'Combined',

    // show how many neighbor lines
    // Differ::CONTEXT_ALL can be used to show the whole file
    // 'diffContext' => 1 ,

    // ignore case difference
    // 'diffIgnoreCase' => false,

    // ignore whitespace difference
    // 'diffIgnoreWhitespace' => true,

    // how detailed the rendered HTML in-line diff is? (none, line, word, char)
    // 'diffDetailLevel' => 'word',

    // show line numbers in HTML renderers
    // 'diffLineNumbers' => true

    // show a separator between different diff hunks in HTML renderers
    // 'diffSeparateBlock' => true,

    // show the (table) header
    // 'diffShowHeader' => true,

    // this option is currently only for the Combined renderer.
    // it determines whether a replace-type block should be merged or not
    // depending on the content changed ratio, which values between 0 and 1.
    // 'diffMergeThreshold' => 0.8,
];
