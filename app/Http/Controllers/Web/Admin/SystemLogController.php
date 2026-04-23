<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SystemLogController extends Controller
{
    public function show(): View
    {
        $path = $this->logPath();
        $exists = File::exists($path);

        return view('admin.system-log.show', [
            'path' => $path,
            'exists' => $exists,
            'size' => $exists ? File::size($path) : 0,
            'updatedAt' => $exists ? File::lastModified($path) : null,
            'content' => $exists ? File::get($path) : '',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['nullable', 'string'],
        ]);

        $path = $this->logPath();

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $data['content'] ?? '');

        return redirect()
            ->route('admin.system-log.show')
            ->with('status', 'Laravel log file updated.');
    }

    public function destroy(): RedirectResponse
    {
        $path = $this->logPath();

        if (File::exists($path)) {
            File::delete($path);
        }

        return redirect()
            ->route('admin.system-log.show')
            ->with('status', 'Laravel log file deleted.');
    }

    private function logPath(): string
    {
        return (string) config('insights.system_log_path', storage_path('logs/laravel.log'));
    }
}
