<?php

// API NO. 1 User 모든 정보 출력
function getUsers($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM User WHERE UserId = ? AND IsDeleted = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;


    $res = array_filter($res);

    return $res;
}

// API NO. 2 My배민 페이지의 User 정보 조회
function getUser($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT User.name, User.level, COUNT(*) AS coupon
        FROM User
        JOIN Coupon
        ON User.userIdx= Coupon.userIdx AND Coupon.isUsed = 'N'
        WHERE UserId = ? AND isDeleted = 'N'
        GROUP BY User.userIdx;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// API NO. 3 포인트 이용 내역 조회
function getUserPoint($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT point, date_format(date,'%Y-%m-%d') AS date , endDate, Point.isDeleted As used, store
        FROM Point
        JOIN User ON User.userId = ? AND Point.userIdx = User.userIdx
        ORDER BY Date DESC;
        ";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    for($i=0; $i<sizeof($res); $i++)
    {
        $res[$i] = array_filter($res[$i]);
    }

    return $res;
}

// API NO. 4 보유 포인트 합계 조회
function getUserPointSum($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT CONCAT(sum(point), '원') AS sum FROM Point
        JOIN User ON userId = ?
        WHERE User.userIdx = Point.userIdx AND Point.isDeleted >= 3
        GROUP BY userID;
        ";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// API NO. 5 찜한가게, 바로결제, 전화주문 목록 조회
function getUserChoose($keyword, $flag)
{
    $pdo = pdoSqlConnect();

    if($flag == 2) {
        $query = "SELECT Store.name, Store.star, Store.min, Store.represent, Store.isOpen, Store.isOrder, Store.storeId
        FROM Choose
        JOIN User on User.userId = ?
        JOIN  Store
        ON Choose.userIdx = User.userIdx AND Choose.storeIdx = Store.storeIdx;";

        $st = $pdo->prepare($query);
        $st->execute([$keyword]);
    }
    else {
        $query = "SELECT Store.name, Store.star, Store.min, Store.represent, Store.isOpen, Store.isOrder, Store.storeId
        FROM OrderMenu
        JOIN User on User.userId = ?
        JOIN  Store
        ON OrderMenu.userIdx = User.userIdx AND OrderMenu.payment = ? AND OrderMenu.storeIdx = Store.storeIdx;";

        $st = $pdo->prepare($query);
        $st->execute([$keyword, $flag]);
    }


    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// API NO. 6 Store 상세 정보
function getStore($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT Store.name, star, min,  type, CONCAT(FORMAT(Store.tip , 0), '원') AS tip, Store.phone, Store.deliveryTime, explan
        FROM Store
        WHERE Store.storeID = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $res = array_filter($res[0]);
    return $res;
}

// API NO. 7 Store 최근 리뷰 / 사장님 댓글 개수 조회
function getStoreReview($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(*) AS review, COUNT(Review.comment) AS comments
        FROM Store
        JOIN Review
        ON Review.storeIdx = Store.storeIdx
        WHERE storeId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// API NO. 8 Store 찜 개수 조회
function getStoreChoose($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(*) As choose
        FROM Store
        JOIN Choose
        ON Choose.storeIdx = Store.storeIdx
        WHERE storeId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// API NO. 9 Store 메뉴 목록 조회
function getStoreMenu($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Menu.Name AS menuName, CONCAT(FORMAT(Menu.price , 0), '원') AS price, Menu.picture, Menu.isPossible, Menu.menuNum
        FROM Menu
        JOIN  Store
        ON Store.storeID = 'mmmm' AND Menu.storeIdx = Store.storeIdx
        WHERE Menu.isDeleted = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// API NO. 10 User 주문 내역 목록 조회
function getUserOrder($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT OrderMenu.type,
                CASE
                    WHEN TIMESTAMPDIFF(DAY, OrderMenu.date, NOW()) < 1 THEN CONCAT('오늘')
                    WHEN TIMESTAMPDIFF(DAY, OrderMenu.date, NOW()) <= 7 THEN CONCAT(TIMESTAMPDIFF(DAY, OrderMenu.date, NOW()), '일 전')
                    ELSE CONCAT(date_format(OrderMenu.date,'%m/%d'), '(', SUBSTR( _UTF8'일월화수목금토', DAYOFWEEK(OrderMenu.date), 1),')')
            END AS date,
            Store.category, Store.name, OrderMenu.isReview, CONCAT(FORMAT(Store.tip , 0), '원') AS tip,
                   GROUP_CONCAT(Menu.name) AS menuName, CONCAT(FORMAT(SUM(Menu.price) , 0), '원') AS menuPrice, Store.storeId, OrderMenu.orderNumber
            FROM OrderMenu
            JOIN User on User.userId = ?
            JOIN  Store
            ON OrderMenu.userIdx = User.userIdx AND OrderMenu.type <= 1 AND OrderMenu.storeIdx = Store.storeIdx
            JOIN  OrderMenuList
            ON OrderMenu.orderNumber = OrderMenuList.orderNumber
            JOIN  Menu
            ON Menu.menuNum = OrderMenuList.menuNum
            WHERE OrderMenu.isDeleted = 'N'
            GROUP BY OrderMenu.date, OrderMenu.orderNumber
            ORDER BY OrderMenu.date DESC ;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// API NO. 11 User 상세 주문 내역 정보 조회
function getUserOrderDetail($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT
               CASE
                   WHEN HOUR(OrderMenu.date) < 12 THEN date_format(OrderMenu.date,'%Y년 %m월 %d일 오전 %h:%i')
                   ELSE date_format(OrderMenu.date,'%Y년 %m월 %d일 오후 %h:%i')
               END AS date,
        OrderMenu.orderNumber,  Store.name, CONCAT(FORMAT(Store.tip , 0), '원') AS tip, OrderMenu.isDelivered, Store.phone, OrderMenu.payment, OrderMenu.address, Store.storeId
        FROM OrderMenu
        JOIN  Store
        ON OrderMenu.orderNumber = ? AND OrderMenu.storeIdx = Store.storeIdx
        WHERE OrderMenu.isDeleted = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    if($res != null)
        $res = array_filter($res[0]);
    return $res;
}

// API NO. 12 User 주문 내역 메뉴 정보 조회
function getUserOrderMenu($userId)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Menu.name, CONCAT(FORMAT(Menu.price , 0), '원') AS menuPrice, OrderMenuList.menuOption, OrderMenuList.menuCnt
        FROM OrderMenu
        JOIN User on User.userId = ?
        JOIN  OrderMenuList
        ON OrderMenu.userIdx = User.userIdx AND OrderMenuList.orderNumber = OrderMenu.orderNumber
        JOIN  Menu
        ON Menu.menuNum = OrderMenuList.menuNum
        WHERE OrderMenu.isDeleted = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    for($i=0; $i<sizeof($res); $i++)
    {
        $res[$i] = array_filter($res[$i]);
    }

    return $res;
}

// API NO. 13 User 리뷰 개수 조회
function getUserReviewCount($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT COUNT(*) AS myReview
        FROM Review
        JOIN User on User.userId = ?
        WHERE Review.userIdx = User.userIdx;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// API NO. 14 User 리뷰 목록 조회
function getUserReview($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Review.contents, Review.tag, GROUP_CONCAT(ReviewPicture.picture SEPARATOR '|') AS picture,
            CASE
                WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) < 1 THEN CONCAT('방금 전')
                WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) <= 1 THEN CONCAT('어제')
                WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) <= 7 THEN CONCAT('이번 주')
                WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) <= 29 THEN CONCAT(CAST(TIMESTAMPDIFF(DAY, Review.date, NOW()) / 7 as unsigned), '주 전')
                WHEN TIMESTAMPDIFF(MONTH , Review.date, NOW()) <= 1 THEN CONCAT('지난 달')
                WHEN TIMESTAMPDIFF(MONTH , Review.date, NOW()) < 12 THEN CONCAT(TIMESTAMPDIFF(MONTH , Review.date, NOW()), '개월 전')
                ELSE CONCAT('작년')
            END AS date
             , Review.star, Store.name, Store.storeId, Review.reviewIdx
        FROM Review
        JOIN User on User.userId = ?
        JOIN  Store
        ON Review.userIdx = User.userIdx AND Review.isDeleted = 'N' AND Review.storeIdx = Store.storeIdx
        JOIN ReviewPicture
        ON Review.reviewIdx = ReviewPicture.reviewIdx
        GROUP BY Review.reviewIdx, Review.date
        ORDER BY Review.date DESC;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    for($i=0; $i<sizeof($res); $i++)
    {
        $res[$i] = array_filter($res[$i]);
    }

    return $res;
}

// API NO. 15 User 회원가입
function createUser($UserId, $UserPw, $Name, $Phone, $Email, $MailReceiving, $SmsReceiving){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User (userId, userPw, name, phone, email, level, mailReceiving, smsReceiving)
 VALUES (?,?,?,?,?,0,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$UserId, $UserPw, $Name, $Phone, $Email, $MailReceiving, $SmsReceiving]);

    $st = null;
    $pdo = null;
}

// API NO. 16 Store 회원가입
function createStore($StoreId, $StorePw, $Name, $Type, $Category,
                    $OpenTime, $CloseTime, $IsDelivery, $IsOrder, $IsBmart,
                    $Represent, $Min, $Tip, $Phone, $Explan, $DeliveryTime,
                    $IsOpenList, $IsUltraCall, $Latitude, $Longitude){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Store (storeId, storePw, name, type, category, isDeleted,
                    openTime, closeTime, isOpen, isDelivery, isOrder, isBmart,
                    represent, min, tip, phone, explan, deliveryTime,
                    isOpenList, isUltraCall, latitude, longitude)
 VALUES (?, ?, ?, ?, ?,'N',?, ?, 'Y', ?,?,?,?,?, ?,?,?,?,?,?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$StoreId, $StorePw, $Name, $Type, $Category,
        $OpenTime, $CloseTime, $IsDelivery, $IsOrder, $IsBmart,
        $Represent, $Min, $Tip, $Phone, $Explan, $DeliveryTime,
        $IsOpenList, $IsUltraCall, $Latitude, $Longitude]);

    $st = null;
    $pdo = null;
}

// API NO. 17 Store 메뉴 추가
function createStoreMenu($StoreIdx, $Name, $Picture, $Price, $MenuOption){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Menu (storeIdx, name, picture, price, isPossible, menuOption)
 VALUES (?, ?, ?, ?, 'Y', ?);";

    $st = $pdo->prepare($query);
    $st->execute([$StoreIdx, $Name, $Picture, $Price, $MenuOption]);

    $st = null;
    $pdo = null;
}

// API NO. 18 리뷰 추가
function createReview($UserIdx, $StoreIdx, $Tag, $Contents, $Star, $OrderNumber){

    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Review (userIdx, storeIdx, tag, contents, isDeleted, star, comment, commentIdx, orderNumber)
 VALUES (?, ?, ?, ?, 'N', ?, null, null, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$UserIdx, $StoreIdx, $Tag, $Contents, $Star, $OrderNumber]);

    $st = null;
    $pdo = null;
}

