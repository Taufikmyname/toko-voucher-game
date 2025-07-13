<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Midtrans\Snap;
use Mockery;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membersihkan mock setelah setiap tes.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Tes untuk memastikan pengguna dapat berhasil melakukan checkout.
     *
     * @return void
     */
    public function test_user_can_successfully_checkout_a_product()
    {
        // 1. Persiapan (Setup)
        // Membuat mock alias untuk kelas Midtrans\Snap.
        // Ini akan menggantikan kelas Snap yang asli selama tes ini berjalan.
        $mock = Mockery::mock('alias:' . Snap::class);
        $mock->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('mocked_snap_token_12345');

        // Membuat data yang diperlukan menggunakan factory.
        $user = User::factory()->create();
        $game = Game::factory()->create();
        $product = Product::factory()->create([
            'game_id' => $game->id,
            'price' => 77000,
        ]);

        // Data yang akan dikirim dalam request.
        $checkoutData = [
            'product_id' => $product->id,
            'game_user_id' => '123456789',
            'zone_id' => '9876',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
        ];

        // 2. Aksi (Action)
        // Mensimulasikan pengguna yang sudah login melakukan POST request ke rute checkout.
        $response = $this->actingAs($user)->post(route('checkout'), $checkoutData);

        // 3. Penegasan (Assertions)
        // Memastikan respons dari server adalah 200 OK.
        $response->assertStatus(200);

        // Memastikan respons JSON berisi snap_token yang sudah kita mock.
        $response->assertJson(['snap_token' => 'mocked_snap_token_12345']);

        // Memastikan sebuah record transaksi telah dibuat di dalam database
        // dengan data yang sesuai.
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'game_user_id' => '123456789',
            'customer_email' => 'test@example.com',
            'total_price' => 77000,
            'status' => 'pending',
        ]);
    }
}