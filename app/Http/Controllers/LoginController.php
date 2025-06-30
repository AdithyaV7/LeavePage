<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'empno' => 'required',
            'password' => 'required',
        ]);

        $user = DB::table('users')->where('empno', $request->empno)->first();

        if ($user && $user->password === $request->password) {
            session(['empno' => $user->empno]);
            return redirect()->route('leaves.index');
        } else {
            return back()->withErrors(['login' => 'Invalid credentials']);
        }
    }

    public function logout()
    {
        session()->forget('empno');
        return redirect()->route('login');
    }
}
