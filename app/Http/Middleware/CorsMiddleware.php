<?php
/**
 * cors header middleware for laravel framework
 * @author di <di3@gmx.net>
 */
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use App\Exceptions\Handler;

class CorsMiddleware implements Middleware {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $Request
	 * @param  \Closure  $next
	 * @return \Illuminate\Http\Response
	 */
	public function handle($Request, Closure $next) {
		if ($Request->isMethod('OPTIONS')) {
			//@todo add your options here
			$Response = response(null, 200);
		} else {
			try {
				$Response = $next($Request);
			} catch (Exception $e) {
				//catch exceptions and create the response
				//we can add cors header then
				$Handler = new Handler();
				$Response = $Handler->render($Request,$e);
			}
		}
		$origin = $Request->header('Origin');
		switch (strtolower($origin)) {
			case null:
				break;
			case 'null': //localfile
			case 'http://localhost':
			//@todo add your api here
				//handle view response
				if (! $Response instanceof SymfonyResponse) {
					$Response = new Response($Response);
				}
				$Response->header(
					'Access-Control-Allow-Methods',
					'GET, POST, PUT, DELETE, OPTIONS'
				);
				$Response->header(
					'Access-Control-Max-Age',
					'86400'
				);
				$Response->header(
					'Access-Control-Allow-Headers',
					'Origin, Content-Type, Accept, Authorization, X-Request-With'
				);
				$Response->header(
					'Access-Control-Allow-Origin',
					$origin
				);
				$Response->header(
					'Access-Control-Allow-Credentials',
					'true'
				);
				break;
			default:
		}
		return $Response;
	}
}
