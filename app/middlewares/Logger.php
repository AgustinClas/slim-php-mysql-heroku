<?php
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

class Logger
{
    public static function LogSocio(Request $request, $handler)
    {
        $payload = json_encode(array("mensaje" => "Perfil Erroneo"));

        try {

            $token = $request->getHeader("token")[0];

            AutentificadorJWT::VerificarToken($token);
            $perfil = AutentificadorJWT::ObtenerData($token)->perfil;
            if ($perfil == "socio") {
                return $handler->handle($request);
            }
        } catch (Exception $e) {
            $payload = json_encode(array("mensaje" => "Token no validado"));
            
        }
        $response = new Response();
        $response->getbody()->write($payload);
        return $response;
    }

    public static function LogMozo(Request $request, $handler)
    {
        $payload = json_encode(array("mensaje" => "Perfil Erroneo"));

        try {

            $token = $request->getHeader("token")[0];

            AutentificadorJWT::VerificarToken($token);

            $perfil = AutentificadorJWT::ObtenerData($token)->perfil;
            
            if ($perfil == "mozo" || $perfil == "socio") {
                return $handler->handle($request);
            }
        } catch (Exception $e) {
            $payload = json_encode(array("mensaje" => "Token no validado"));
            
        }
        $response = new Response();
        $response->getbody()->write($payload);
        return $response;
    }

    public static function LogCocinero(Request $request, $handler)
    {
        $payload = json_encode(array("mensaje" => "Perfil Erroneo"));

        try {

            $token = $request->getHeader("token")[0];

            AutentificadorJWT::VerificarToken($token);

            $perfil = AutentificadorJWT::ObtenerData($token)->perfil;
            
            if ($perfil == "cocinero" || $perfil == "socio") {
                return $handler->handle($request);
            }
        } catch (Exception $e) {
            $payload = json_encode(array("mensaje" => "Token no validado"));
            
        }
        $response = new Response();
        $response->getbody()->write($payload);
        return $response;
    }

    public static function LogBartender(Request $request, $handler)
    {
        $payload = json_encode(array("mensaje" => "Perfil Erroneo"));

        try {

            $token = $request->getHeader("token")[0];

            AutentificadorJWT::VerificarToken($token);

            $perfil = AutentificadorJWT::ObtenerData($token)->perfil;
            
            if ($perfil == "bartender" || $perfil == "socio") {
                return $handler->handle($request);
            }
        } catch (Exception $e) {
            $payload = json_encode(array("mensaje" => "Token no validado"));
            
        }
        $response = new Response();
        $response->getbody()->write($payload);
        return $response;
    }

    public static function LogCervecero(Request $request, $handler)
    {
        $payload = json_encode(array("mensaje" => "Perfil Erroneo"));

        try {

            $token = $request->getHeader("token")[0];

            AutentificadorJWT::VerificarToken($token);

            $perfil = AutentificadorJWT::ObtenerData($token)->perfil;
            
            if ($perfil == "cervecero" || $perfil == "socio") {
                return $handler->handle($request);
            }
        } catch (Exception $e) {
            $payload = json_encode(array("mensaje" => "Token no validado"));
            
        }
        $response = new Response();
        $response->getbody()->write($payload);
        return $response;
    }

   

    public static function LogUsuario(Request $request, $handler)
    {
        try {

            $token = $request->getHeader("token")[0];

            AutentificadorJWT::VerificarToken($token);

            return $handler->handle($request);

        } catch (Exception $e) {
            
            $payload = json_encode(array("mensaje" => "Token no validado"));
            $response = new Response();
            $response->getbody()->write($payload);
            return $response;
        }
        
    }
}