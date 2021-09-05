<?php

namespace Modules\jgate\src;

use Joonika\Database;
use Joonika\Modules\Users\Users;


class jgate
{
    protected static $instance = null;
    static $gatewayId = false;
    static $gate = false;
    static $apiKey = false;
    static $gatewayAddress = false;
    static $gatewaySlug = false;
    static $token = false;
    static $tokenDate = false;
    static $status = 403;
    static $message = 'token not valid';
    static $data = [];
    static $database = false;

    static $serviceAddress = false;
    static $serviceName = false;
    static $needToken = true;
    static $cacheCheck = true;
    static $inputArray = [];
    static $content_type = "application/json";

    static $sendType = 'input';

    public function __construct()
    {
        if (empty(self::$database)) {
            self::$database = Database::connect();
        }
        $gate = self::$database->get('jgate.jgate_gateways', '*', [
            "id" => self::$gatewayId,
        ]);
        if ($gate) {
            self::$apiKey = $gate['apiKey'];
            self::$gatewayId = $gate['id'];
            self::$gatewaySlug = $gate['slugControl'];
            self::$gatewayAddress = $gate['mainAddress'];
            self::$token = $gate['lastToken'];
            self::$tokenDate = $gate['lastTokenDate'];
            self::$gate = $gate;
            return $gate;
        }
        return false;
    }

    public static function manageServices()
    {
        return [
            'getToken' => 0,
            'checkToken' => 0,
        ];
    }

    public static function request($serviceName, $input, $cacheDisable = false, $gatewayId = null)
    {
        self::$gatewayId = !empty(self::$gatewayId) ? self::$gatewayId : $gatewayId;
        self::$serviceName = $serviceName;
        self::$inputArray = $input;
        if (!empty(self::$gatewayId) && empty(self::$instance[$gatewayId])) {
            self::$instance[self::$gatewayId] = new jgate();
        }
        if (empty(self::$database)) {
            self::$database = Database::connect();
        }
        $managedService = substr($serviceName, 0, strlen('manage/')) == 'manage/';
        if (array_key_exists($serviceName, self::manageServices()) || $managedService) {
            if (empty(self::$gatewayId) || empty(self::$instance[self::$gatewayId])) {
                return self::returnResponse([], false, __("gateway is invalid"));
            }
            self::$serviceAddress = $serviceName;
            if ($managedService) {
                self::$needToken = 1;
            } else {
//                jdie($serviceName);
            }

        } else {

            $service = self::$database->get("jgate.jgate_services_rel(gsr)", [
                "[>]jgate.jgate_services(gs)" => ["gsr.sId" => "id"]
            ], [
                "gsr.address",
                "gs.slug",
                "gs.type",
                "gs.status(slugStatus)",
                "gsr.status(gatewayStatus)",
                "gsr.gId(gatewayID)",
            ], [
                "AND" => [
                    "gsr.status" => 1,
                    "gs.slug" => $serviceName,
                ],
                "ORDER" => ["sort" => "ASC"]
            ]);
            if (empty($service)) {
                $notFound = true;
                if (strpos($serviceName, '/')) {
                    $findApiKey = self::findApiKey();
                    if ($findApiKey[0] == 200) {
                        $notFound = false;
                        self::$serviceAddress = $serviceName;
                        self::$gatewayAddress = $findApiKey[1];
                        self::$apiKey = $findApiKey[2];
                        self::$needToken = false;
                        self::$gatewayId = 0;
                        self::$sendType = 'json';
                        if (empty(self::$instance[0])) {
                            self::$instance[0] = new jgate();
                        }
                    }
                }
                if ($notFound) {
                    return self::returnResponse([], false, __("service not found"));
                }

            } else {
                if ($service['type'] == 'json') {
                    self::$sendType = 'json';
                }
                $serviceName = $service['address'];
                self::$serviceAddress = $serviceName;
                self::$gatewayId = $service['gatewayID'];
                if (empty(self::$instance[$service['gatewayID']])) {
                    self::$instance[$service['gatewayID']] = new jgate();
                }
                $addressF = is_json(self::$gate['servicesJson'], true);
                if (!empty($addressF)) {
                    foreach ($addressF as $ad) {
                        if ($ad['address'] == $serviceName) {
                            if ($ad['tokenless'] == 1) {
                                self::$needToken = false;
                                break;
                            }
                        }
                    }
                }
            }
        }
        if ($serviceName != 'getToken') {
            $gatewayIdToken = !empty($service['gatewayID']) ? $service['gatewayID'] : null;
            $checkToken = self::getToken($gatewayIdToken);
            if ($checkToken[0] != 200) {
                return self::returnResponse([], false, $checkToken[1]);
            }
        }
        if (self::$cacheCheck) {
            //TODO get cache
        }
        $response = self::$instance[self::$gatewayId]->curl(self::$serviceAddress);
        if (!empty($response['success'])) {
            return self::returnResponse($response['data']);
        } else {
            return self::returnResponse([], false, !empty($response['errors']) ? $response['errors'] : []);
        }
    }


