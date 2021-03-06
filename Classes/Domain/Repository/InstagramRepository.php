<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Repository;

use In2code\Instagram\Domain\Service\FetchProfile;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class InstagramRepository
 */
class InstagramRepository
{
    /**
     * @var string
     */
    protected $cacheKey = 'instagram';

    /**
     * Default cache live time is 24h
     *
     * @var int
     */
    protected $cacheLifeTime = 86400;

    /**
     * @var FrontendInterface
     */
    protected $cacheInstance = null;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject = null;

    /**
     * InstagramRepository constructor.
     * @param ContentObjectRenderer $contentObject
     */
    public function __construct(ContentObjectRenderer $contentObject)
    {
        $this->contentObject = $contentObject;
        $this->cacheInstance = GeneralUtility::makeInstance(CacheManager::class)->getCache($this->cacheKey);
    }

    /**
     * @param string $profileId
     * @return array
     */
    public function findByProfileId(string $profileId): array
    {
        $configuration = $this->getConfigurationFromCache();
        if ($configuration === []) {
            $fetchProfile = GeneralUtility::makeInstance(FetchProfile::class);
            $configuration = $fetchProfile->fetch($profileId);
            $this->cacheConfiguration($configuration);
        }
        return $configuration;
    }

    /**
     * @param array $configuration
     * @return void
     */
    protected function cacheConfiguration(array $configuration): void
    {
        if ($configuration !== []) {
            $this->cacheInstance->set(
                $this->getCacheIdentifier(),
                $configuration,
                [$this->cacheKey],
                $this->cacheLifeTime
            );
        }
    }

    /**
     * @return array
     */
    protected function getConfigurationFromCache(): array
    {
        $configuration = [];
        $configurationCache = $this->cacheInstance->get($this->getCacheIdentifier());
        if (!empty($configurationCache)) {
            $configuration = $configurationCache;
        }
        return $configuration;
    }

    /**
     * @return string
     */
    protected function getCacheIdentifier(): string
    {
        return md5($this->contentObject->data['uid'] . $this->cacheKey);
    }
}
