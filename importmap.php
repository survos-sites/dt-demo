<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    'datatables.net-bs5' => [
        'version' => '2.1.3',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'datatables.net' => [
        'version' => '2.1.3',
    ],
    'datatables.net-bs5/css/dataTables.bootstrap5.min.css' => [
        'version' => '2.1.3',
        'type' => 'css',
    ],
    'axios' => [
        'version' => '1.7.7',
    ],
    'datatables.net-searchpanes-bs5' => [
        'version' => '2.3.0',
    ],
    'datatables.net-searchpanes-bs5/css/searchPanes.bootstrap5.min.css' => [
        'version' => '2.3.0',
        'type' => 'css',
    ],
    'html-prettify' => [
        'version' => '1.0.7',
    ],
    'datatables.net-dt' => [
        'version' => '2.1.3',
    ],
    'simple-datatables' => [
        'version' => '9.0.4',
    ],
    'simple-datatables/dist/style.min.css' => [
        'version' => '9.0.4',
        'type' => 'css',
    ],
    'twig' => [
        'version' => '1.17.1',
    ],
    'locutus/php/strings/sprintf' => [
        'version' => '2.0.32',
    ],
    'locutus/php/strings/vsprintf' => [
        'version' => '2.0.32',
    ],
    'locutus/php/math/round' => [
        'version' => '2.0.32',
    ],
    'locutus/php/math/max' => [
        'version' => '2.0.32',
    ],
    'locutus/php/math/min' => [
        'version' => '2.0.32',
    ],
    'locutus/php/strings/strip_tags' => [
        'version' => '2.0.32',
    ],
    'locutus/php/datetime/strtotime' => [
        'version' => '2.0.32',
    ],
    'locutus/php/datetime/date' => [
        'version' => '2.0.32',
    ],
    'locutus/php/var/boolval' => [
        'version' => '2.0.32',
    ],
    'datatables.net-responsive' => [
        'version' => '3.0.2',
    ],
    'datatables.net-select-bs5' => [
        'version' => '2.0.4',
    ],
    'datatables.net-select-bs5/css/select.bootstrap5.min.css' => [
        'version' => '2.0.4',
        'type' => 'css',
    ],
    'fos-routing' => [
        'version' => '0.0.6',
    ],
    '@fortawesome/fontawesome-free' => [
        'version' => '6.6.0',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '6.6.0',
        'type' => 'css',
    ],
    '@fortawesome/free-solid-svg-icons' => [
        'version' => '6.6.0',
    ],
    '@fortawesome/fontawesome-svg-core' => [
        'version' => '6.6.0',
    ],
    '@fortawesome/fontawesome-svg-core/styles.min.css' => [
        'version' => '6.6.0',
        'type' => 'css',
    ],
    'bootswatch/dist/cerulean/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    'bootswatch/dist/sandstone/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    'bootswatch/dist/materia/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    'datatables.net-plugins/i18n/en-GB.mjs' => [
        'version' => '2.0.8',
    ],
    'datatables.net-buttons-bs5' => [
        'version' => '3.1.1',
    ],
    'datatables.net-buttons' => [
        'version' => '3.1.1',
    ],
    'datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css' => [
        'version' => '3.1.1',
        'type' => 'css',
    ],
    'datatables.net-responsive-bs5' => [
        'version' => '3.0.2',
    ],
    'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css' => [
        'version' => '3.0.2',
        'type' => 'css',
    ],
    'datatables.net-scroller-bs5' => [
        'version' => '2.4.3',
    ],
    'datatables.net-scroller' => [
        'version' => '2.4.3',
    ],
    'datatables.net-scroller-bs5/css/scroller.bootstrap5.min.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    'datatables.net-select' => [
        'version' => '2.0.4',
    ],
    'datatables.net-searchpanes' => [
        'version' => '2.3.0',
    ],
    'perfect-scrollbar' => [
        'version' => '1.5.5',
    ],
    'perfect-scrollbar/css/perfect-scrollbar.min.css' => [
        'version' => '1.5.5',
        'type' => 'css',
    ],
    'datatables.net-searchbuilder-bs5' => [
        'version' => '1.7.1',
    ],
    'datatables.net-searchbuilder' => [
        'version' => '1.7.1',
    ],
    'datatables.net-searchbuilder-bs5/css/searchBuilder.bootstrap5.min.css' => [
        'version' => '1.7.1',
        'type' => 'css',
    ],
    'datatables.net-dt/css/dataTables.dataTables.min.css' => [
        'version' => '2.1.3',
        'type' => 'css',
    ],
    'bootstrap/js/dist/modal' => [
        'version' => '5.3.3',
    ],
    'imposterjs' => [
        'version' => '1.0.9',
    ],
    'dexie' => [
        'version' => '4.0.8',
    ],
    'datatables.net-plugins/i18n/es-ES.mjs' => [
        'version' => '2.1.7',
    ],
    'datatables.net-plugins/i18n/de-DE.mjs' => [
        'version' => '2.1.7',
    ],
    '@tabler/core' => [
        'version' => '1.0.0-beta21',
    ],
    '@tabler/core/dist/css/tabler.min.css' => [
        'version' => '1.0.0-beta21',
        'type' => 'css',
    ],
];
