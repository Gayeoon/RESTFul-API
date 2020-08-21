<?php

require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/users', ['IndexController', 'getUsers']);
    $r->addRoute('GET', '/store', ['IndexController', 'getStoreWord']);

    $r->addRoute('GET', '/user/{user_id}', ['IndexController', 'getUser']);
    $r->addRoute('GET', '/user/{user_id}/point', ['IndexController', 'getUserPoint']);
    $r->addRoute('GET', '/user/{user_id}/point/sum', ['IndexController', 'getUserPointSum']);
    $r->addRoute('GET', '/user/{user_id}/choose', ['IndexController', 'getUserChoose']);
    $r->addRoute('GET', '/store/{store_id}', ['IndexController', 'getStore']);
    $r->addRoute('GET', '/store/{store_id}/review-cnt', ['IndexController', 'getStoreReview']);
    $r->addRoute('GET', '/store/{store_id}/choose-cnt', ['IndexController', 'getStoreChoose']);
    $r->addRoute('GET', '/store/{store_id}/menu', ['IndexController', 'getStoreMenu']);
    $r->addRoute('GET', '/user/{user_id}/order', ['IndexController', 'getUserOrder']);
    $r->addRoute('GET', '/user/{order_num}/order-detail', ['IndexController', 'getUserOrderDetail']);
    $r->addRoute('GET', '/user/{user_id}/order/menu', ['IndexController', 'getUserOrderMenu']);
    $r->addRoute('GET', '/user/{user_id}/review/count', ['IndexController', 'getUserReviewCount']);
    $r->addRoute('GET', '/user/{user_id}/review', ['IndexController', 'getUserReview']);
    $r->addRoute('GET', '/user/review/{review_idx}', ['IndexController', 'getUserReviewDetail']);
    $r->addRoute('GET', '/user/{user_id}/coupon', ['IndexController', 'getUserCoupon']);
    $r->addRoute('GET', '/user/{user_id}/order-cnt', ['IndexController', 'getUserOrderCount']);

    $r->addRoute('POST', '/user', ['IndexController', 'createUser']);
    $r->addRoute('POST', '/store', ['IndexController', 'createStore']);
    $r->addRoute('POST', '/store/menu', ['IndexController', 'createStoreMenu']);
    $r->addRoute('POST', '/user/review', ['IndexController', 'createReview']);
    $r->addRoute('POST', '/user/coupon', ['IndexController', 'createCoupon']);
    $r->addRoute('POST', '/user/order', ['IndexController', 'createOrder']);

    $r->addRoute('PATCH', '/user/{user_id}/choose', ['IndexController', 'editChoose']);
    $r->addRoute('PATCH', '/user/review/{review_idx}', ['IndexController', 'editUserReview']);
    $r->addRoute('PATCH', '/user/coupon/{coupon_idx}', ['IndexController', 'useCoupon']);
    $r->addRoute('PATCH', '/store/{store_id}/is-open', ['IndexController', 'editStoreOpen']);
    $r->addRoute('PATCH', '/store/{menu_num}/menu/possible', ['IndexController', 'editMenuPossible']);
    $r->addRoute('PATCH', '/store/{menu_num}/menu/picture', ['IndexController', 'editMenuPicture']);

    $r->addRoute('DELETE', '/user/{user_id}', ['IndexController', 'deleteUser']);
    $r->addRoute('DELETE', '/store/{store_id}', ['IndexController', 'deleteStore']);
    $r->addRoute('DELETE', '/user/review/{review_idx}', ['IndexController', 'deleteUserReview']);
    $r->addRoute('DELETE', '/user/order/{order_num}', ['IndexController', 'deleteUserOrder']);
    $r->addRoute('DELETE', '/store/menu/{menu_num}', ['IndexController', 'deleteStoreMenu']);

    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']);
   // $r->addRoute('GET', '/users', ['IndexController', 'getUsers']);
   // $r->addRoute('GET', '/users/{no}', ['IndexController', 'getUserDetail']);
    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);



//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            /*case 'EventController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/EventController.php';
                break;
            case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
