<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyLoginRedirectTest extends TestCase
{
    public function test_legacy_root_domain_login_redirects_to_portal_login_preserving_query_string(): void
    {
        $response = $this->get('http://jovemempreendedor.org/login?src=legacy');

        $response->assertRedirect('https://portalje.org/login?src=legacy');
    }
}
