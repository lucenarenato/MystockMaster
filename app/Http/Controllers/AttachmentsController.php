<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Upload;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AttachmentsController extends Controller
{
    public function download(Customer $customer, Upload $upload)
    {
        abort_if(Gate::denies('customer_show'), 403);
        abort_unless($upload->arquivavel_type === get_class($customer) && $upload->arquivavel_id === $customer->id, 404);

        return Storage::disk($upload->disk)->download($upload->caminho, $upload->nome);
    }
}
