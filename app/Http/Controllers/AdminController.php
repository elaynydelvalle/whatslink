<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function stats()
    {
        $users  = User::where('role', '!=', 'admin')->count();
        $links  = Link::count();
        $clicks = Link::sum('clicks');
        $today  = User::where('role', '!=', 'admin')->whereDate('created_at', today())->count();

        $top = Link::with('user:id,name')
            ->orderByDesc('clicks')
            ->limit(5)
            ->get()
            ->map(fn($l) => [
                'id'         => $l->id,
                'label'      => $l->label,
                'clicks'     => $l->clicks,
                'owner_name' => $l->user->name ?? '',
            ]);

        $recent = User::where('role', '!=', 'admin')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        return response()->json(['ok' => true, 'data' => compact('users', 'links', 'clicks', 'today', 'top', 'recent')]);
    }

    public function users(Request $request)
    {
        $period = $request->query('period', 'all');
        $q      = '%' . $request->query('q', '') . '%';

        $query = User::where('role', '!=', 'admin')
            ->where(fn($sq) => $sq->where('name', 'like', $q)->orWhere('email', 'like', $q))
            ->withCount('links')
            ->withSum('links', 'clicks');

        $cutMap = [
            'today'   => now()->startOfDay(),
            'week'    => now()->subDays(6)->startOfDay(),
            'month'   => now()->startOfMonth(),
            'quarter' => now()->subDays(89)->startOfDay(),
        ];
        if (isset($cutMap[$period])) {
            $query->where('created_at', '>=', $cutMap[$period]);
        }

        $users = $query->orderByDesc('created_at')->get()->map(fn($u) => [
            'id'           => $u->id,
            'name'         => $u->name,
            'email'        => $u->email,
            'plan_name'    => $u->plan_name,
            'active'       => $u->active,
            'created_at'   => $u->created_at,
            'link_count'   => $u->links_count,
            'total_clicks' => $u->links_sum_clicks ?? 0,
        ]);

        return response()->json(['ok' => true, 'data' => $users]);
    }

    public function links(Request $request)
    {
        $q   = '%' . $request->query('q', '') . '%';
        $uid = (int) $request->query('uid', 0);

        $links = Link::with('user:id,name,email')
            ->where(fn($sq) => $sq->where('label', 'like', $q)->orWhere('phone', 'like', $q)->orWhere('message', 'like', $q))
            ->when($uid, fn($sq) => $sq->where('user_id', $uid))
            ->orderByDesc('created_at')
            ->limit(500)
            ->get()
            ->map(fn($l) => array_merge($l->toArray(), [
                'owner_name'  => $l->user->name  ?? '',
                'owner_email' => $l->user->email ?? '',
            ]));

        return response()->json(['ok' => true, 'data' => $links]);
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        if ($request->has('plan_name')) $user->plan_name = $request->input('plan_name');
        if ($request->has('active'))    $user->active    = (bool) $request->input('active');
        $user->save();
        return response()->json(['ok' => true, 'data' => null]);
    }

    public function destroyUser(int $id)
    {
        User::where('id', $id)->where('role', '!=', 'admin')->delete();
        return response()->json(['ok' => true, 'data' => null]);
    }

    public function destroyLink(string $id)
    {
        Link::findOrFail($id)->delete();
        return response()->json(['ok' => true, 'data' => null]);
    }
}
