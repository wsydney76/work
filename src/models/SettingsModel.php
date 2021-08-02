<?php

namespace wsydney76\work\models;

use craft\base\Model;

class SettingsModel extends Model
{
    public $diffRendererName = 'Combined';
    public $diffContext = 1;
    public $diffIgnoreCase = false;
    public $diffIgnoreWhitespace = true;
    public $diffDetailLevel = 'word';

    public $diffLineNumbers = true;
    public $diffSeparateBlock = true;
    public $diffShowHeader = true;

    public $diffMergeThreshold = 0.8;
}
