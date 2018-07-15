<?php

namespace App\Http\Middleware;

use Closure;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Auth\AuthenticationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class MyCheckClientCredentials extends CheckClientCredentials
{
    /**
     * The Resource Server instance.
     *
     * @var ResourceServer
     */
    private $server;
    /**
     * Create a new middleware instance.
     *
     * @param  ResourceServer  $server
     * @return void
     */
    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        $psr = (new DiactorosFactory)->createRequest($request);
        try{
            $psr = $this->server->validateAuthenticatedRequest($psr);

            // This is the custom line. Set an "oauth_client_id" field on the
            // request with the client id determined by the bearer token.
            $request['oauth_client_id'] = $psr->getAttribute('oauth_client_id');
        } catch (OAuthServerException $e) {
            throw new AuthenticationException;
        }
        foreach ($scopes as $scope) {
           if (!in_array($scope,$psr->getAttribute('oauth_scopes'))) {
             throw new AuthenticationException;
           }
         }
        return $next($request);
    }
}