<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkHit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    private function uid(): string
    {
        return base_convert((int)(microtime(true) * 1000), 10, 36) . bin2hex(random_bytes(3));
    }

    public function click(Request $request, string $id)
    {
        $link = Link::find($id);
        if (!$link) return response()->json(['ok' => false, 'error' => 'Link não encontrado.'], 404);

        $link->increment('clicks');

        LinkHit::create([
            'link_id' => $id,
            'ip'      => substr($request->header('X-Forwarded-For') ?? $request->ip() ?? '', 0, 45),
            'ua'      => substr($request->userAgent() ?? '', 0, 300),
            'referer' => substr($request->header('referer') ?? '', 0, 500),
        ]);

        return response()->json(['ok' => true, 'data' => null]);
    }

    public function getUrl(string $id)
    {
        $link = Link::find($id);
        if (!$link) return response()->json(['ok' => false, 'error' => 'Link não encontrado.'], 404);
        return response()->json(['ok' => true, 'data' => ['url' => $link->url]]);
    }

    public function index(Request $request)
    {
        $q = '%' . trim($request->query('q', '')) . '%';
        $links = Link::where('user_id', Auth::id())
            ->where(function ($query) use ($q) {
                $query->where('label', 'like', $q)
                      ->orWhere('phone', 'like', $q)
                      ->orWhere('message', 'like', $q);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['ok' => true, 'data' => $links]);
    }

    public function store(Request $request)
    {
        $phone = trim($request->input('phone', ''));
        $msg   = trim($request->input('message', ''));
        $url   = trim($request->input('url', ''));
        $label = trim($request->input('label', '')) ?: now()->format('d/m/Y');

        if (!$phone || !$msg || !$url) {
            return response()->json(['ok' => false, 'error' => 'Campos obrigatórios faltando.'], 400);
        }

        $link = Link::create([
            'id'      => $this->uid(),
            'user_id' => Auth::id(),
            'label'   => $label,
            'phone'   => $phone,
            'message' => $msg,
            'url'     => $url,
        ]);

        return response()->json(['ok' => true, 'data' => $link]);
    }

    public function update(Request $request, string $id)
    {
        $link = Link::find($id);
        if (!$link) return response()->json(['ok' => false, 'error' => 'Link não encontrado.'], 404);

        $user = Auth::user();
        if ($link->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['ok' => false, 'error' => 'Não autorizado.'], 403);
        }

        $phone = trim($request->input('phone', ''));
        $msg   = trim($request->input('message', ''));
        $url   = trim($request->input('url', ''));
        $label = trim($request->input('label', ''));

        if (!$phone || !$msg || !$url) {
            return response()->json(['ok' => false, 'error' => 'Campos obrigatórios faltando.'], 400);
        }

        $link->update(['label' => $label, 'phone' => $phone, 'message' => $msg, 'url' => $url]);

        return response()->json(['ok' => true, 'data' => $link->fresh()]);
    }

    public function destroy(Request $request, string $id)
    {
        $link = Link::find($id);
        if (!$link) return response()->json(['ok' => false, 'error' => 'Link não encontrado.'], 404);

        $user = Auth::user();
        if ($link->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['ok' => false, 'error' => 'Não autorizado.'], 403);
        }

        $link->delete();
        return response()->json(['ok' => true, 'data' => null]);
    }
}