// API NO. 18 리뷰 사진 추가
function createReviewPicture($ReviewIdx, $Picture){

    $pdo = pdoSqlConnect();
    $query = "INSERT INTO ReviewPicture (reviewIdx, picture)
 VALUES (?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$ReviewIdx, $Picture]);

    $st = null;
    $pdo = null;
}

// API NO. 19 쿠폰 발급
function createCoupon($userIdx, $price, $coupon, $endDate, $storeIdx){
    $pdo = pdoSqlConnect();
    if($storeIdx == 0){
        $query = "INSERT INTO Coupon (userIdx, price, coupon, isUsed, endDate, storeIdx)
        VALUES (?, ?, ?, 'N', ?, 0);";

        $st = $pdo->prepare($query);
        $st->execute([$userIdx, $price, $coupon, $endDate]);
    }else {
        $query = "INSERT INTO Coupon (userIdx, price, coupon, isUsed, endDate, storeIdx)
        VALUES (?, ?, ?, 'N', ?, ?);";

        $st = $pdo->prepare($query);
        $st->execute([$userIdx, $price, $coupon, $endDate, $storeIdx]);
    }
    $st = null;
    $pdo = null;
}

// API NO. 20 User 보유 쿠폰 조회
function getUserCoupon($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT couponIdx, price, coupon, date_format(startDate,'%Y.%m.%d') AS startDate, date_format(endDate,'%Y.%m.%d') AS endDate
        FROM Coupon
        JOIN User ON User.userId = ? AND User.userIdx = Coupon.userIdx
        WHERE Coupon.isUsed = 'N';";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// API NO. 21 User 이번달 누적 주문 횟수
function getUserOrderCount($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT COUNT(*) AS orderCount, User.level
        FROM OrderMenu
        JOIN User ON User.userId = ? AND User.userIdx = OrderMenu.userIdx
        WHERE TIMESTAMPDIFF(MONTH , OrderMenu.date, NOW()) < 1
        GROUP BY User.userIdx;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// API NO. 22 User 음식주문
function createOrder($userIdx, $storeIdx, $address, $number, $toStoreMemo, $toRiderMemo, $payment, $type)
{
    $pdo = pdoSqlConnect();

    $query = "INSERT INTO OrderMenu(userIdx, storeIdx, address, number, toStoreMemo, toRiderMemo, payment, isDelivered, isReview, type, isDeleted)
        VALUES (?,?,?,?,?,?,?,'N','Y',?,'N');";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $storeIdx, $address, $number, $toStoreMemo, $toRiderMemo, $payment, $type]);

    $st = null;
    $pdo = null;
}

// API NO. 22 User 음식주문 - 메뉴 추가
function createOrderList($orderNum, $menuNum, $menuCnt, $menuOption)
{
    $pdo = pdoSqlConnect();

    $query = "INSERT INTO OrderMenuList(orderNumber, menuNum, menuCnt, menuOption)
        VALUES (?, ?, ?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$orderNum, $menuNum, $menuCnt, $menuOption]);

    $st = null;
    $pdo = null;

}

// API NO. 23 User 가게 찜 / 취소
function editChoose($userIdx, $storeIdx, $isDeleted)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Choose SET isDeleted = ?
        WHERE userIdx = ? AND storeIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$isDeleted, $userIdx, $storeIdx]);

    $st = null;
    $pdo = null;

}

