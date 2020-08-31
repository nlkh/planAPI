<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__.'/../member/classes/Database.php';
require_once __DIR__.'/../member/middlewares/Auth.php';

// GET HEADER(AUTHORIZATION ACCESS TOKEN)
$allHeaders = getallheaders();
$data = json_decode(file_get_contents("php://input"));

// DB CONNECTION
$db_connection = new Database();
$conn = $db_connection->dbConnection();


// AUTHORIZE ACCESS TOKEN
$auth = new Auth($conn,$allHeaders);

$returnData = [
    "success" => 0,
    "status" => 401,
    // "message" => "Unauthorized"
    "message" => '인증 실패'
];

if(isset($data->plan_no)
    || !empty(trim($data->plan_no))
    ):
    $plan_no = $data->plan_no;
    // RETURN AUTHORIZATION RESULT
    $authReturn = $auth->isAuth();
    if(isset($authReturn)) :
        $member_no = $authReturn['user']['member_no'];
    
    try {
        // coding here
        
        $insert_query = "DELETE from plan where plan_no=:plan_no";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bindValue(':plan_no', $plan_no, PDO::PARAM_INT);
        $insert_stmt->execute();
            
        $returnData = ['success' => 1, 'message' => '성공적으로 여행계획을 삭제했습니다.'];
        }
    catch(PDOException $e) {
        $returnData = ['success' => 0, 'message' => "서버와의 접속에 실패했습니다."];
    }
    endif;
endif;


echo json_encode($returnData,  JSON_UNESCAPED_UNICODE);
?>
