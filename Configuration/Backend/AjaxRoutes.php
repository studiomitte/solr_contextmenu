<?php

return [
    'web_list_solrrecordclear' => [
        'path' => '/web/list/solrrecordclear',
        'target' => \StudioMitte\SolrContextmenu\Controller\SolrRemoveRecordController::class . '::mainAction'
    ],
];
