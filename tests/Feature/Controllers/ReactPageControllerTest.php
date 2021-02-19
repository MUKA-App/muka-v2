<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class ReactPageControllerTest extends TestCase
{
    /**
     * @test
     *
     * Assert view controller returns app view
	 */
    public function page_controller_returns_view_when_authenticated()
    {
        $user = User::factory()->make();
        $this->be($user);
        $request = $this->get('/');
        $request->assertViewIs('app');
    }
}
