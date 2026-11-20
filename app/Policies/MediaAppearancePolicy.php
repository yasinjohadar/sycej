<?php

namespace App\Policies;

/**
 * No owner column — permission alone decides.
 */
class MediaAppearancePolicy extends BasePolicy
{
    protected string $resource = 'media-appearances';
}
