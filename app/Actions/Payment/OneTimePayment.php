<?php 

namespace App\Actions\Payment;

use App\Services\Xendit\XenditServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

Class OneTimePayment implements XenditServiceInterface {

    public function sessionPayment(array $dataPayment)
    {

        $referenceId = 'CUSTOMER' . Str::upper(Str::random(10));

        $host = config('app.xendit_return_host', 'https://absentee-gladiator-appealing.ngrok-free.dev');
        $successUrl = $host . '/payment/success?ref=' . $referenceId;
        $cancelUrl = $host . '/payment/failure?ref=' . $referenceId;

        $payload = [
            'reference_id' => $referenceId,
            'session_type' => 'PAY',
            'mode' => 'PAYMENT_LINK',
            'amount' => (int) $dataPayment['total_payment'],
            'currency' => 'IDR',
            'country' => 'ID',
            'customer' => [
                'type' => 'INDIVIDUAL',
                'reference_id' => 'USER' . Auth::id() . '_' . $referenceId,
                'email' => Auth::user()->email,
                'individual_detail' => [
                    'given_names' => preg_replace('/[^a-zA-Z0-9]/', '', Auth::user()->name),
                ]
            ],
            'success_return_url' => $successUrl,
            'cancel_return_url' => $cancelUrl,
        ];

        // dd($payload);
        return app(XenditServiceInterface::class)->sessionPayment($payload);

    }
} 