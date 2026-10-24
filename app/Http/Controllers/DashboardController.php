<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Ringkasan: jumlah tugas per status dan tugas yang tenggatnya paling dekat. */
    public function __invoke(Request $request): View
    {
        $tasks = $request->user()->tasks();

        $counts = [];
        foreach (TaskStatus::cases() as $status) {
            $counts[$status->value] = (clone $tasks)->where('status', $status)->count();
        }

        return view('dashboard', [
            'counts' => $counts,
            'overdue' => (clone $tasks)->where('status', '!=', TaskStatus::Done)
                ->whereDate('due_date', '<', today())->count(),
            'upcoming' => (clone $tasks)->with('category')
                ->where('status', '!=', TaskStatus::Done)
                ->whereNotNull('due_date')
                ->orderBy('due_date')
                ->limit(5)
                ->get(),
        ]);
    }
}
