<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

$route = $_GET['route'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($route) {
    case 'proxy/start':
        require_once 'lib/ProxyHandler.php';
        echo json_encode(ProxyHandler::start($input['port'] ?? 8080));
        break;
    case 'proxy/stop':
        require_once 'lib/ProxyHandler.php';
        echo json_encode(ProxyHandler::stop());
        break;
    case 'proxy/intercept':
        require_once 'lib/ProxyHandler.php';
        echo json_encode(ProxyHandler::toggleIntercept($input['enabled'] ?? false));
        break;
    case 'proxy/capture':
        require_once 'lib/ProxyHandler.php';
        echo json_encode(ProxyHandler::getCaptured());
        break;
    case 'repeater/send':
        require_once 'lib/RepeaterHandler.php';
        echo json_encode(RepeaterHandler::send($input));
        break;
    case 'intruder/start':
        require_once 'lib/IntruderHandler.php';
        echo json_encode(IntruderHandler::otpBruteforce($input));
        break;
    case 'bypass/run':
        require_once 'lib/BypassHandler.php';
        echo json_encode(BypassHandler::runAll($input));
        break;
    case 'ssl/generate':
        require_once 'lib/SSLHandler.php';
        echo json_encode(SSLHandler::generateCA());
        break;
    case 'ssl/download':
        require_once 'lib/SSLHandler.php';
        SSLHandler::downloadCA();
        break;
    case 'ssl/domain':
        require_once 'lib/SSLHandler.php';
        echo json_encode(SSLHandler::generateDomainCert($input['domain']));
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
}
?>
