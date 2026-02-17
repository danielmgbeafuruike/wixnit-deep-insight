<?php

    namespace Wixnit\DeepInsight;

    use Wixnit\App\api;
    use Wixnit\DeepInsight\Auth\DeepInsightAuth;
    use Wixnit\Enum\HTTPResponseCode;
    use Wixnit\Interfaces\IRouteGuard;
    use Wixnit\Routing\PayloadedGuard;
    use Wixnit\Routing\Request;
    use Wixnit\Routing\Response;
    
    class Guard extends PayloadedGuard implements IRouteGuard
    {
        public function checkAccess(Request $req): bool
        {
            $token = Request::GetBearerToken();

            if($token == null)
            {
                return false;
            };

            $auth = DeepInsightAuth::ByToken($token);

            if($auth->id != null)
            {
                $this->setPayload($auth);
                return true;
            }
            return false;
        }

        public function onFail(): Response
        {
            return (new Response(HTTPResponseCode::UNAUTHORIZED))
                ->json(api::Unauthorized());
        }
    }