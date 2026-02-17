<?php

    namespace Wixnit\DeepInsight\Traffic;

    use Detection\MobileDetect;
    use DeviceDetector\ClientHints;
    use DeviceDetector\DeviceDetector;
    use DeviceDetector\Parser\Client\Browser;
    use Exception;
    use ReturnTypeWillChange;
    use Wixnit\App\Model;
    use Wixnit\Data\Filter;
    use Wixnit\Data\Pagination;
    use Wixnit\Location\Country;
    use Wixnit\Routing\Request;
    use Wixnit\Routing\Response;
    use Wixnit\Utilities\Random;

    class DeepInsightVisit extends Model
    {
        public string $tag = "";

        public string $country = "";
        public string $city = "";
        public string $os = "";
        public string $browser = "";
        public string $deviceName = "";
        public string $brandName = "";
        public string $model = "";
        public string $deviceType = "";
        public string $ipAddress = "";
        public string $referer = "";

        public bool $isBot = false;
        public string $botInfo = "";

        public bool $isRevisit = false;

        //device type
        public bool $isSmartphone = false;
        public bool $isFeaturePhone = false;
        public bool $isTablet = false;
        public bool $isPhablet = false;
        public bool $isConsole = false;
        public bool $isPortableMediaPlayer = false;
        public bool $isCarBrowser = false;
        public bool $isTV = false;
        public bool $isSmartDisplay = false;
        public bool $isSmartSpeaker = false;
        public bool $isCamera = false;
        public bool $isWearable = false;
        public bool $isPeripheral = false;

        //client type
        public bool $isBrowser = false;
        public bool $isFeedReader = false;
        public bool $isMobileApp = false;
        public bool $isPIM = false;
        public bool $isLibrary = false;
        public bool $isMediaPlayer = false;



        protected array $longText = ["botInfo", "referer"];


        public static function ByTag(string $tag): DeepInsightVisit | null
        {
            $d = DeepInsightVisit::Get(new Filter(['tag'=> $tag]), new Pagination(1, 1));

            if($d->count() > 0)
            {
                return $d[0];
            }
            return null;
        }


        public static function Create(): DeepInsightVisit
        {
            $ret = new DeepInsightVisit();

            try{
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $clientHints = ClientHints::factory($_SERVER);

                $md = new MobileDetect();
                $dd = new DeviceDetector($userAgent, $clientHints);
                $dd->parse();
                

                $ret->isTablet = $md->isTablet();
                $ret->isSmartphone = $md->isMobile();
               
                $ret->country = "";
                $ret->city = "";
                $ret->os = $dd->getOs('name');
                $ret->browser = Browser::getBrowserFamily($dd->getClient('name')) ?? "";
                $ret->deviceName = $dd->getDeviceName();
                $ret->brandName = $dd->getBrandName();
                $ret->model = $dd->getModel();
                $ret->deviceType = "";
                $ret->ipAddress = Request::GetClientIP();
                $ret->referer = Request::GetReferrer();

                $ret->isBot = $dd->isBot();
                $ret->botInfo = $dd->getBot() ?? "";

                $ret->isRevisit = false;

                //device type
                $ret->isSmartphone = $dd->isSmartphone();
                $ret->isFeaturePhone = $dd->isFeaturePhone();
                $ret->isTablet = $dd->isTablet();
                $ret->isPhablet = $dd->isPhablet();
                $ret->isConsole = $dd->isConsole();
                $ret->isPortableMediaPlayer = $dd->isPortableMediaPlayer();
                $ret->isCarBrowser = $dd->isCarBrowser();
                $ret->isTV = $dd->isTV();
                $ret->isSmartDisplay = $dd->isSmartDisplay();
                $ret->isSmartSpeaker = $dd->isSmartSpeaker();
                $ret->isCamera = $dd->isCamera();
                $ret->isWearable = $dd->isWearable();
                $ret->isPeripheral = $dd->isPeripheral();

                //client type
                $ret->isBrowser = $dd->isBrowser();
                $ret->isFeedReader = $dd->isFeedReader();
                $ret->isMobileApp = $dd->isMobileApp();
                $ret->isPIM = $dd->isPIM();
                $ret->isLibrary = $dd->isLibrary();
                $ret->isMediaPlayer = $dd->isMediaPlayer();


                //track revisit and get visit country
                if(Request::GetCookie("deep-insight-sess") != null)
                {
                    $tag = self::ByTag(Request::GetCookie("deep-insight-sess"));

                    if(Request::HasSession("deep-insight-active-sess"))
                    {
                        $activeData = json_decode(Request::GetSession("deep-insight-active-sess"));
                        $ret->country = $activeData->country;
                    }
                    else
                    {
                        $ret->country = ucwords(Country::ByCode(strtolower(self::GetCountryCode()))->name);

                        Response::SetSession("deep-insight-active-sess", json_encode([
                            "country"=> $ret->country,
                            "tag"=> $tag->tag,
                        ]));
                    }
                    $ret->tag = $tag->tag;
                    $ret->isRevisit = true;
                }
                else
                {
                    $country = ucwords(Country::ByCode(strtolower(self::GetCountryCode()))->name);
                    $tag = Random::Characters(64);

                    $ret->country = $country;
                    $ret->tag = $tag;

                    while(DeepInsightVisit::Count(new Filter(['tag'=> $tag])) > 0)
                    {
                        $tag = Random::Characters(64);
                    }

                    setcookie(
                        "deep-insight-sess",
                        $tag,
                        time() + (((60 * 60) * 24) * 365),
                        "/",
                        "",
                        true,
                        true,
                    );

                    Response::SetSession("deep-insight-active-sess", json_encode([
                        "country"=> $country,
                        "tag"=> $tag,
                    ]));
                }
            }
            catch(\Detection\Exception\MobileDetectException $e)
            {

            }
            return $ret;
        }

        public static function IsNewSession(): bool
        {
            return Request::HasSession("deep-insight-active-sess");
        }
        public static function GetCountryCode(string | null $ip =null): string | null
        {
            try{
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://api.country.is/'.(($ip == null) ? self::getUserIP() : $ip));

                if($response->getStatusCode() == 200)
                {
                    $data = json_decode($response->getBody());
                    return $data->country;
                }
                else
                {
                    return null;
                }
            }
            catch(Exception $e)
            {
                //die($e->getMessage());
                return null;
            }
        }
        
        public static function getUserIP()
        {
            // Get real visitor IP behind CloudFlare network
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                    $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                    $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            }
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = $_SERVER['REMOTE_ADDR'];

            if(filter_var($client, FILTER_VALIDATE_IP))
            {
                $ip = $client;
            }
            elseif(filter_var($forward, FILTER_VALIDATE_IP))
            {
                $ip = $forward;
            }
            else
            {
                $ip = $remote;
            }

            return $ip;
        }
    }