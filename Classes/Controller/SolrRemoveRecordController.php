<?php

declare(strict_types=1);

namespace StudioMitte\SolrContextmenu\Controller;

use ApacheSolrForTypo3\Solr\ConnectionManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StudioMitte\SolrContextmenu\ContextMenu\AccessCheck;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class SolrRemoveRecordController
{

    public function mainAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        $recordUid = (int)($parsedBody['uid'] ?? $queryParams['uid'] ?? 0);
        $table = $parsedBody['table'] ?? $queryParams['table'] ?? '';
        $language = null;

        if (!AccessCheck::tableIsValid($table)) {
            return new JsonResponse([
                'success' => true,
                'title' => 'table invalid',
                'message' => ''
            ]);
        }

        $pageId = null;

        $connectionManager = GeneralUtility::makeInstance(ConnectionManager::class);
        $fullRecord = BackendUtility::getRecord($table, $recordUid);


        if (!$fullRecord) {
            return new JsonResponse([
                'success' => true,
                'title' => 'record not found',
                'message' => ''
            ]);
        }


        if ($table !== 'pages') {
            $pageId = $fullRecord['pid'];
            $pageRow = BackendUtility::getRecord('pages', $pageId);

            if ($GLOBALS['TCA'][$table]['ctrl']['languageField'] ?? false) {
                $language = $fullRecord[$GLOBALS['TCA'][$table]['ctrl']['languageField']] ?? 0;
            } else {
                $language = 0;
            }
        } else {
            $pageId = $recordUid;
            $pageRow = $fullRecord;
            $language = $fullRecord['sys_language_uid'] ?? 0;
        }

        if (!$this->checkAccess($table, $fullRecord, $pageRow)) {
            return new JsonResponse([
                'success' => false,
                'title' => 'Could not be cleared',
                'message' => ''
            ]);
        }

        if ($pageId) {
            $connection = $connectionManager->getConnectionByPageId($pageId, $language);
            $connection->getWriteService()->deleteByQuery(sprintf('type:%s AND uid:%s', $table, $recordUid));
        }

        return new JsonResponse([
            'success' => true,
            'title' => 'Cleared from solr',
            'message' => ''
        ]);
    }

    protected function checkAccess(string $table, array $record, array $page): bool
    {
        $user = $this->getBackendUserAuthentication();
        if ($user->isAdmin()) {
            return true;
        }
        if (!$user->recordEditAccessInternals($table, $record)) {
            return false;
        }
        if (!$user->check('tables_modify', $table)) {
            return false;
        }

        if (class_exists(Permission::class)) {
            $pagePermissions = new Permission($user->calcPerms($page));
            if (!$pagePermissions->isGranted(Permission::CONTENT_EDIT)) {
                return false;
            }
        } else {
            $pagePermissions = $user->calcPerms($page);
            if (!(($pagePermissions & Permission::CONTENT_EDIT) == Permission::CONTENT_EDIT)) {
                return false;
            }
        }
        return true;
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
