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

if((isset($data->content_id)
    || !empty($data->content_id))&&(
       isset($data->content_type)
    || !empty($data->content_type))&&(
        isset($data->plan_no)
    || !empty($data->plan_no))):
       
    $content_type = $data->content_type;
    $content_id = $data->content_id;
    $title = $data->title;
    $thumbnail = $data->thumbnail;
    $plan_no = $data->plan_no;
    // RETURN AUTHORIZATION RESULT
    $authReturn = $auth->isAuth();
    if(isset($authReturn)) :
//         $plan_no = $authReturn['user']['plan_no'];

    try {
        // coding here
        
        $insert_query = "INSERT INTO node(content_type, content_id, title, thumbnail, plan_no) values(:content_type, :content_id, :title, :thumbnail, :plan_no);";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bindValue(':plan_no', $plan_no, PDO::PARAM_INT);
        $insert_stmt->bindValue(':content_type', $content_type, PDO::PARAM_INT);
        $insert_stmt->bindValue(':content_id', $content_id, PDO::PARAM_INT);
        $insert_stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $insert_stmt->bindValue(':thumbnail', $thumbnail, PDO::PARAM_STR);
        $insert_stmt->execute();
           
        $returnData = ['success' => 1, 'message' => '성공적으로 노드를 추가하였습니다.'];
        }
    catch(PDOException $e) {
        $returnData = ['success' => 0, 'message' => '서버에 연결할 수 없습니다.'];
    }
    endif;
endif;

echo json_encode($returnData,  JSON_UNESCAPED_UNICODE);
?>
