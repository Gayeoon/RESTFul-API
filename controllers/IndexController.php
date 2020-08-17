<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
 //   addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 1
         * API Name : User 모든 정보 출력 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getUserInfo":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserInfo($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 2
         * API Name : MY배민 페이지의 User 정보 출력 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getUser":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUser($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 3
         * API Name : 포인트 이용 내역 조회 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getUserPoint":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserPoint($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 4
         * API Name : 보유 포인트 합계 조회 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getUserPointSum":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserPointSum($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 5
         * API Name : 찜한가게, 바로결제, 전화주문 목록 조회 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getUserChoose":
            http_response_code(200);

            $keyword = $_GET['userId'];
            $flag = $_GET['flag'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($flag > 2){
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "유효하지 않은 flag입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserChoose($keyword, $flag);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 6
         * API Name : Store 상세 정보 출력 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getStore":
            http_response_code(200);

            $keyword = $_GET['storeId'];

            if(!isValidStoreId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getStore($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 7
         * API Name : Store 최근리뷰 / 사장님 댓글 개수 조회 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getStoreReview":
            http_response_code(200);

            $keyword = $_GET['storeId'];

            if(!isValidStoreId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getStoreReview($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

         /*
         * API No. 8
         * API Name : Store 찜 개수 조회 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getStoreChoose":
            http_response_code(200);

            $keyword = $_GET['storeId'];

            if(!isValidStoreId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getStoreChoose($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 9
         * API Name : Store 메뉴 목록 조회 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getStoreMenu":
            http_response_code(200);

            $keyword = $_GET['storeId'];

            if(!isValidStoreId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getStoreMenu($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


         /*
         * API No. 10
         * API Name : User 주문 내역 목록 조회 API
         * 마지막 수정 날짜 : 20.08.14
         */
        case "getUserOrder":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserOrder($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 11
        * API Name : User 상세 주문 내역 정보 조회 API
        * 마지막 수정 날짜 : 20.08.17
        */
        case "getUserOrderDetail":
            http_response_code(200);

            $keyword = $_GET['orderNum'];

            if(!isValidNumber($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 주문 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserOrderDetail($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 12
        * API Name : User 주문 내역 메뉴 정보 조회 API
        * 마지막 수정 날짜 : 20.08.17
        */
        case "getUserOrderMenu":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserOrderMenu($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 13
        * API Name : User 리뷰 개수 조회 API
        * 마지막 수정 날짜 : 20.08.17
        */
        case "getUserReviewCount":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserReviewCount($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 14
        * API Name : User 리뷰 목록 조회 API
        * 마지막 수정 날짜 : 20.08.17
        */
        case "getUserReview":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserReview($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 15
        * API Name :
        * 마지막 수정 날짜 : 20.08.17
        */
        case "createUser":
            http_response_code(200);

            if(idCheck($req->UserId)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "이미 존재하는 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            createUser($req->UserId, $req->UserPw, $req->Name, $req->Phone, $req->Email, $req->MailReceiving, $req->SmsReceiving, $req->IsDeleted, $req->Latitude, $req->Longitude);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "getUsers":
            http_response_code(200);

            $keyword = $_GET['keyword'];

            $res->result = getUsers($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "getUserDetail":
            http_response_code(200);

            $no = $vars["no"];

            if(!isValidNo($no)){
                $res->isSuccess = FALSE;
                $res->code = 100;
                $res->message = "유효하지 않은 no입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserDetail($vars["no"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
