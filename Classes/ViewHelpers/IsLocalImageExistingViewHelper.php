<?php
declare(strict_types=1);
namespace In2code\Instagram\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Class IsLocalImageExistingViewHelper
 */
class IsLocalImageExistingViewHelper extends AbstractConditionViewHelper
{
    /**
     * @var string
     */
    protected static $imageFolder = 'typo3temp/assets/tx_instagram/';

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('node', 'array', 'Array from instagram', false);
    }

    /**
     * @param null $arguments
     * @return bool
     * @throws \Exception
     */
    protected static function evaluateCondition($arguments = null): bool
    {
        $file = GeneralUtility::getFileAbsFileName(self::$imageFolder) . $arguments['node']['shortcode'] . '.jpg';
        return is_file($file);
    }
}
