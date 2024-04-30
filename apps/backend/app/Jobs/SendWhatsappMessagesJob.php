<?php

namespace app\Jobs;

use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\WppconnectGroups;
use App\Models\WppconnectService;
use App\Models\WppconnectToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use WPPConnectTeam\Wppconnect\Facades\Wppconnect;
use Illuminate\Support\Facades\Redis;


class SendWhatsappMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $key;
    protected $session;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param string $messageId
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->url = config('wppconnect.defaults.base_uri');
        $this->key = config('wppconnect.defaults.secret_key');
        $this->session = "front";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $randomDelay = rand(0, 60);
        sleep($randomDelay);

        $messageId = $this->data['messageId'];
        $groupId = $this->data['groupId'];
        $groupMessageId = $this->data['groupMessageId'];

        $groupMessage = GroupMessage::where('id', $groupMessageId)->first();

        $message = Message::findOrFail($messageId);
        $service = WppconnectService::where('service_name', $this->session)->first();
        $serviceId = $service->id;
        $session = WppconnectToken::where('session_id', $service->id)->get();
        $sessionToken = WppconnectToken::where('session_id', $serviceId)->first();
        $messages = Message::where('id', $messageId)->first();
        $phone = WppconnectGroups::where('class_id',$groupId)->first();
        $serializedId = $phone->serialized_id;
        $messagesToSend = $messages->message;

        Wppconnect::make($this->url);
        $response = Wppconnect::to('/api/' . $this->session . '/send-message')->withBody([
            'phone' => $serializedId,
            'message' => $messagesToSend,
            'isGroup' => true
        ])->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $sessionToken->token
        ])->asJson()->post();
        $response = json_decode($response->getBody()->getContents(), true);

        if ($message->media == true) {

            $imagePath = $message->media;
            $imageContent = file_get_contents($imagePath);

            if ($imageContent !== false) {
                $base64Image = 'data:;base64,' . base64_encode($imageContent);
            }

            Wppconnect::make($this->url);
            $response = Wppconnect::to('/api/' . $this->session . '/send-file-base64')->withBody([
                'phone' => $serializedId,
                'base64' => $base64Image,
                'isGroup' => true
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $sessionToken->token
            ])->asJson()->post();
            $response = json_decode($response->getBody()->getContents(), true);
        }
    }
}
