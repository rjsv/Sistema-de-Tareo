<?php
/**
 * Created by PhpStorm.
 * User: DEXTER
 * Date: 23/11/17
 * Time: 6:07 PM
 */

namespace App\Traits;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

trait AppBoot{


    public function isLegal(){
        $legalFile = storage_path().'/legal';

        if(file_exists($legalFile)){
            $legalFileInfo = File::get($legalFile);

            $legalFileInfo = explode('**', $legalFileInfo);
            $domain = $legalFileInfo[0];
            $purchaseCode = $legalFileInfo[1];
            $envatoItemId = config('app.envato_item_id');

            //verify purchase
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://worksuite.biz/verify-purchase/verify-envato-purchase.php");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                "purchaseCode=$purchaseCode&domain=$domain&itemId=$envatoItemId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            $response = json_decode($server_output, true);
            curl_close ($ch);

            if($response['status'] == 'success'){
                return true;
            }
            else{
                return false;
            }
            return false;
        }
        return false;
    }

}