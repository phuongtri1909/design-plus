<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Active
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
     
        $user = auth()->user();
        if($user->status == 'active')
        {
            return $next($request);
        }
        return redirect()->back()->with('error','Tài khoản của bạn không được kích hoạt. Vui lòng liên hệ với quản trị viên.');
      
    }
}
