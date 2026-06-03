<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\SyncYouCanProducts;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantWebhookController extends Controller
{
    public function woocommerce(Request $request, Tenant $tenant): JsonResponse
    {
        Tenant::setCurrent($tenant);

        Log::info('WooCommerce webhook received', [
            'tenant_id' => $tenant->id,
            'topic' => $request->header('x-wc-webhook-topic'),
            'resource' => $request->header('x-wc-webhook-resource'),
            'event' => $request->header('x-wc-webhook-event'),
            'payload_id' => $request->input('id'),
        ]);

        Tenant::setCurrent(null);

        return response()->json(['message' => 'Webhook recebido.']);
    }

    public function youcan(Request $request, Tenant $tenant): JsonResponse
    {
        Tenant::setCurrent($tenant);

        $payload = $request->all();
        $products = $payload['products'] ?? (array_is_list($payload) ? $payload : [$payload]);

        SyncYouCanProducts::dispatch($products);

        Tenant::setCurrent(null);

        return response()->json(['message' => 'Sincronização agendada.'], 202);
    }
}