    private static function getToken($gatewayId = null)
    {

        if (!empty($gatewayId)) {
            self::$gatewayId = $gatewayId;
        }
        if (empty(self::$needToken)) {
            return [200, __("not need token")];
        }
        if (empty(self::$instance[self::$gatewayId])) {
            self::$instance[self::$gatewayId] = new self();
        }

        if (empty(self::$instance[self::$gatewayId])) {
            return [500, __("bad gateway for token")];
        } else {
            if (!self::$token || empty(self::$tokenDate) || strtotime(self::$tokenDate) <= strtotime("-15 min")) {
                $curl = curl_init();
                $ip='';
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } //whether ip is from the proxy
                elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } //whether ip is from the remote address
                elseif(isset($_SERVER['REMOTE_ADDR'])) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                curl_setopt_array($curl, array(
                    CURLOPT_URL => self::$gatewayAddress . "/getToken",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        "apikey: " . self::$apiKey,
                        "clientIp: " . $ip,
                    ),
                ));
                $err = curl_error($curl);
                $response = curl_exec($curl);
                curl_close($curl);
                $is_json = is_json($response, true, true);
                if ($is_json) {
                    if (isset($is_json['data']['token'])) {
                        self::$token = $is_json['data']['token'];
                        self::$tokenDate = date("Y/m/d H:i:s");
                        self::$database->update('jgate.jgate_gateways', [
                            "lastToken" => self::$token,
                            "lastTokenDate" => self::$tokenDate,
                            "lastCheckToken" => $response,
                        ], [
                            "id" => self::$gatewayId
                        ]);
                        return [200, self::$token];

                    } else {
                        return [403, []];
                    }
                } else {
                    self::$data['token'] = self::$token;
                }
            }
        }
        return [200, self::$token];
    }

    private
    static function getCache($serviceSlug, $input)
    {
        $data = self::$database->get('gateway_cache', 'output', [
            "AND" => [
                "input" => json_encode($input, JSON_UNESCAPED_UNICODE),
                "expireDate[>=]" => date("Y/m/d H:i:s"),
                "serviceSlug" => $serviceSlug,
            ]
        ]);
        return $data;
    }

    private
    static function setCache($serviceSlug, $input, $output, $expireAfter = 600)
    {
        $out = is_json($output, true, true);
        if ($out) {
            if (isset($out['status']) && $out['status'] != 200) {
                $expireAfter = 60;
            }
            self::$database->insert('gateway_cache', [
                "input" => json_encode($input, JSON_UNESCAPED_UNICODE),
                "expireDate" => date("Y/m/d H:i:s", time() + $expireAfter),
                "datetime" => date("Y/m/d H:i:s"),
                "gatewaySlug" => self::$gatewaySlug,
                "serviceSlug" => $serviceSlug,
                "output" => json_encode($out, JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    public
    function curl($serviceAddress)
    {
        $url = rtrim(self::$gatewayAddress, '/') . '/' . ltrim($serviceAddress, '/');
        $postArray = self::$inputArray;
        $curl = curl_init();
        $http_request_header = [];
        if (self::$sendType == 'json') {
            array_push($http_request_header, 'Content-Type: application/json');
            $postArray = json_encode($postArray, 256);
        }
        $ip='';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } //whether ip is from the proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } //whether ip is from the remote address
        elseif(isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        array_push($http_request_header, "clientIp: " . $ip);
        array_push($http_request_header, "serverType: " . JK_SERVER_TYPE);
//        jdie(self::$serviceName);
//        if(self::$serviceName=='srp_trip_reserve'){
//            jdie($postArray);
//        }
//        jdie(4);
        if (self::$needToken && !in_array($serviceAddress, ['getToken', 'checkToken'])) {
            array_push($http_request_header, "token: " . self::$token);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $postArray,
                CURLOPT_HTTPHEADER => $http_request_header,
            ));
        } else {
            array_push($http_request_header, "apikey: " . self::$apiKey);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $postArray,
                CURLOPT_HTTPHEADER => $http_request_header,
            ));
        }
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if (self::$content_type == 'application/json') {
            if ($err) {
                return self::returnResponse([], false, $err);
            }
            $isJson = is_json($response, true, true);
            if (empty($isJson)) {
                $txt=__("response type not valid");
                if(JK_APP_DEBUG()){
                    $txt=$response;
                }
                return self::returnResponse([], false,$txt );
            }
            $response = $isJson;
        }
        return $response;
    }

    public
    static function returnResponse($data = [], $success = true, $error = '')
    {
        if ($success) {
            return [
                "success" => $success,
                "data" => $data,
            ];
        } else {
            return [
                "success" => $success,
                "errors" => is_array($error) ? $error : ([['message' => $error]]),
            ];
        }
    }

    public
    static function errorMessages($value)
    {
        $message = '';
        if (!empty($value['errors'])) {
            if (is_array($value['errors'])) {
                foreach ($value['errors'] as $error) {
                    $message .= $error['message'] . "\n";
                }
            }else{
                $message = $value['errors'];
            }
        }
        return $message;
    }

    private
    static function findApiKey()
    {
        $defaultUrl = env('jgate_default_url');
        $defaultApiKey = env('jgate_default_ApiKey');
        if (!empty($defaultApiKey) && !empty($defaultUrl)) {
            return [
                200, $defaultUrl, $defaultApiKey
            ];
        } else {
            return [
                400, false, false
            ];
        }

    }
}
