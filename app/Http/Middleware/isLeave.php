<?php

namespace App\Http\Middleware;

use App\Models\Leave;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class isLeave
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $hasLeave = Leave::where('user_id', $user->id)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasLeave) {
            Notification::make()
                ->title('Akses Ditolak')
                ->danger()
                ->body('Anda sedang cuti atau sedang mengajukan cuti.')
                ->send();

            return redirect('dashboard/attendances');
        }

        return $next($request);
    }
}

