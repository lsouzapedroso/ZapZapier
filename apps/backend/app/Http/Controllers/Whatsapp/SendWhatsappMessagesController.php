<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappMessagesJob;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\WppconnectGroups;
use App\Models\WppconnectService;
use App\Models\WppconnectToken;
use Illuminate\Support\Facades\DB;
use WPPConnectTeam\Wppconnect\Facades\Wppconnect;
use Carbon\Carbon;


class SendWhatsappMessagesController extends Controller
{
    protected $url;
    protected $key;
    protected $session;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->url = config('wppconnect.defaults.base_uri');
        $this->key = config('wppconnect.defaults.secret_key');
        $this->session = "front";
    }

    public function send()
    {

        $service = WppconnectService::where('service_name', $this->session)->first();
        $serviceId = $service->id;
        $session = WppconnectToken::where('session_id', $service->id)->get();
        $sessionToken = WppconnectToken::where('session_id', $serviceId)->first();

        if ($sessionToken == NULL || $sessionToken->init == 0) {
            $responseData['status'] = false;
        } else {
            Wppconnect::make($this->url);
            $response = Wppconnect::to('/api/' . $this->session . '/check-connection-session')->withHeaders([
                'Authorization' => 'Bearer ' . $sessionToken->token
            ])->asJson()->get();
            $responseData = json_decode($response->getBody()->getContents(), true);
        }

        if ($responseData['status']) {
            //$messagesToSend = Message::whereRaw(DB::raw('TIMESTAMPDIFF(MINUTE, day_time, NOW()) >= 1'))->get();
            $groupsMessage = GroupMessage::all();
            foreach ($groupsMessage as $groupMessage) {
                if ($groupMessage && !$groupMessage->send) {
                    $message_id = $groupMessage->message_id;
                    $groupId = $groupMessage->group_id;
                    $groupMessageId = $groupMessage->id;
                    $messagesToSend = Message::where('id', $message_id)->first();
                    $timeTosend = $messagesToSend->day_time;
                    $currentDateTime = Carbon::now();
                    if ($timeTosend <= $currentDateTime) {
                        if ($sessionToken != null && $sessionToken->init == 1) {
                            $data = [

                                'messageId' => $message_id,
                                'groupId' => $groupId,
                                'groupMessageId'=> $groupMessageId
                            ];
                            SendWhatsappMessagesJob::dispatch($data);
                            $groupMessage->update(['send' => true]);
                        }
                    }
                }
            }
        }
    }
}
