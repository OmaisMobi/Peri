<?php

namespace App\Models;
use Laravelcm\Subscriptions\Models\Subscription as BaseSubscription;

class Subscription extends BaseSubscription
{
    /**
     * Check if the subscription has a feature.
     *
     * @param string $feature
     * @return bool
     */
    public function hasFeature(string $featureSlug): bool
    {
        $featureValue = $this->getFeatureValue($featureSlug);

        if ($featureValue === 'true') {
            return true;
        }

        // If the feature value is zero, let's return false since
        // there's no uses available. (useful to disable countable features)
        if ($featureValue === null || $featureValue === '0' || $featureValue === 'false') {
            return false;
        }

        // Check for available uses
        return true;
    }
}
