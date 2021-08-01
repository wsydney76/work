<?php

namespace wsydney76\work;

use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\events\DefineBehaviorsEvent;
use craft\events\DefineHtmlEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementTableAttributesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\events\SetElementTableAttributeHtmlEvent;
use craft\i18n\PhpMessageSource;
use craft\services\Dashboard;
use craft\services\UserPermissions;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use wsydney76\work\behaviors\WorkEntryBehavior;
use wsydney76\work\behaviors\WorkMatrixBehavior;
use wsydney76\work\services\WorkService;
use wsydney76\work\widgets\MyProvisionsalDraftsWidget;
use yii\base\Event;
use const DIRECTORY_SEPARATOR;

class Work extends Plugin
{
    public function init()
    {
        Craft::setAlias('@work', $this->getBasePath());

        // Set the controllerNamespace based on whether this is a console or web request
        $this->controllerNamespace = Craft::$app->request->isConsoleRequest ?
            'wsydney76\\work\\console\\controllers' :
            'wsydney76\\work\\controllers';

        parent::init();

        if (!Craft::$app->request->isCpRequest) {
            return;
        }

        // Set template root for cp requests
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event) {
            $event->roots['customwork'] = Craft::parseEnv('@templates') . DIRECTORY_SEPARATOR . '_work';
        }
        );

        // Inject template into entries edit screen
        $user = Craft::$app->user->identity;
        if ($user) {
            Craft::$app->view->hook('cp.entries.edit.meta', function(array $context) {
                $entry = $context['entry'];
                if ($entry === null) {
                    return '';
                }
                return Craft::$app->view->renderTemplate(
                    'work/draft_hints',
                    ['entry' => $entry]);
            });
        }

        // Register Behavior
        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors[] = WorkEntryBehavior::class;
        });
        Event::on(
            MatrixBlock::class,
            MatrixBlock::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors[] = WorkMatrixBehavior::class;
        });
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('work', WorkService::class);
        }
        );

        // Create Permissions
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions['Work Plugin'] = [
                'viewpeerprovisionaldrafts' => [
                    'label' => Craft::t('work', 'View provisional drafts of other users')
                ],
                'transferprovisionaldrafts' => [
                    'label' => Craft::t('work', 'Transfer other users provisional draft to own account')
                ]
            ];
        }
        );

        // Register Widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = MyProvisionsalDraftsWidget::class;
        });

        // Register element index column
        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_TABLE_ATTRIBUTES, function(RegisterElementTableAttributesEvent $event) {
            $event->tableAttributes['hasProvisionalDraft'] = ['label' => Craft::t('work', 'Edited')];
        }
        );
        Event::on(
            Entry::class,
            Element::EVENT_SET_TABLE_ATTRIBUTE_HTML, function(SetElementTableAttributeHtmlEvent $event) {

            if ($event->attribute == 'hasProvisionalDraft') {
                $event->handled = true;
                /** @var Entry $entry */
                $entry = $event->sender;
                $event->html = '';

                $query = Entry::find()
                    ->draftOf($entry)
                    ->provisionalDrafts(true)
                    ->site($entry->site)
                    ->anyStatus();

                $countProvisionalDrafts = $query->count();

                $query->draftCreator(Craft::$app->user->identity);
                $hasOwnProvisionalDraft = $query->exists();

                if ($hasOwnProvisionalDraft) {
                    $event->html .= '<span class="status active"></span>';
                }

                if (Craft::$app->user->identity->can('viewpeerprovisionaldrafts')) {
                    // Workaround because there is no ->draftCreator('not ...)
                    if ($hasOwnProvisionalDraft) {
                        --$countProvisionalDrafts;
                    }
                    if ($countProvisionalDrafts) {
                        $event->html .= '<span class="status"></span>';
                    }
                }
            }
        });

        // Add hint in entry slideouts
        Event::on(
            Element::class,
            Element::EVENT_DEFINE_SIDEBAR_HTML, function(DefineHtmlEvent $event) {
            if ($event->sender instanceof Entry) {
                $event->html = Craft::$app->view->renderTemplate('work/entry_hasdrafts.twig', [
                        'entry' => $event->sender
                    ]) . $event->html;
            }
        }
        );
    }
}
