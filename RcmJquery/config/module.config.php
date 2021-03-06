<?php
/**
 * ZF2 Module Config file for Rcm
 *
 * This file contains all the configuration for the Module as defined by ZF2.
 * See the docs for ZF2 for more information.
 *
 * PHP version 5.3
 *
 * LICENSE: No License yet
 *
 * @category  Reliv
 * @author    Rod Mcnew <rmcnew@relivinc.com>
 * @copyright 2012 Reliv International
 * @license   License.txt New BSD License
 * @version   GIT: <git_id>
 */

return [
    'asset_manager' => [
        'resolver_configs' => [
            'aliases' => [
                'modules/rcm-jquery/' => __DIR__ . '/../public/',
            ],
            'collections' => [
                'modules/rcm-jquery/all.js' => [
                    'modules/rcm-jquery/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js',
                    'modules/rcm-jquery/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js',
                    'modules/rcm-jquery/jquery-block-ui/jquery-block-ui.js',
                    'modules/rcm-jquery/jquery-context-menu/jquery.contextMenu.js',
                    'modules/rcm-jquery/jquery-context-menu/jquery.ui.position.js',
                    'modules/rcm-jquery/cropper/dist/cropper.min.js',
                    'modules/rcm-jquery/jquery.form/jquery.form.js',
                    'modules/rcm-jquery/notifyjs-0.3.2/dist/notify.min.js',
                    'modules/rcm-jquery/notifyjs-0.3.2/dist/styles/bootstrap/notify-bootstrap.js',
                    'modules/rcm-jquery/jquery-taghandler/js/jquery.taghandler.min.js',
                ],
                'modules/rcm-jquery/all.css' => [
                    'modules/rcm-jquery/jquery-ui-1.10.4.custom/css/smoothness/jquery-ui-1.10.4.custom.min.css',
                    'modules/rcm-jquery/jquery-context-menu/jquery.contextMenu.css',
                    'modules/rcm-jquery/cropper/dist/cropper.min.css',
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
