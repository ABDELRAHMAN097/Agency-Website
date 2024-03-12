<?php
// To Get The Code Use This
// https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id=1000.VMTXORDGZ322VABES1OR3N86HQICWE&scope=ZohoCRM.modules.ALL,ZohoCRM.users.ALL,ZohoBooks.fullaccess.all&redirect_uri=https://easetasks.com&access_type=offline&prompt=consent

function get_access_token($code = 0, $pdo)
{
    $fianl = '';
    if ($code == 0) {
        $sql = "SELECT access_token From app_info WHERE (record_id = 1)";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $access_token = $result["access_token"];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.zohoapis.com/crm/v2/Deals/(id:equals:1)");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Zoho-oauthtoken ' . $access_token,
                'Content-Type: application/x-www-form-urlencoded'
            ));
            $response = curl_exec($ch);
            $response = json_decode($response, true);
            if ($response['code'] == 'INVALID_TOKEN') {
                $sql = "SELECT refresh_token From app_info WHERE (record_id = 1)";
                $statement = $pdo->prepare($sql);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $refresh_token = $result["refresh_token"];
                    $post = [
                        'refresh_token' => $refresh_token,
                        'client_id' => '1000.VMTXORDGZ322VABES1OR3N86HQICWE',
                        'client_secret' => '2a2741875e2b0b5c719eedb98e5bdaac0bcfd935af',
                        'redirect_uri' => 'https://easetasks.com',
                        'grant_type' => 'refresh_token'
                    ];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://accounts.zoho.com/oauth/v2/token");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                    $response = curl_exec($ch);
                    $response = json_decode($response, true);
                    if (count($response) < 3) {
                        $fianl = $response['error'];
                    } else {
                        $fianl = $response['access_token'];
                        $sql = "UPDATE app_info SET access_token = :access_token WHERE (record_id = 1)";
                        $statement = $pdo->prepare($sql);
                        $statement->bindParam(':access_token', $fianl);
                        $statement->execute();
                    }
                }
            } else {
                $fianl = $access_token;
            }
        }
    } else {
        echo $code;
        $post = [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'https://easetasks.com',
            'client_id' => '1000.VMTXORDGZ322VABES1OR3N86HQICWE',
            'client_secret' => '2a2741875e2b0b5c719eedb98e5bdaac0bcfd935af',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://accounts.zoho.com/oauth/v2/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        print_r($response);
        if ($response['error'] == 'invalid_code') {
            $fianl = $response['error'];
        } else {
            $fianl = $response['access_token'];
            $refresh_token = $response['refresh_token'];
            $sql = "UPDATE app_info SET (access_token = :access_token,refresh_token=:refresh_token) WHERE (record_id = 1)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':access_token', $fianl);
            $statement->bindParam(':refresh_token', $refresh_token);
            $statement->execute();
        }
    }
    return $fianl;
};

// function get_records($Module, $code = 0, $pdo)
// {
//     $Access_Token = 0;
//     if ($code == 0) {
//         $Access_Token = get_access_token(0, $pdo);
//     } else {
//         $Access_Token = get_access_token($code, $pdo);
//     }
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, "https://www.zohoapis.com/crm/v2/" . $Module);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//         'Authorization: Zoho-oauthtoken ' . $Access_Token,
//         'Content-Type: application/x-www-form-urlencoded'
//     ));
//     $response = curl_exec($ch);
//     return $response;
// };

// function SearchRecords($Module, $code = 0, $pdo, $SearchCritera)
// {
//     $Access_Token = 0;
//     if ($code == 0) {
//         $Access_Token = get_access_token(0, $pdo);
//     } else {
//         $Access_Token = get_access_token($code, $pdo);
//     }
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, "https://www.zohoapis.com/crm/v2/" . $Module . "/" . "search?criteria=(" . $SearchCritera . ")");
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//         'Authorization: Zoho-oauthtoken ' . $Access_Token,
//         'Content-Type: application/x-www-form-urlencoded'
//     ));
//     $response = curl_exec($ch);
//     return $response;
// };

