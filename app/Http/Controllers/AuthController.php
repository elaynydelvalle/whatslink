<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function me(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'error' => 'Não autenticado.'], 401);
        }
        $u = Auth::user();
        return response()->json(['ok' => true, 'data' => [
            'id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role,
        ]]);
    }

    public function login(Request $request)
    {
        $email = strtolower(trim($request->input('email', '')));
        $pass  = $request->input('password', '');

        if (!$email || !$pass) {
            return $this->fail('Preencha e-mail e senha.');
        }

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($pass, $user->password ?? '')) {
            return $this->fail('E-mail ou senha incorretos.');
        }
        if (!$user->active) {
            return $this->fail('Conta desativada. Contate o administrador.');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'data' => [
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role,
        ]]);
    }

    public function register(Request $request)
    {
        $name  = trim($request->input('name', ''));
        $email = strtolower(trim($request->input('email', '')));
        $pass  = $request->input('password', '');
        $pass2 = $request->input('password2', '');

        if (!$name)                               return $this->fail('Nome obrigatório.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return $this->fail('E-mail inválido.');
        if (strlen($pass) < 6)                    return $this->fail('Senha mínima de 6 caracteres.');
        if ($pass !== $pass2)                      return $this->fail('Senhas não coincidem.');
        if (User::where('email', $email)->exists()) return $this->fail('E-mail já cadastrado.');

        $user = User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => $pass,
            'role'      => 'user',
            'plan_name' => 'Gratuito',
            'active'    => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'data' => [
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role,
        ]]);
    }

    public function google(Request $request)
    {
        $email = strtolower(trim($request->input('email', '')));
        $name  = trim($request->input('name', ''));
        $gid   = trim($request->input('google_id', ''));

        if (!$email || !$name) return $this->fail('Dados do Google inválidos.');

        $adminEmails = ['elaine@chromotech.com.br'];

        $user = User::where('email', $email)->first();

        if (!$user) {
            $role = in_array($email, $adminEmails) ? 'admin' : 'user';
            $user = User::create([
                'name'      => $name,
                'email'     => $email,
                'google_id' => $gid,
                'role'      => $role,
                'plan_name' => $role === 'admin' ? null : 'Gratuito',
                'active'    => true,
            ]);
        } else {
            if (!$user->active) return $this->fail('Conta desativada.');
            $role = in_array($email, $adminEmails) ? 'admin' : $user->role;
            $user->update(['name' => $name, 'google_id' => $gid, 'role' => $role]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'data' => [
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role,
        ]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['ok' => true, 'data' => null]);
    }

    private function fail(string $msg, int $code = 400)
    {
        return response()->json(['ok' => false, 'error' => $msg], $code);
    }
}