// API NO. 23 User 가게 찜 생성
function createChoose($userIdx, $storeIdx, $isDeleted)
{
    $pdo = pdoSqlConnect();

    $query = "INSERT INTO Choose(userIdx, storeIdx, isDeleted)
        VALUES (?, ?, ?);";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $storeIdx, $isDeleted]);

    $st = null;
    $pdo = null;

}

// API NO. 24 User 리뷰 수정
function editUserReview($reviewIdx, $temp, $flag)
{
    $pdo = pdoSqlConnect();

    // flag = 0 : contents 수정
    // flag = 1 : star 수정
    if($flag == 0){
        $query = "UPDATE Review SET contents = ?
        WHERE reviewIdx = ?;";

        $st = $pdo->prepare($query);
        $st->execute([$temp, $reviewIdx]);
    }

    else if($flag == 1){
        $query = "UPDATE Review SET star = ?
        WHERE reviewIdx = ?;";

        $st = $pdo->prepare($query);
        $st->execute([$temp, $reviewIdx]);
    }

    $st = null;
    $pdo = null;

}

// API NO. 24 User 리뷰 수정
function deleteReviewPicture($reviewIdx)
{
    $pdo = pdoSqlConnect();

    $query = "DELETE FROM ReviewPicture WHERE reviewIdx = ?";

    $st = $pdo->prepare($query);
    $st->execute([$reviewIdx]);
    $st = null;
    $pdo = null;

}

