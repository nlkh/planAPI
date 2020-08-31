<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

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


if(isset($data->node_no) || !empty($data->node_no)):
    $node_no = $data->node_no;

    // RETURN AUTHORIZATION RESULT
    $authReturn = $auth->isAuth();
    if(isset($authReturn)) :
//         $plan_no = $authReturn['user']['plan_no'];

    try {
        // coding here
        $insert_query = "DELETE FROM node where node_no = :node_no";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bindValue(':node_no', $node_no);
        $insert_stmt->execute();
            
        $returnData = ['success' => 1, 'message' => '성공적으로 노드를 제거하였습니다.'];
        }
    catch(PDOException $e) {
        $returnData = ['success' => 0, 'message' => '서버에 연결할 수 없습니다.'];
    }
    endif;
endif;

echo json_encode($returnData,  JSON_UNESCAPED_UNICODE);
?>
