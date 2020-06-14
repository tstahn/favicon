<?php
defined('TYPO3_MODE') || die();

call_user_func(
    function () {
        $rootlineFields = &$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'];

        if ($rootlineFields !== '') {
            $rootlineFields .= ',';
        }

        $rootlineFields .= 'tx_favicon_favicon';
    }
);