// API NO. 25 Store 오픈 상태 변경
function editStoreOpen($storeIdx, $flag)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Store SET isOpen = ? WHERE storeIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$flag, $storeIdx]);
    $st = null;
    $pdo = null;
}

// API NO. 26 Store 메뉴 상태 변경(품절)
function editMenuPossible($menuNum, $flag)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Menu SET isPossible = ? WHERE menuNum = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$flag, $menuNum]);
    $st = null;
    $pdo = null;
}

// API NO. 27 Store 메뉴 상태 변경(이미지)
function editMenuPicture($menuNum, $image)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Menu SET picture = ? WHERE menuNum = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$image, $menuNum]);
    $st = null;
    $pdo = null;
}

// API NO. 28 User 쿠폰 사용
function useCoupon($couponIdx)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Coupon SET isUsed = 'Y' WHERE couponIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$couponIdx]);
    $st = null;
    $pdo = null;

}

// API NO. 29 가게 키워드 검색
function getStoreWord($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Store.name, Store.star, Store.min, Store.represent, Store.isOpen, Store.isOrder
        FROM Store
        WHERE Store.name LIKE CONCAT('%', ?, '%');";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// API NO. 30 User 리뷰 상세 조회
function getUserReviewDetail($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Review.contents, Review.tag, GROUP_CONCAT(ReviewPicture.picture SEPARATOR '|') AS picture,
                    CASE
                        WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) < 1 THEN CONCAT('방금 전')
                        WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) <= 1 THEN CONCAT('어제')
                        WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) <= 7 THEN CONCAT('이번 주')
                        WHEN TIMESTAMPDIFF(DAY, Review.date, NOW()) <= 29 THEN CONCAT(CAST(TIMESTAMPDIFF(DAY, Review.date, NOW()) / 7 as unsigned), '주 전')
                        WHEN TIMESTAMPDIFF(MONTH , Review.date, NOW()) <= 1 THEN CONCAT('지난 달')
                        WHEN TIMESTAMPDIFF(MONTH , Review.date, NOW()) < 12 THEN CONCAT(TIMESTAMPDIFF(MONTH , Review.date, NOW()), '개월 전')
                        ELSE CONCAT('작년')
                    END AS date
                     , Review.star, Store.name, Store.storeId, Review.reviewIdx
                FROM Review
                JOIN  Store
                ON Review.reviewIdx = ? AND Review.isDeleted = 'N' AND Review.storeIdx = Store.storeIdx
                JOIN ReviewPicture
                ON Review.reviewIdx = ReviewPicture.reviewIdx
                GROUP BY Review.reviewIdx, Review.date;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    for($i=0; $i<sizeof($res); $i++)
    {
        $res[$i] = array_filter($res[$i]);
    }

    return $res;
}

