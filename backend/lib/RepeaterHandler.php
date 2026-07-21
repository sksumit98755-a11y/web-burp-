<?php
class RepeaterHandler {
    public static function send($request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request['url']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request['method'] ?? 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request['headers'] ?? []);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request['body'] ?? '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'response' => $response,
            'error' => $error
        ];
    }
}
?>