// function DownloadAttachment($Module_Name, $Record_ID, $pdo)
// {
//     $All_Attachments = json_decode(get_records("Attachments", $code = 0, $pdo), true)['data'];
//     $Final_Array = [];
//     foreach ($All_Attachments as $Object) {
//         if (gettype($Object) == "array") {

//             if ($Object['Parent_Id']['id'] == $Record_ID) {
//                 array_push($Final_Array, $Object['id']);
//             }
//         }
//     }
//     $Return_Array = [];
//     $Folder_Name = "photos/" . $Record_ID . "/";
//     if (file_exists($Folder_Name)) {
//         $files = glob($Folder_Name . "*"); // get all file names
//         foreach ($files as $file) { // iterate files
//             if (is_file($file)) {
//                 unlink($file);
//             }
//         }
//     } else {
//         mkdir($Folder_Name);
//     }
//     $i = 0;
//     if (count($Final_Array) > 0) {
//         foreach ($Final_Array as $Attachment_ID) {
//             $curl_pointer = curl_init();
//             $curl_options = array();
//             $url = "https://www.zohoapis.com/crm/v2/$Module_Name/$Record_ID/Attachments/$Attachment_ID";
//             $curl_options[CURLOPT_URL] = $url;
//             $curl_options[CURLOPT_RETURNTRANSFER] = true;
//             $curl_options[CURLOPT_HEADER] = 1;
//             $curl_options[CURLOPT_CUSTOMREQUEST] = "GET";
//             $headersArray = array();
//             $headersArray[] = "Authorization" . ":" . "Zoho-oauthtoken " . get_access_token(0, $pdo);
//             $curl_options[CURLOPT_HTTPHEADER] = $headersArray;
//             curl_setopt_array($curl_pointer, $curl_options);
//             $result = curl_exec($curl_pointer);
//             $responseInfo = curl_getinfo($curl_pointer);
//             curl_close($curl_pointer);
//             list($headers, $content) = explode("\r\n\r\n", $result, 2);
//             if (strpos($headers, " 100 Continue") !== false) {
//                 list($headers, $content) = explode("\r\n\r\n", $content, 2);
//             }
//             $headerArray = (explode("\r\n", $headers, 50));
//             $headerMap = array();
//             foreach ($headerArray as $key) {
//                 if (strpos($key, ":") != false) {
//                     $firstHalf = substr($key, 0, strpos($key, ":"));
//                     $secondHalf = substr($key, strpos($key, ":") + 1);
//                     $headerMap[$firstHalf] = trim($secondHalf);
//                 }
//             }
//             $response = $content;
//             if ($response == null && $responseInfo['http_code'] != 204) {
//                 list($headers, $content) = explode("\r\n\r\n", $content, 2);
//                 $response = json_decode($content, true);
//             }
//             $contentDisp = $headerMap['Content-Disposition'];
//             $fileName = substr($contentDisp, strrpos($contentDisp, "'") + 1, strlen($contentDisp));

//             if (strpos($fileName, "=") !== false) {
//                 $fileName = substr($fileName, strrpos($fileName, "=") + 1, strlen($fileName));

//                 $fileName = str_replace(array(
//                     '\'',
//                     '"'
//                 ), '', $fileName);
//             }
//             $random = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 40);
//             $allowed = array('gif', 'png', 'jpg', 'jpeg', 'tif');
//             $ext = pathinfo($fileName, PATHINFO_EXTENSION);
//             if (in_array($ext, $allowed)) {
//                 $filePath = $Folder_Name . $Record_ID . "_" . $random . "." . $ext;
//                 $fp = fopen($filePath, "w"); // $filePath - absolute path where downloaded file has to be stored.
//                 $stream = $response;
//                 fputs($fp, $stream);
//                 fclose($fp);
//                 array_push($Return_Array, $filePath);
//             }
//         }
//         return json_encode($Return_Array, true);
//     }
// }
