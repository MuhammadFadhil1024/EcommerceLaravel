<?php

namespace Tests\Feature; // 1. Ubah ke namespace Feature

use Illuminate\Foundation\Testing\RefreshDatabase; // 2. Import RefreshDatabase
use Tests\TestCase; // 3. Pastikan menggunakan TestCase bawaan Laravel, bukan bawaan murni PHPUnit

class ExampleTest extends TestCase
{
    // 4. Tambahkan trait ini di dalam class
    use RefreshDatabase; 

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        // Asumsi URL halaman home Anda adalah '/'
        $response = $this->get('/'); // 5. Sesuaikan URL dengan route yang benar

        $response->assertStatus(200);
    }
}