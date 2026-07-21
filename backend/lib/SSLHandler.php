<?php
class SSLHandler {
    private static $sslDir = __DIR__ . '/../../storage/ssl/';
    private static $certsDir = __DIR__ . '/../../storage/ssl/certs/';

    public static function generateCA() {
        if (!is_dir(self::$sslDir)) {
            mkdir(self::$sslDir, 0755, true);
        }
        if (!is_dir(self::$certsDir)) {
            mkdir(self::$certsDir, 0755, true);
        }

        $privkey = openssl_pkey_new(['private_key_bits' => 2048]);
        $csr = openssl_csr_new([
            'countryName' => 'US',
            'organizationName' => 'WebBurp Lab',
            'commonName' => 'WebBurp Root CA'
        ], $privkey);
        $cert = openssl_csr_sign($csr, null, $privkey, 3650);
        
        openssl_x509_export($cert, $certout);
        openssl_pkey_export($privkey, $keyout);
        
        file_put_contents(self::$sslDir . 'root-ca.crt', $certout);
        file_put_contents(self::$sslDir . 'root-ca.key', $keyout);
        
        return ['status' => 'success', 'message' => 'CA generated'];
    }

    public static function downloadCA() {
        $file = self::$sslDir . 'root-ca.crt';
        if (!file_exists($file)) {
            http_response_code(404);
            echo json_encode(['error' => 'CA not found']);
            exit;
        }
        header('Content-Type: application/x-x509-ca-cert');
        header('Content-Disposition: attachment; filename="web-burp-ca.crt"');
        readfile($file);
        exit;
    }

    public static function generateDomainCert($domain) {
        if (empty($domain)) {
            return ['status' => 'error', 'message' => 'Domain required'];
        }
        
        $caCrt = file_get_contents(self::$sslDir . 'root-ca.crt');
        $caKey = file_get_contents(self::$sslDir . 'root-ca.key');
        
        if (!$caCrt || !$caKey) {
            return ['status' => 'error', 'message' => 'Generate CA first'];
        }
        
        $privkey = openssl_pkey_new(['private_key_bits' => 2048]);
        $csr = openssl_csr_new(['commonName' => $domain], $privkey);
        $cert = openssl_csr_sign($csr, $caCrt, $caKey, 365);
        
        openssl_x509_export($cert, $certout);
        openssl_pkey_export($privkey, $keyout);
        
        file_put_contents(self::$certsDir . $domain . '.crt', $certout);
        file_put_contents(self::$certsDir . $domain . '.key', $keyout);
        
        return ['status' => 'success', 'message' => 'Certificate generated for ' . $domain];
    }
}
?>
