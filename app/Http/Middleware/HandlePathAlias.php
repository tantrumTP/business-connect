<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PathAlias;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

class HandlePathAlias
{
    /**
     * Capture aliases and internally redirect to original path
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $path = '/' . trim($request->path(), '/');
            $alias = PathAlias::active()->where('alias', $path)->first();
            if ($alias) {
                // Create a new request with the original route
                $originalRequest = Request::create($alias->path, $request->method());

                // Dispatch the new request and get the response
                $response = Route::dispatch($originalRequest);

                return $response;
            } else {
                $originalPath = PathAlias::active()->where('path', $path)->first();
                if ($originalPath) {
                    return Redirect::to($originalPath->alias, 301);
                }
            }
        }

        return $next($request);
    }
}
