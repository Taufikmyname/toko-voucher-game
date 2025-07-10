<x-mail::message>
# Kode Voucher Anda Telah Tiba!

Halo **{{ $transaction->user->name ?? 'Pelanggan' }}**,

Terima kasih telah melakukan pembelian di **{{ config('app.name') }}**.
Pembayaran untuk pesanan **{{ $transaction->order_id }}** telah berhasil kami terima.

Berikut adalah rincian pesanan dan kode voucher Anda:

- **Produk:** {{ $transaction->product->name }} ({{ $transaction->product->game->name }})
- **User ID Game:** {{ $transaction->game_user_id }}
- **Kode Voucher Anda:**
<x-mail::panel>
# {{ $transaction->voucher_code }}
</x-mail::panel>

Silakan gunakan kode voucher di atas. Jika Anda mengalami kendala, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
