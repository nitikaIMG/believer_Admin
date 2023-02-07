<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use DB;
// use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class CashfreeApiController extends Controller
{

    public $environment = 'PROD',
    $urls = [
        'TEST' => 'https://payout-gamma.cashfree.com',
        'PROD' => 'https://payout-api.cashfree.com',
    ];

    public function authorize_token()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urls[$this->environment] . "/payout/v1/authorize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                // test
                // "X-Client-Id: CF14892DK1NHY04Y2AAMA2",
                // "X-Client-Secret: 1144e06b4317a76e8dcf92670d8cc69821b130a9"
                "X-Client-Id: CF54553D039X0GN4B8MUQI",
                "X-Client-Secret: 7ee0681f359ccfce56802821cc00781c7a556d41",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    public function check_cashfree_payout_status()
    {
        $response = json_decode($this->authorize_token(), true);

        if ($response['status'] == 'SUCCESS') {
            $token = $response['data']['token'];
        } else {
            echo 'something went wrong';
            echo '<br/>';
            echo '<pre>';
            print_r($response);die;
        }

        // $transfer_id = '127317182';
        // $reference_id = '140443';

        $response_transfer_status = $this->check_transfer_status($token, $transfer_id);

        // if($response_transfer_status['status'] == 'SUCCESS') {
        //     $token = $response_transfer_status['data']['token'];
        // } else {
        //     echo 'something went wrong';
        //     echo '<br/>';
        //     echo '<pre>';
        //     print_r($response_transfer_status);die;
        // }

        echo $response_transfer_status;

    }

    public function transfer($token, $to_transfer)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urls[$this->environment] . "/payout/v1/requestTransfer",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $to_transfer,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
                "Content-Type: text/plain",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }

    public function check_transfer_status($token, $transfer_id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urls[$this->environment] . "/payout/v1/getTransferStatus?transferId=" . $transfer_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    public function add_beneficiary($token, $beneficiary)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urls[$this->environment] . "/payout/v1/addBeneficiary",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $beneficiary,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
                "Content-Type: text/plain",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }

    public function send_money()
    {

        $token_response = $this->authorize_token();

        $token_response = json_decode($token_response, true);

        if ($token_response['status'] == 'SUCCESS') {
            $token = $token_response['data']['token'];
        } else {
            echo 'something went wrong';die;
        }

        $beneficiary = array();
        $beneficiary["beneId"] = $beneId = "Praveen123";
        $beneficiary["name"] = "praveen soni";
        $beneficiary["email"] = "praveensoni.img@gmail.com";
        $beneficiary["phone"] = "7062334389";
        $beneficiary["bankAccount"] = "917062334389";
        $beneficiary["ifsc"] = "PYTM0123456";
        $beneficiary["address1"] = "Jaipur";
        $beneficiary["city"] = "Jaipur";
        $beneficiary["state"] = "Rajasthan";
        $beneficiary["pincode"] = "302016";

        $beneficiary = json_encode($beneficiary);

        // $beneficiary_response = $this->add_beneficiary($token, $beneficiary);

        $amount = '1.00';
        $transfer_id = '123123123';

        $to_transfer = array();
        $to_transfer["beneId"] = $beneId;
        $to_transfer["amount"] = $amount;
        $to_transfer["transferId"] = $transfer_id;

        $to_transfer = json_encode($to_transfer);

        $to_transfer_response = $this->transfer($token, $to_transfer);

        echo $to_transfer_response;
    }

    # payout cashfree for withdraw

    # webhook for cashfree payout
    public function cashfree_payout_webhook()
    {
        date_default_timezone_set('Asia/Kolkata');
        $data = $_POST;
        $signature = $_POST["signature"];
        unset($data["signature"]); // $data now has all the POST parameters except signature
        ksort($data); // Sort the $data array based on keys
        $postData = "";
        foreach ($data as $key => $value) {
            if (strlen($value) > 0) {
                $postData .= $value;
            }
        }
        // $hash_hmac = hash_hmac('sha256', $postData, $clientSecret, true);
        $hash_hmac = hash_hmac('sha256', $postData, '247cb7ed756efdadd90ef44182e662c4179160c0', true);

        $dta = array();
        $dta['data'] = implode('', $_POST);
        DB::table('withdraw_webhook')->insert($dta);

        // Use the clientSecret from the oldest active Key Pair.
        $computedSignature = base64_encode($hash_hmac);

        if ($signature == $computedSignature) {
            // Proceed based on $event

            // $withdraw_data = DB::table('withdraw')->where('transfer_id', $_POST['referenceId'])->first();
            $withdraw_data = DB::table('withdraw')
                ->where('withdraw.transfer_id', $_POST['transferId'])
                ->join('registerusers', 'registerusers.id', '=', 'withdraw.user_id')
                ->select('user_id', 'registerusers.email', 'registerusers.team', 'withdraw_request_id', 'withdraw.*')
                ->first();

            if (!empty($withdraw_data)) {

                if ($_POST['event'] == 'TRANSFER_SUCCESS' || $_POST['event'] == 'TRANSFER_ACKNOWLEDGED') {

                    $upstatus['status'] = 1;
                    $upstatus['comment'] = $_POST['event'];
                    $upstatus['referenceid'] = $_POST['referenceId'];
                    $upstatus['approved_date'] = date('Y-m-d H:i:s');
                    DB::table('withdraw')->where('transfer_id', $withdraw_data->transfer_id)->update($upstatus);

                    // $gst = array();
                    // $gst['status']  = 1;
                    // DB::table('gst_deduction')->where('withdrawid',$withdraw_data->id)->update($gst);
                    //update transaction
                    $transactionsdataup = array();
                    $transactionsdataup['paymentstatus'] = 'confirmed';
                    $transactionidd = DB::table('transactions')->where('transaction_id', $withdraw_data->withdraw_request_id)->update($transactionsdataup);

                    # mail send

                    $datamessage['email'] = $withdraw_data->email;
                    $datamessage['subject'] = 'Believer11  - Withdrawal Request Canceled';
                    // $datamessage['content'] = '<p><strong>Hello ' . ucwords($withdraw_data->team) . ' </strong></p>';
                    // $datamessage['content'] .= '<p>Your withdrawal request of ₹' . $withdraw_data->amount . ' has been approved successfully.</p>';
                    //$content.='<p><strong>'.$input['comment'].'</strong></p>';
                    // $datamessage['content'] .= '<p></p>';
                    // $content = Helpers::Mailbody1($datamessage['content'], $datamessage['email']);
                    // Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);

                    // $content = Htmlhelpersemail::withdrawApprove_email($withdraw_data->team, $input['amount']);
                    // Helpers::mailsentFormat($datamessage['email'], $datamessage['subject'], $content);

                    // $notificationdata['userid'] = $withdraw_data->user_id;
                    // $notificationdata['title'] = 'Withdraw Request Approved successfully of amount ₹' . $withdraw_data->amount;
                    // DB::table('notifications')->insert($notificationdata);
                    //notifications//
                    // $notificationdata['userid'] = $withdraw_data->user_id;
                    // $notificationdata['title'] = 'Withdraw Request Approved successfully of amount ₹' . $input['amount'];
                    // DB::connection('mysql2')->table('notifications')->insert($notificationdata);

                    // //push notifications//
                    // $titleget = 'Believer11  - Withdrawal Request Approved';
                    // Helpers::sendnotification($titleget, $notificationdata['title'], '', $finduserdetails->user_id);

                    // $titleget = 'Withdrawal Request Approved!';
                    // Helpers::sendnotification($titleget, $notificationdata['title'], '', $withdraw_data->user_id);
                    # mail send

                } else if ($_POST['event'] == 'TRANSFER_FAILED') {

                    $upstatus['status'] = 2;
                    $upstatus['comment'] = $_POST['reason'];
                    $upstatus['approved_date'] = date('Y-m-d H:i:s');

                    DB::table('withdraw')->where('transfer_id', $withdraw_data->transfer_id)->update($upstatus);
                    // DB::table('gst_deduction')->where('withdrawid', $withdraw_data->id)->delete();
                    DB::table('userbalance')
                        ->where('user_id', $withdraw_data->user_id)
                        ->increment('winning', $withdraw_data->amount);

                    //update transaction
                    $transactionsdataup = array();
                    $transactionsdataup['paymentstatus'] = 'failed';
                    $transactionidd = DB::table('transactions')->where('transaction_id', $withdraw_data->withdraw_request_id)->update($transactionsdataup);

                } else {

                    $upstatus['status'] = 2;
                    $upstatus['comment'] = !empty($_POST['reason']) ? $_POST['reason'] : $_POST['event'];
                    $upstatus['approved_date'] = date('Y-m-d H:i:s');
                    DB::table('withdraw')->where('transfer_id', $withdraw_data->transfer_id)->update($upstatus);
                    // DB::table('gst_deduction')->where('withdrawid', $withdraw_data->id)->delete();
                    DB::table('userbalance')
                        ->where('user_id', $withdraw_data->user_id)
                        ->increment('winning', $withdraw_data->amount);

                    //update transaction
                    $transactionsdataup = array();
                    $transactionsdataup['paymentstatus'] = 'failed';
                    $transactionidd = DB::table('transactions')->where('transaction_id', $withdraw_data->withdraw_request_id)->update($transactionsdataup);
                }

            }

        } else {
            // Reject this call
        }

    }
    # webhook for cashfree payout

}
