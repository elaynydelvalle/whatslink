<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    private function uid(): string
    {
        return base_convert((int)(microtime(true) * 1000), 10, 36) . bin2hex(random_bytes(3));
    }

    public function index()
    {
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        $plans = Plan::when(!$isAdmin, fn($q) => $q->where('active', true))
            ->orderBy('sort_order')
            ->get();

        return response()->json(['ok' => true, 'data' => $plans]);
    }

    public function store(Request $request)
    {
        $plan = Plan::create([
            'id'          => $this->uid(),
            'name'        => trim($request->input('name', '')),
            'price'       => (float) $request->input('price', 0),
            'max_links'   => (int) $request->input('maxLinks', 10),
            'highlighted' => (bool) $request->input('highlighted', false),
            'active'      => true,
            'cta'         => trim($request->input('cta', 'Assinar')),
            'features'    => $request->input('features', []),
            'sort_order'  => (int) $request->input('order', 1),
        ]);

        return response()->json(['ok' => true, 'data' => $plan]);
    }

    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->update([
            'name'        => trim($request->input('name', '')),
            'price'       => (float) $request->input('price', 0),
            'max_links'   => (int) $request->input('maxLinks', 10),
            'highlighted' => (bool) $request->input('highlighted', false),
            'cta'         => trim($request->input('cta', 'Assinar')),
            'features'    => $request->input('features', []),
            'sort_order'  => (int) $request->input('order', 1),
        ]);

        return response()->json(['ok' => true, 'data' => null]);
    }

    public function destroy(string $id)
    {
        Plan::findOrFail($id)->delete();
        return response()->json(['ok' => true, 'data' => null]);
    }

    public function toggle(string $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->update(['active' => !$plan->active]);
        return response()->json(['ok' => true, 'data' => null]);
    }
}
