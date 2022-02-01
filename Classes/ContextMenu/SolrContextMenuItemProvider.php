<?php

declare(strict_types=1);

namespace StudioMitte\SolrContextmenu\ContextMenu;

use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;

class SolrContextMenuItemProvider extends AbstractProvider
{

    /**
     * This array contains configuration for items you want to add
     * @var array
     */
    protected $itemsConfiguration = [
        'hello' => [
            'type' => 'item',
            'label' => 'LLL:EXT:solr_contextmenu/Resources/Private/Language/locallang.xlf:contextmenu.remove',
            'iconIdentifier' => 'actions-edit-delete',
            'callbackAction' => 'removeFromSolr'
        ]
    ];

    public function addItems(array $items): array
    {
        $this->initDisabledItems();

        //passes array of items to the next item provider
        $items += $this->prepareItems($this->itemsConfiguration);
        return $items;
    }

    public function getPriority(): int
    {
        return 19;
    }

    public function canHandle(): bool
    {
        return AccessCheck::tableIsValid($this->table);
    }

    protected function getAdditionalAttributes(string $itemName): array
    {
        return [
            'data-callback-module' => 'TYPO3/CMS/SolrContextmenu/ContextMenuActions',
        ];
    }
}
