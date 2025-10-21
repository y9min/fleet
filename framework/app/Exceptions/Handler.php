<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		\Illuminate\Auth\AuthenticationException::class,
		\Illuminate\Auth\Access\AuthorizationException::class,
		\Symfony\Component\HttpKernel\Exception\HttpException::class,
		\Illuminate\Database\Eloquent\ModelNotFoundException::class,
		\Illuminate\Session\TokenMismatchException::class,
		\Illuminate\Validation\ValidationException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $exception
	 * @return void
	 */
	public function report(Throwable $exception) {
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $exception
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Throwable $exception) {
		// Hide SQL exceptions from end users in production
		if ($exception instanceof \Illuminate\Database\QueryException && !config('app.debug')) {
			// Log the actual error for debugging
			\Log::error('Database Query Exception', [
				'message' => $exception->getMessage(),
				'sql' => $exception->getSql() ?? 'N/A',
				'bindings' => $exception->getBindings() ?? [],
				'url' => $request->fullUrl(),
				'user_id' => auth()->id(),
			]);
			
			// Return a user-friendly error message
			if ($request->expectsJson()) {
				return response()->json([
					'error' => 'A database error occurred. Please try again later.',
					'message' => 'Service temporarily unavailable'
				], 500);
			}
			
			return response()->view('errors.500', [
				'message' => 'We\'re experiencing technical difficulties. Please try again later.'
			], 500);
		}
		
		// Hide other database-related exceptions in production
		if ($exception instanceof \PDOException && !config('app.debug')) {
			\Log::error('PDO Exception', [
				'message' => $exception->getMessage(),
				'url' => $request->fullUrl(),
				'user_id' => auth()->id(),
			]);
			
			if ($request->expectsJson()) {
				return response()->json([
					'error' => 'A database error occurred. Please try again later.',
					'message' => 'Service temporarily unavailable'
				], 500);
			}
			
			return response()->view('errors.500', [
				'message' => 'We\'re experiencing technical difficulties. Please try again later.'
			], 500);
		}
		
		return parent::render($request, $exception);
	}

	/**
	 * Convert an authentication exception into an unauthenticated response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Auth\AuthenticationException  $exception
	 * @return \Illuminate\Http\Response
	 */
	protected function unauthenticated($request, AuthenticationException $exception) {
		if ($request->expectsJson()) {
			return response()->json(['error' => 'Unauthenticated.'], 401);
		}

		return redirect()->guest(route('login'));
	}
}
