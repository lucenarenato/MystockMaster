<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
//use App\Services\FileService;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function __invoke()
    {
        abort_if(Gate::denies('setting_access'), 403);

        $settings = Setting::firstOrFail();

        return view('admin.settings.index', compact('settings'));
    }

    public function edit(Setting $setting)
    {
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validatedData = $request->validate([
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validatedData['company_logo']) {
            $path = FileService::upload($validatedData['company_logo'], 'settings');
            $setting->update(['company_logo' => $path]);
        }

        return redirect()->route('settings.edit', ['setting' => $setting->id])->with('success', 'Configurações atualizadas com sucesso!');
    }
}
