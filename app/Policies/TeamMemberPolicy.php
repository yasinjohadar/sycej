<?php

namespace App\Policies;

/**
 * No owner column — permission alone decides.
 */
class TeamMemberPolicy extends BasePolicy
{
    protected string $resource = 'team-members';
}
