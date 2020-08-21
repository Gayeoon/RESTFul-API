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
            echo "This is Gayeon's API Server";
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
        case "getUsers":
            http_response_code(200);

            $keyword = $_GET['userId'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUsers($keyword);
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

            $keyword = $vars["user_id"];

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

            $keyword = $vars['user_id'];

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

            $keyword = $vars['user_id'];

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

            $keyword = $vars['user_id'];
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

            $keyword = $vars['store_id'];

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

            $keyword = $vars['store_id'];

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

            $keyword = $vars['store_id'];

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

            $keyword = $vars['store_id'];

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

            $keyword = $vars['user_id'];

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

            $keyword = $vars['order_num'];

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

            $keyword = $vars['user_id'];

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

            $keyword= $vars['user_id'];

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

            $keyword = $vars['user_id'];

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
        * API Name : User 회원가입 API
        * 마지막 수정 날짜 : 20.08.17
        */
        case "createUser":
            http_response_code(200);

            if($req->userId == null) {
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "아이디 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

                if(idCheck($req->userId)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "이미 존재하는 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->userPw == null){
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "비밀번호 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->phone == null){
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "사용자 번호 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->email == null){
                $res->isSuccess = FALSE;
                $res->code = 600;
                $res->message = "이메일 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if ($req->name == null){
                $name = $req->userId;
                createUser($req->userId, $req->userPw, $name, $req->phone, $req->email, $req->mailReceiving, $req->smsReceiving);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "테스트 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            createUser($req->UserId, $req->UserPw, $req->Name, $req->Phone, $req->Email, $req->MailReceiving, $req->SmsReceiving);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 16
        * API Name : Store 회원가입 API
        * 마지막 수정 날짜 : 20.08.18
        */
        case "createStore":
            http_response_code(200);
            if($req->storeId == null) {
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "아이디 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(isValidStoreId($req->storeId)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "이미 존재하는 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if($req->storePw == null){
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "비밀번호 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->phone == null){
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "Store 번호 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->type == null){
                $res->isSuccess = FALSE;
                $res->code = 600;
                $res->message = "Type 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->name == null){
                $res->isSuccess = FALSE;
                $res->code = 700;
                $res->message = "Store 이름 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->type == null){
                $res->isSuccess = FALSE;
                $res->code = 800;
                $res->message = "Category 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->openTime == null || $req->closeTime == null){
                $res->isSuccess = FALSE;
                $res->code = 900;
                $res->message = "Open / Close 시간 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            createStore($req->storeId, $req->storePw, $req->name, $req->type, $req->category,
                $req->openTime, $req->closeTime, $req->isDelivery, $req->isOrder, $req->isBmart,
                $req->represent, $req->min, $req->tip, $req->phone, $req->explan, $req->deliveryTime,
                $req->isOpenList, $req->isUltraCall, $req->latitude, $req->longitude);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 17
        * API Name : Store Menu 추가 API
        * 마지막 수정 날짜 : 20.08.18
        */
        case "createStoreMenu":
            http_response_code(200);
            if($req->storeId == null) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "아이디 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(!isValidStoreId($req->storeId)){
                $res->isSuccess = FALSE;
                $res->code = 600;
                $res->message = "존재하지 않는 Store ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $storeIdx = getStoreId($req->storeId);

            if(checkMenu($storeIdx['StoreIdx'], $req->name)){
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "해당 가게에 동일한 메뉴가 있습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->name == null) {
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "메뉴 이름 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->price == null) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "가격 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            createStoreMenu($storeIdx['storeIdx'], $req->name, $req->picture, $req->price, $req->menuOption);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 18
        * API Name : User 리뷰쓰기 API
        * 마지막 수정 날짜 : 20.08.20
        */
        case "createReview":
            http_response_code(200);

            if($req->orderNum == null) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "주문 번호 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if(isValidOrderNum($req->orderNum)){
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "이미 리뷰가 존재합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $temp = getOrderNum($req->orderNum);
            $userIdx = $temp['userIdx'];
            $storeIdx = $temp['storeIdx'];


            if($req->contents == null) {
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "contents 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            createReview($userIdx,  $storeIdx, $req->tag, $req->contents, $req->star, $req->orderNum);

            if($req->picture != null){
                $reviewIdx = getReviewIdx($req->orderNum);

                for($t=0; $t<sizeof($req->picture); $t++)
                {
                    createReviewPicture($reviewIdx['reviewIdx'], $req->picture[$t]);
                }

            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
       * API No. 19
       * API Name : User 쿠폰발급 API
       * 마지막 수정 날짜 : 20.08.20
       */
        case "createCoupon":
            http_response_code(200);

            if($req->userId == null) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "아이디 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->price == null){
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "가격 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->coupon == null){
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "쿠폰 내용 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $userIdx = getUserId($req->userId);

            if($req->storeId ==  null){
                createCoupon($userIdx['userIdx'], $req->price, $req->coupon, $req->endDate, 0);
            }else{
                $storeIdx = getStoreId($req->storeId);
                createCoupon($userIdx['userIdx'], $req->price, $req->coupon, $req->endDate, $storeIdx['storeIdx']);
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
        * API No. 20
        * API Name : User 보유 쿠폰 조회 API
        * 마지막 수정 날짜 : 20.08.20
        */
        case "getUserCoupon":
            http_response_code(200);

            $keyword = $vars['user_id'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserCoupon($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 21
        * API Name : User 이번달 누적 주문 횟수 API
        * 마지막 수정 날짜 : 20.08.20
        */
        case "getUserOrderCount":
            http_response_code(200);

            $keyword = $vars['user_id'];

            if(!isValidId($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserOrderCount($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
       * API No. 22
       * API Name : User 음식 주문 API
       * 마지막 수정 날짜 : 20.08.20
       */
        case "createOrder":
            http_response_code(200);

            if($req->userId == null) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "User 아이디 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req->storeId == null) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "Store 아이디 값이 누락되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $userIdx = getUserId($req->userId);
            $storeIdx = getStoreId($req->storeId);

            $toStoreMemo = $req->toStoreMemo;
            if($req->toStoreMemo == null)
                $toStoreMemo = "(없음)";

            createOrder($userIdx['userIdx'],  $storeIdx['storeIdx'], $req->address, $req->number, $toStoreMemo, $req->toRiderMemo, $req->payment, $req->type);

            $orderNum = findOrderNum();

             for($t=0; $t<sizeof($req->menuNum); $t++)
                {
                    createOrderList($orderNum, $req->menuNum[$t], $req->menuCnt[$t], $req->menuOption[$t]);
                }


            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 23
        * API Name : User 가게 찜 / 찜 취소 API
        * 마지막 수정 날짜 : 20.08.21
        */
        case "editChoose":
            http_response_code(200);

            $keyword = $vars['user_id'];

            if(!isValidStoreId($req -> storeId)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 Store ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $userIdx = getUserId($keyword)['userIdx'];
            $storeIdx = getStoreId($req->storeId)['storeIdx'];

            $ans = isChoosed($userIdx, $storeIdx);

            if($req -> status == 0 && $ans == 0){
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "찜이 되어있지 않는 Store ID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req -> status == 0 && $ans == 1){
                editChoose($userIdx, $storeIdx, 'Y');
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "정보 수정 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req -> status == 1 && $ans == 1){
                editChoose($userIdx, $storeIdx, 'N');
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "정보 수정 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req -> status == 1 && $ans == 0){
                createChoose($userIdx, $storeIdx, 'N');
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "정보 수정 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            break;

        /*
        * API No. 24
        * API Name : User 리뷰 수정 API
        * 마지막 수정 날짜 : 20.08.21
        */
        case "editUserReview":
            http_response_code(200);

            $reviewIdx = $vars['review_idx'];

            if(!isValidReviewIdx($reviewIdx)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 Review Idx입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            if($req -> contents != null){
                editUserReview($reviewIdx, $req -> contents, 0);
            }

            if($req -> star != null){
                editUserReview($reviewIdx, $req -> star, 1);
            }

            if($req->picture != null){
                deleteReviewPicture($reviewIdx);

                for($t=0; $t<sizeof($req->picture); $t++)
                {
                    createReviewPicture($reviewIdx, $req->picture[$t]);
                }
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 수정 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 28
        * API Name : 해당 단어가 포함되는 가게 검색 API
        * 마지막 수정 날짜 : 20.08.20
        */
        case "getStoreWord":
            http_response_code(200);

            $keyword = $_GET['word'];

            if($keyword == null){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "키워드가 입력되지 않았습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getStoreWord($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
        * API No. 29
        * API Name : User 리뷰 목록 조회 API
        * 마지막 수정 날짜 : 20.08.17
        */
        case "getUserReviewDetail":
            http_response_code(200);

            $keyword = $vars['review_idx'];
            if(!isValidReviewIdx($keyword)){
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "유효하지 않은 리뷰 Idx입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            $res->result = getUserReviewDetail($keyword);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "정보 출력 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
