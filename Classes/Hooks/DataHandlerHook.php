<?php
declare(strict_types=1);
namespace Brightside\Favicon\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class DataHandlerHook
{
    /**
     * @var int
     */
    private $minWidth = 600;

    /**
     * @var int
     */
    private $minHeight = 600;

    /**
     * @param DataHandler $dataHandler
     *
     * @throws ResourceDoesNotExistException
     */
    public function processDatamap_beforeStart(DataHandler $dataHandler): void
    {
        $datamap = $dataHandler->datamap;

        if (empty($datamap['sys_file_reference'])) {
            return;
        }

        foreach ($datamap['sys_file_reference'] as $uid => $incomingFieldArray) {
            if (MathUtility::canBeInterpretedAsInteger($uid)) {
                // $uid is already a valid integer and therefore belongs to an already
                // existing file relation - skip.
                continue;
            }

            $incomingFieldArray['uid'] = $uid;
            $pageId = MathUtility::convertToPositiveInteger($incomingFieldArray['pid']);

            $favicon = $datamap['pages'][$pageId]['tx_favicon_favicon'];

            if (strpos((string)$favicon, $incomingFieldArray['uid']) === false) {
                // Field `pages.tx_favicon_favicon` does not contain the given uid, therefore it's not
                // a file relation we care for - skip.
                continue;
            }

            $parts = GeneralUtility::revExplode('_', $incomingFieldArray['uid_local'], 2);
            $fileUid = $parts[count($parts) - 1];

            $file = ResourceFactory::getInstance()->getFileObject($fileUid);

            if (!$this->isAllowed($file)) {
                // Unset file from `sys_file_references` map
                unset($dataHandler->datamap['sys_file_reference'][$incomingFieldArray['uid']]);

                // Clean `pages.tx_favicon_favicon` which will still contain the NEW... id at this point
                $favicon = GeneralUtility::intExplode(',', $datamap['pages'][$pageId]['tx_favicon_favicon']);
                $sanitized = array_filter(array_values($favicon), static function ($value) {
                    return $value > 0;
                });
                $datamap['pages'][$pageId]['tx_favicon_favicon'] = implode(',', $sanitized);

                // Compile and display error message
                $label = $this->getLanguageService()->sL('LLL:EXT:favicon/Resources/Private/Language/locallang_db.xlf:' . 'error.file_not_saved');
                $message = vsprintf(
                    $label,
                    [
                        $file->getName(),
                        $this->minWidth,
                        $this->minHeight,
                    ]
                );

                $dataHandler->log(
                    'sys_file_reference',
                    $incomingFieldArray['uid'],
                    1,
                    0,
                    1,
                    $message,
                    0
                );
            }
        }
    }

    /**
     * Checks whether a file is allowed according to the criteria
     * defined in the class variables ($this->minWidth, $this->minHeight etc.).
     *
     * @param File $file
     *
     * @return bool
     */
    private function isAllowed($file): bool
    {
        $fileProperties = $file->getProperties();
        $result = true;

        // Check required minimum width
        if (!empty($this->minWidth)
            && !empty($fileProperties['width'])
            && ($this->minWidth > $fileProperties['width'])
        ) {
            $result = false;
        }

        // Check required minimum height
        if (!empty($this->minHeight)
            && !empty($fileProperties['height'])
            && ($this->minHeight > $fileProperties['height'])
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Returns LanguageService
     *
     * @return LanguageService
     */
    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
