# Work Plugin

__This plugin has the status of a 'Proof of Concept'__ 

It is not intended for use in production.

* Shows info about conflicting drafts/provisional drafts
* Lets you compare drafts/provisional drafts with the current version
* Adds a 'My Open Edits' dashboard widget showing your provisional drafts
* Adds an 'Edited' element index column showing provisional drafts
* Allows other users provisional drafts to your account (requires special permission)

## Installation

Add this to `composer.json`:

```
"minimum-stability": "dev",
"prefer-stable": true,
"repositories": [
{
"type": "git",
"url": "https://github.com/wsydney76/work"
}
]
```

Run `composer require wsydney76/work`

Run `php craft plugin/install work`
