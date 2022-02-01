<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Solr Enhancements',
    'description' => 'Remove documents from solr via backend context menu',
    'category' => 'backend',
    'author' => 'Georg Ringer',
    'author_email' => 'gr@studiomitte.com',
    'author_company' => 'Studio Mitte',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '9.5.0-11.5.99',
                    'solr' => '10.0.0-11.99.99'
                ],
        ],
    'autoload' =>
        [
            'psr-4' =>
                [
                    'StudioMitte\\SolrContextmenu\\' => 'Classes',
                ],
        ]
];
