<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\Peixe;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /** Realiza o login de um usuário
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'E-mail não encontrado na base de dados.'], 401);
        }

        /*
         * Verifica se o usuário é inválido ou se a senha está incorreta
         */
        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        /*
         * Cria o token
         * Retorna dados do usuário autenticado com o token de acesso
         */
        $token = $user->createToken('LoginToken')->plainTextToken;

        return response()->json([
            'user' => auth()->user(),
            'token' => $token
            ]);

        /*
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'token' => $token
        ]);*/
    }


    /** Realiza o logout do usuário e exclui os tokens de acesso
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();
        Auth::guard('web')->logout();
        return response()->json(['message' => 'Usuário desconectado com sucesso.'], 200);
    }


    /** Cria um novo usuário
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        /*
         * Validação dos campos da request
         */
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'name' => 'required|string|max:50'
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
            'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
            ],
            'token' => $token
        ], 201);

        // Falta criar a parte de validação por email
    }

    /** Retorna informações do usuário autenticado
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        if (auth()->user() === $request->user())
        return response()->json(['user' => $request->user()]);
    }

    private function generateRandomToken(int $bytes)
    {
        $token = random_bytes($bytes);
        return bin2hex($token);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = $this->generateRandomToken(32);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token
        ]);

        Mail::to($request->email)->send(new ResetPassword($token));

        return response()->json(['message' => 'Instruções para a recuperação da senha foram enviadas para o endereço ' . $request->email], 200);
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|confirmed|min:6'
        ]);

        $token = DB::table('password_resets')->where('token', $request->token);

        if (is_null($token->first())) {
            return response()->json(['message' => 'Requisição inválida'], 401);
        }

        try {
            $user = User::where('email', $token->first()->email)->firstOrFail();
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Usuário inválido'], 401);
        }


        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $token->first()->email)->delete();


        return response()->json(['message' => 'Senha alterada com sucesso']);
    }

    public function getStatusPescador()
    {

        try {
            $pescador = auth()->user();
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Requisição inválida'], 401);
        }

        $totalPontos = $pescador->pontos()->count();
        $pescados = 0;
        $peixesArray = collect();


        foreach ($pescador->pontos()->get() as $ponto) {
            $pescados += $ponto->pescados()->count();
            foreach ($ponto->pescados()->get() as $pescado) {
                $peixesArray->push($pescado->peixe->id);
            }
        }

        $key = $peixesArray->countBy()->sortDesc()->keys()->first();

        if (empty($key)) {

            $peixeMaisPescado['nome'] = "";
            $peixeMaisPescado['quantidade'] = 0;

        } else {

            $peixeMaisPescado['nome'] = Peixe::find($key)->nome;
            $peixeMaisPescado['quantidade'] = $peixesArray->countBy()->sortDesc()->values()->first();
        }

        return response()->json([
            'pontos_registrados' => $totalPontos,
            'peixes_registrados' => $pescados,
            'peixe_mais_pescado' => $peixeMaisPescado['nome'],
            'peixe_mais_pescado_qtd' => $peixeMaisPescado['quantidade']
        ]);

    }

    public function updateUserProfile(Request $request)
    {
        if ($request->has("password")) {
            $request->validate([
                'email' => ['required',
                    'email',
                    Rule::unique('users')->ignoreModel($request->user())
                ],
                'password' => 'required|string|confirmed|min:6',
                'name' => 'required|string|max:50'
            ]);
        } else {
            $request->validate([
                'email' => ['required',
                    'email',
                    Rule::unique('users')->ignoreModel($request->user())
                ],
                'name' => 'required|string|max:50'
            ]);
        }

        try {
            $user = auth()->user();
        }
        catch (ModelNotFoundException)
        {
            return response()->json(["message" => "Requisição inválida", 403]);
        }

        $user->email = $request->email;
        $user->name = $request->name;
        if ($request->has("password")) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return response()->json([
            "message" => "Dados atualizados com suceso.",
            "user" => $user
            ]);

    }


}
