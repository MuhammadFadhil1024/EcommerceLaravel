<?php

namespace App\Http\Controllers\Webhook;

use App\Actions\Payment\HandleXenditWebhook;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('Xendit webhook received', $payload);

            app(HandleXenditWebhook::class)->handle($payload);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Xendit webhook error', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
