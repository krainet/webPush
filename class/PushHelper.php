<?php
require_once __DIR__.'/../config/config.php';

class PushHelper{

    private $gcm_secret_key;
    /**
     * PushHelper constructor.
     * @param null $GCM_SECRET_KEY
     */
    public function __construct($GCM_SECRET_KEY = null)
    {
        if(empty($GCM_SECRET_KEY))
            $this->gcm_secret_key = API_ACCESS_KEY;
        else
            $this->gcm_secret_key = $GCM_SECRET_KEY;
    }

    public function sendOnePush($params,$regIds){
        $msg = array
        (
            'message'       => $params['message'],
            'title'         => $params['title'],
            'subtitle'      => $params['title'],
            'tickerText'    => $params['title'],
            'vibrate'       => 1,
            'sound'         => 1,
            'largeIcon'     => 'large_icon',
            'smallIcon'     => 'small_icon'
        );

        $fields = array
        (
            'registration_ids'      => $regIds,
            'data'                  => $msg
        );
        return $this->makeCall($fields);
    }

    private function makeCall($fields){
        $headers = array
        (
            'Authorization: key=' . $this->gcm_secret_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, GCM_URL );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        return $result;
    }
}