// API NO. 31 회원 정보 삭제
function deleteUser($userId)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE User SET isDeleted = 'Y' WHERE userId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$userId]);
    $st = null;
    $pdo = null;
}

// API NO. 32 가게 정보 삭제
function deleteStore($storeId)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Store SET isDeleted = 'Y' WHERE storeId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$storeId]);
    $st = null;
    $pdo = null;
}

// API NO. 33 회원 리뷰 삭제
function deleteUserReview($review_idx)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Review SET isDeleted = 'Y' WHERE reviewIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$review_idx]);
    $st = null;
    $pdo = null;
}

// API NO. 34 회원 주문내역 삭제
function deleteUserOrder($order_num)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE OrderMenu SET isDeleted = 'Y' WHERE orderNumber = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$order_num]);
    $st = null;
    $pdo = null;
}

// API NO. 35 가게 메뉴 삭제
function deleteStoreMenu($menu_num)
{
    $pdo = pdoSqlConnect();

    $query = "UPDATE Menu SET isDeleted = 'Y' WHERE menuNum = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$menu_num]);
    $st = null;
    $pdo = null;
}

// User Idx 가져오기
function getUserId($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT userIdx FROM User WHERE userId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// Store Idx 가져오기
function getStoreId($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT storeIdx FROM Store WHERE storeId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// choose 테이블에 있는지 확인
function isChoosed($userIdx, $storeIdx)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Choose WHERE userIdx = ? AND storeIdx = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$userIdx, $storeIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]['exist']);
}

