<?php

namespace Tests\Feature;

use App\Models\Codigo_ref;
use App\Models\Curso;
use App\Models\PurchaseEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadPreCheckoutCaptureTest extends TestCase
{
    use RefreshDatabase;

    public function test_pre_checkout_lead_persists_buyer_email(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'dominio' => 'afiliado.test',
        ]);

        $curso = Curso::create([
            'titulo' => 'Curso Lead Email',
            'url' => 'curso-lead-email',
            'publicado' => true,
            'permitir_afiliacao' => true,
            'codigo_id_hotmart' => 'P12345678A',
            'codigo_afiliado_plano_completo' => 'abc123',
            'preco_cheio_completo' => 'R$197',
            'preco_parcelado_completo' => '12xR$19,70',
            'link_checkout_completo' => 'https://go.hotmart.com/T99999999A?ap=abc123',
            'link_checkout_basico' => 'https://go.hotmart.com/T99999999A?ap=abc123',
        ]);

        Codigo_ref::create([
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'codigo_ref' => 'REFLEADEMAIL',
            'mostrar_curso' => true,
        ]);

        $response = $this->post(route('lead_whatsapp'), [
            'nome' => 'Bruno Sampaio',
            'email' => 'bruno@example.com',
            'telefone' => '62999998888',
            'user_id' => $user->id,
            'curso_id' => $curso->id,
            'origem' => 'checkout_completo',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('purchase_events', [
            'buyer_name' => 'Bruno Sampaio',
            'buyer_email' => 'bruno@example.com',
            'buyer_checkout_phone' => '62999998888',
            'product_id' => 'P12345678A',
            'affiliate_code' => 'REFLEADEMAIL',
        ]);

        $purchaseEvent = PurchaseEvent::query()->where('buyer_checkout_phone', '62999998888')->first();
        $this->assertNotNull($purchaseEvent);
        $this->assertSame('bruno@example.com', $purchaseEvent->buyer_email);
    }
}
