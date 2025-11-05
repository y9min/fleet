<?php

namespace Tests\Unit;

use Tests\TestCase;

class StripeBackfillCommandTest extends TestCase
{
    public function test_command_is_registered()
    {
        $this->artisan('list')->expectsOutputToContain('stripe:backfill-subscriptions');
        $this->assertTrue(true);
    }
}