// Review Idx 가져오기
function getReviewIdx($orderNum)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT reviewIdx
        FROM Review
        WHERE orderNumber = ?";

    $st = $pdo->prepare($query);
    $st->execute([$orderNum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// OrderNumber로 user 정보 가져오기
function getOrderNum($orderNum)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT userIdx, storeIdx
        FROM OrderMenu
        WHERE orderNumber = ?";

    $st = $pdo->prepare($query);
    $st->execute([$orderNum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// OrderNumber
function isValidOrderNum($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Review WHERE orderNumber = ? AND isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// OrderNumber 유효성 체크
function isCheckOrderNum($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM OrderMenu WHERE orderNumber = ? AND isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// Menu Number 유효성 체크
function isValidMenuNum($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Menu WHERE menuNum = ? AND isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// ID 체크
function isValidId($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM User WHERE userId = ? AND isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// Store Id 중복 체크
function isValidStoreId($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Store WHERE storeId = ? AND isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// OrderNumber 유효성 체크
function isValidNumber($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM OrderMenu WHERE orderNumber = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// ID 중복 체크
function idCheck($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM User WHERE userId = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// 동일한 메뉴가 있는지 확인
function checkMenu($storeIdx, $name)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Menu WHERE storeIdx = ? AND NAME = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$storeIdx, $name]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// ReviewIdx 유효성 체크
function isValidReviewIdx($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Review WHERE reviewIdx = ? AND isDeleted = 'N') AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// CouponIdx 유효성 체크
function isValidCouponIdx($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Coupon WHERE couponIdx = ? AND isUsed = 'N' AND TIMESTAMPDIFF(DAY, endDate, NOW()) < 1) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

// CouponIdx 사용 가능 가게 유효성 체크
function getCouponStore($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT storeIdx FROM Coupon WHERE couponIdx = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['storeIdx']);
}

// OrderNumber 인덱스 찾기
function findOrderNum()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT MAX(orderNumber) AS orderNumber From OrderMenu";

    $st = $pdo->prepare($query);
    $st->execute([]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['orderNumber']);
}

//// Test
//function getUsers($keyword)
//{
//    $pdo = pdoSqlConnect();
//    $query = "select * from testTable where name like concat('%', ?, '%');";
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$keyword]); //파라미터 list 형태로 넣을 것
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res;
//}
//
//// Test
//function getUserDetail($no)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT * FROM testTable WHERE no = ?;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$no]);
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return $res[0];
//}
//
//// Test
//function isValidNo($no)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS (SELECT * FROM testTable WHERE no = ?) AS exist;";
//
//    $st = $pdo->prepare($query);
//    $st->execute([$no]);
//    //    $st->execute();
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//    //echo json_encode($res);
//    return intval($res[0]['exist']);
//}
//
//// Test
//function isValidUser($id, $pw){
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT * FROM User WHERE userId= ? AND userPw = ?) AS exist;";
//
//
//    $st = $pdo->prepare($query);
//    //    $st->execute([$param,$param]);
//    $st->execute([$id, $pw]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st=null;$pdo = null;
//
//    return intval($res[0]["exist"]);
//
//}


// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
