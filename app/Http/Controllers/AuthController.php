<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        /*
         * Validação dos campos email e password
         */
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        /*
         * Checa se o email do usuário existe
         * Se existe, instancia o usuário
         * Se não existe, retorna mensagem de erro
         */
        try {
            $user = User::where('email', $request->email)->firstOrFail();
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['error' => 'E-mail não encontrado na base de dados.'], 404);
        }

        /*
         * Verifica se o usuário é inválido ou se a senha está incorreta
         */
        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json('Credenciais inválidas', 401);
        }

        /*
         * Cria o token
         * Retorna dados do usuário autenticado com o token de acesso
         */
        $token = $user->createToken('LoginToken')->plainTextToken;
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'token' => $token
        ]);
    }

    /*
     * Realiza o logout do usuário e exclui os tokens de acesso
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Usuário desconectado com sucesso.'], 200);
    }


    public function register (Request $request)
    {
        /*
         * Validação dos campos da request
         */
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'name' => 'required|string'
        ]);

        /*
         * Cria o usuário a partir dos dados validados
         */
        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password)
        ]);

        /*
         * Cria o token;
         * Retorna as informações do usuário criado com o token de acesso
         */
        $token = $user->createToken('RegisterToken')->plainTextToken;
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token
        ], 201);

        // Falta criar a parte de validação por email
    }

    public function user()
    {
        return auth()->user()->pontos()->get(); // So para testar
    }
}
