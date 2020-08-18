<?php

function getUserInfo($keyword)
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

function getUser($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT User.Name, User.Level, COUNT(*) AS Coupon
        FROM User
        JOIN Coupon
        ON User.UserIdx= Coupon.UserIdx AND Coupon.isUsed = 'N'
        WHERE UserId = ? AND isDeleted = 'N'
        GROUP BY User.UserIdx;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUserPoint($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Point, date_format(Date,'%Y-%m-%d') AS Date , EndDate, Point.IsDeleted As Used, Store
        FROM Point
        JOIN User ON User.UserId = ? AND Point.UserIdx = User.UserIdx
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

function getUserPointSum($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT CONCAT(sum(Point), '원') AS Sum FROM Point
        JOIN User ON UserId = ?
        WHERE User.UserIdx = Point.UserIdx AND Point.IsDeleted <= 0
        GROUP BY UserID;
        ";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getUserChoose($keyword, $flag)
{
    $pdo = pdoSqlConnect();

    if($flag == 2) {
        $query = "SELECT Store.Name, Store.Star, Store.Min, Store.Represent, Store.IsOpen, Store.IsOrder
        FROM Choose
        JOIN User on User.UserId = ?
        JOIN  Store
        ON Choose.UserIdx = User.UserIdx AND Choose.StoreIdx = Store.StoreIdx;";
    }
    else {
        $query = "SELECT Store.Name, Store.Star, Store.Min, Store.Represent, Store.IsOpen, Store.IsOrder
        FROM OrderMenu
        JOIN User on User.UserId = ?
        JOIN  Store
        ON OrderMenu.UserIdx = User.UserIdx AND OrderMenu.Payment = ? AND OrderMenu.StoreIdx = Store.StoreIdx;";
    }

    $st = $pdo->prepare($query);
    $st->execute([$keyword, $flag]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getStore($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT Store.Name, Star, Min,  Type, CONCAT(FORMAT(Store.Tip , 0), '원') AS Tip, Store.Phone, Store.DeliveryTime, Explan
        FROM Store
        WHERE Store.StoreID = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $res = array_filter($res[0]);
    return $res;
}

function getStoreReview($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(*) AS Review, COUNT(Review.Comment) AS Comments
        FROM Store
        JOIN Review
        ON Review.StoreIdx = Store.StoreIdx
        WHERE StoreId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getStoreChoose($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(*) As Choose
        FROM Store
        JOIN Choose
        ON Choose.StoreIdx = Store.StoreIdx
        WHERE StoreId = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getStoreMenu($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Menu.Name AS MenuName, CONCAT(FORMAT(Menu.Price , 0), '원') AS Price, Menu.Picture, Menu.IsPossible, Menu.MenuNum
        FROM Menu
        JOIN  Store
        ON Store.StoreID = 'mmmm' AND Menu.StoreIdx = Store.StoreIdx;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUserOrder($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT OrderMenu.Type,
            CASE
                WHEN TIMESTAMPDIFF(DAY, OrderMenu.Date, NOW()) < 1 THEN CONCAT('오늘')
                WHEN TIMESTAMPDIFF(DAY, OrderMenu.Date, NOW()) <= 7 THEN CONCAT(TIMESTAMPDIFF(DAY, OrderMenu.Date, NOW()), '일 전')
                ELSE CONCAT(date_format(OrderMenu.Date,'%m/%d'), ' (', SUBSTR( _UTF8'일월화수목금토', DAYOFWEEK(OrderMenu.Date), 1),')')
        END AS Date,
        Store.Category, Store.Name, OrderMenu.IsReview, CONCAT(FORMAT(Store.Tip , 0), '원') AS Tip,
               GROUP_CONCAT(Menu.Name) AS MenuName, CONCAT(FORMAT(SUM(Menu.Price) , 0), '원') AS MenuPrice, Store.StoreId, OrderMenu.OrderNumber, OrderMenu.OrderIdx
        FROM OrderMenu
        JOIN User on User.UserId = 'judy'
        JOIN  Store
        ON OrderMenu.UserIdx = User.UserIdx AND OrderMenu.Type <= 1 AND OrderMenu.StoreIdx = Store.StoreIdx
        JOIN  OrderMenuList
        ON OrderMenu.OrderNumber = OrderMenuList.OrderNumber
        JOIN  Menu
        ON Menu.MenuNum = OrderMenuList.MenuNum
        GROUP BY OrderMenu.Date, OrderMenu.OrderIdx
        ORDER BY OrderMenu.Date DESC ;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUserOrderDetail($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT
       CASE
           WHEN HOUR(OrderMenu.Date) < 12 THEN date_format(OrderMenu.Date,'%Y년 %m월 %d일 오전 %h:%i')
           ELSE date_format(OrderMenu.Date,'%Y년 %m월 %d일 오후 %h:%i')
       END AS Date,
        OrderMenu.OrderNumber,  Store.Name, CONCAT(FORMAT(Store.Tip , 0), '원') AS Tip, OrderMenu.IsDelivered, Store.Phone, OrderMenu.Payment, OrderMenu.Address, Store.StoreId
        FROM OrderMenu
        JOIN  Store
        ON OrderMenu.OrderNumber = ? AND OrderMenu.StoreIdx = Store.StoreIdx;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $res = array_filter($res[0]);
    return $res;
}

function getUserOrderMenu($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Menu.Name, CONCAT(FORMAT(Menu.Price , 0), '원') AS MenuPrice, OrderMenuList.MenuOption, OrderMenuList.MenuCnt
        FROM OrderMenu
        JOIN User on User.UserId = ?
        JOIN  OrderMenuList
        ON OrderMenu.UserIdx = User.UserIdx AND OrderMenu.OrderNumber = 'B0QE01AX5S' AND OrderMenuList.OrderNumber = OrderMenu.OrderNumber
        JOIN  Menu
        ON Menu.MenuNum = OrderMenuList.MenuNum;";

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

function getUserReviewCount($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT COUNT(*) AS MyReview
        FROM Review
        JOIN User on User.UserId = ?
        WHERE Review.UserIdx = User.UserIdx;";

    $st = $pdo->prepare($query);
    $st->execute([$keyword]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function getUserReview($keyword)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT Review.Contents, Review.Tag, GROUP_CONCAT(ReviewPicture.Picture SEPARATOR '|') AS Picture,
            CASE
                WHEN TIMESTAMPDIFF(DAY, Review.Date, NOW()) < 1 THEN CONCAT('방금 전')
                WHEN TIMESTAMPDIFF(DAY, Review.Date, NOW()) <= 1 THEN CONCAT('어제')
                WHEN TIMESTAMPDIFF(DAY, Review.Date, NOW()) <= 7 THEN CONCAT('이번 주')
                WHEN TIMESTAMPDIFF(DAY, Review.Date, NOW()) <= 29 THEN CONCAT(CAST(TIMESTAMPDIFF(DAY, Review.Date, NOW()) / 7 as unsigned), '주 전')
                WHEN TIMESTAMPDIFF(MONTH , Review.Date, NOW()) <= 1 THEN CONCAT('지난 달')
                WHEN TIMESTAMPDIFF(MONTH , Review.Date, NOW()) < 12 THEN CONCAT(TIMESTAMPDIFF(MONTH , Review.Date, NOW()), '개월 전')
                ELSE CONCAT('작년')
            END AS Date
             , Review.Star, Store.Name, Store.StoreId
        FROM Review
        JOIN User on User.UserId = 'judy'
        JOIN  Store
        ON Review.UserIdx = User.UserIdx AND Review.isDeleted = 'N' AND Review.StoreIdx = Store.StoreIdx
        JOIN ReviewPicture
        ON Review.ReviewIdx = ReviewPicture.ReviewIdx
        GROUP BY Review.ReviewIdx, Review.Date
        ORDER BY Review.Date DESC;";

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

function createUser($UserId, $UserPw, $Name, $Phone, $Email, $MailReceiving, $SmsReceiving){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User (UserId, UserPw, Name, Phone, Email, Level, MailReceiving, SmsReceiving)
 VALUES (?,?,?,?,?,0,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$UserId, $UserPw, $Name, $Phone, $Email, $MailReceiving, $SmsReceiving]);

    $st = null;
    $pdo = null;
}

function createStore($StoreId, $StorePw, $Name, $Type, $Category,
                    $OpenTime, $CloseTime, $IsDelivery, $IsOrder, $IsBmart,
                    $Represent, $Min, $Tip, $Phone, $Explan, $DeliveryTime,
                    $IsOpenList, $IsUltraCall, $Latitude, $Longitude){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Store (StoreId, StorePw, Name, Type, Category, IsDeleted,
                    OpenTime, CloseTime, IsOpen, IsDelivery, IsOrder, IsBmart,
                    Represent, Min, Tip, Phone, Explan, DeliveryTime,
                    IsOpenList, IsUltraCall, Latitude, Longitude)
 VALUES (?, ?, ?, ?, ?,'N',?, ?, 'Y', ?,?,?,?,?, ?,?,?,?,?,?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$StoreId, $StorePw, $Name, $Type, $Category,
        $OpenTime, $CloseTime, $IsDelivery, $IsOrder, $IsBmart,
        $Represent, $Min, $Tip, $Phone, $Explan, $DeliveryTime,
        $IsOpenList, $IsUltraCall, $Latitude, $Longitude]);

    $st = null;
    $pdo = null;
}

function isValidId($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM User WHERE UserId = ? AND IsDeleted = 'N') AS exist;";

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

function isValidStoreId($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM Store WHERE StoreId = ? AND IsDeleted = 'N') AS exist;";

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

function isValidNumber($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM OrderMenu WHERE OrderNumber = ?) AS exist;";

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

function idCheck($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM User WHERE UserId = ?) AS exist;";

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

//READ
function getUsers($keyword)
{
    $pdo = pdoSqlConnect();
    $query = "select * from testTable where name like concat('%', ?, '%');";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$keyword]); //파라미터 list 형태로 넣을 것
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//READ
function getUserDetail($no)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM testTable WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$no]); // 여기가 ? 변수, 꼭 리스트 안에 넣을것!
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

function isValidNo($no)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS (SELECT * FROM testTable WHERE no = ?) AS exist;";

    $st = $pdo->prepare($query);
    $st->execute([$no]); // 여기가 ? 변수, 꼭 리스트 안에 넣을것!
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    //echo json_encode($res);
    return intval($res[0]['exist']);
}

function isValidUser($id, $pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE userId= ? AND userPw = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}


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
