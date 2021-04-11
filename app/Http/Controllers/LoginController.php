<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        Log::channel('daily')->debug('LoginController#login');

        $input = $request->all();

        Log::channel('daily')->debug(sprintf('LoginController#login - [ %s ]', json_encode($input)));
        Log::channel('daily')->debug(sprintf('LoginController#login - Usuário:[ %s ]', $input['usuario']));

        $usuario = Usuario::where('cpf', $input['usuario'])
            ->where('senha', md5($input['senha']))
            ->first();

        if ($usuario != null) {
            session(['usuario' => $usuario]);
            session(['nivel' => $usuario->getNivel()]);

            return redirect('menu');
        }

        return redirect('/')->with('error','Usuário e/ou Senha inválidos!');
    }

    public function logoff(Request $request)
    {
        Log::channel('daily')->debug('LoginController#logoff');

        $request->session()->forget(['usuario', 'nivel']);
        $request->session()->flush();

        return redirect('/');
    }

    public function menu() 
    {
        Log::channel('daily')->debug(sprintf('LoginController#menu - SESSAO:[%s]', session()->get('usuario')));

        if ('' == session()->get('usuario')) {
            return redirect('/')->with('error','É necessário efetuar o login!');
        } 

        return view('menu')->with(['usuario' => session()->get('usuario')]);
    }
}
