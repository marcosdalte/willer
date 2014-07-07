<?php

trait Util {
    public static function get($input,$key,$default = null) {
        if (!is_array($input) and !(is_object($input))) {
            return null;
        }

        if (is_array($input)) {
            return isset($input[$key]) ? !empty($input[$key]) ? $input[$key] : $default : $default;

        } else if (is_object($input)) {
            return isset($input->$key) ? !empty($input->$key) ? $input->$key : $default : $default;
        }
    }

    public static function validate($list = []) {
        if (empty($list)) {
            return false;
        }

        foreach ($list as $value) {
            if (empty($value)) {
                return false;
            }
        }

        return true;
    }

    public static function csrf($request) {
        $mt_rand = mt_rand();
        $csrf = sprintf("%s/%s",$request->SERVER["REMOTE_ADDR"],$mt_rand);
        $csrf = hash("whirlpool",$csrf);

        return $csrf;
    }

    public static function autoLoad() {
        spl_autoload_register(function ($class) {
            $class_ = $class;

            $class = str_replace("\\","/",$class);
            $class = sprintf("%s/%s.php",ROOT_PATH,$class);

            if (!file_exists($class)) {
                $scan_dir = array_diff(scandir(ROOT_PATH."/Vendor"),array("..","."));

                foreach ($scan_dir as $value) {
                    $class = sprintf("%s/Vendor/%s/%s.php",ROOT_PATH,$value,$class_);
                    $class = str_replace("\\","/",$class);

                    if (file_exists($class)) {
                        break;
                    }
                }
            }

            include_once $class;
        });
    }

    public static function httpRedirect($url) {
        header("Location: ".$url);
    }

    public static function renderToJson($data = []) {
        header("Content-Type: application/json");
        print json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    public static function urlRoute($url,$page) {
        foreach ($url as $key => $value) {
            if (preg_match($key,$page)) {
                return [
                    "application" => $value["application"],
                    "protect_resource" => $value["protect_resource"],
                    "controller" => $value["controller"],
                ];
            }
        }

        return false;
    }

    public static function apiRequest($url,$params_get,$params_post) {
        if (!empty($params_get)) {
            $url = $url."/?".http_build_query($params_get);
        }

        $http = curl_init();
        curl_setopt($http,CURLOPT_URL,$url);
        curl_setopt($http,CURLOPT_POST,1);
        curl_setopt($http,CURLOPT_POSTFIELDS,$params_post);
        curl_setopt($http,CURLOPT_HEADER,0);
        curl_setopt($http,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($http,CURLOPT_TIMEOUT,10);
        $output = curl_exec($http);
        curl_close($http);

        return json_decode($output,true);
    }
}

?>