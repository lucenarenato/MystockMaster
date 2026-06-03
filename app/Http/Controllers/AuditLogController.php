<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    public function __invoke()
    {
        abort_if(Gate::denies('setting_access'), 403);

        return view('audit-logs.index', [
            'logs' => AuditLog::with('user')
                ->latest('created_at')
                ->paginate(25),
        ]);
    }
}

