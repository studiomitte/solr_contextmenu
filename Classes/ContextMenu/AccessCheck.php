<?php

declare(strict_types=1);

namespace StudioMitte\SolrContextmenu\ContextMenu;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AccessCheck
{
    public static function tableIsValid(string $tableName): bool
    {
        try {
            $settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('solr_contextmenu');
            $tables = GeneralUtility::trimExplode(',', $settings['tables'] ?? '', true);
            return in_array($tableName, $tables, true);
        } catch (\Exception $e) {
            // do nothing
        }

        return false;
    }
}
