<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // use createApplication;
    use RefreshDatabase;

    /**
     * Set up the test environment Dev.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup can be done here if needed
        DB::delete('delete from addresses');
        DB::delete('delete from contacts');
        DB::delete('delete from users');
    }
}
