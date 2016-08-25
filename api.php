<?php

/**
 * Created by Develjitsu.com.
 * User: Ramón Albertí
 * Date: 25/08/16
 * Time: 15:54
 */

/**
 * Simple API to manage AJAX request to send message to GCM
 *
 * For the moment is not allowed to send data (unencrypted) in webPush, for this reason
 * you need to trigger a call to your own API to manage the push content
 *
 * Api functions (2) :
 *
 * 1.- Manage AJAX request and send the push event to GCM , and save the push_data(title,msg...) in a simple json file
 *
 * 2.- When service-worker recibe a push event, calls again to this api to obtain the push content (saved in json_file in this case)
 *
 * This is a simple example, you need to manage your own api logics to send correct push to any customer or globally
 */


require_once 'class/PushHelper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS,PUT, DELETE');
header("Access-Control-Allow-Headers: Content-Type");


$data = $_REQUEST;

if(!empty($data) && $data['action']){
    $action = $data['action'];
    if($action == 'send_push'){
        $ph = new PushHelper();
        $params = array(
            'title'     => $data['title'],
            'message'   => $data['message'],
            'icon'      => $data['icon'],
            'tag'       => md5($data['icon']),
        );

        $regID = array();
        $regID[] = $data['regID'];

        $response = $ph->sendOnePush($params,$regID);

        //hack (save push content for recovery in a get request
        $hand = fopen('tmp_push.json','w');
        fwrite($hand,json_encode($params));
        fclose($hand);

        echo $response;
    }

}else {
    /* GET Example request sending data for notification */
    $response = array(
        'success' => true,
        'notification' => null
    );

    /** read data from your remote api , in this case i'm read from fake file  */
    $hand = fopen('tmp_push.json','r');
    $line = fgets($hand);
    fclose($hand);

    if(!empty($line)){
        $data = json_decode($line,true);
    }

    $data_i = array(
        'title'     => empty($data['title']) ? 'Default title' : $data['title'],
        'message'   => empty($data['message']) ? 'Default message' : $data['message'],
        'icon'      => empty($data['icon']) ? 'images/mqu_logo.png' : $data['icon'],
        'tag'       => empty($data['tag']) ? md5('myRandomTag' . rand(0, 200)) : $data['tag']
    );

    $response['notification'] = $data_i;


    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
}