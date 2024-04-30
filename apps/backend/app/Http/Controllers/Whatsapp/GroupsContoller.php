<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\WppconnectGroups;
use App\Models\WppconnectService;
use Illuminate\Http\Request;

class GroupsContoller extends Controller
{
    protected $session;
    /**
     * __construct function
     */
    public function __construct()
    {
        $this->session = "front";
    }
    public function index()
    {
        $user = auth()->user();
        $accessLevel = $user->access_level;
        $Messages = Message::All();
        return view('communication.whatsapp.groups', ['Messages' => $Messages], compact('accessLevel'));
    }
    public function edit($id)
    {
        $user = auth()->user();
        $accessLevel = $user->access_level;
        $groups = WppconnectGroups::with('classe')->get();
        $messageGroup = GroupMessage::where('message_id', $id)->get();
        $messageGroup->each->delete();

        return view('communication.whatsapp.edit-select-groups',compact('accessLevel'))->with([
            'groups' => $groups,
            'MessageId' => $id,
        ]);

    }

    public function update(Request $request, $id)
    {
        $selectedGroups = $request->input('selected_groups');
        $newMessageId = $request->input('new_message_id');
        $service = WppconnectService::where('service_name', $this->session)->first();
        $session = $service->id;

        foreach ($selectedGroups as $groupId) {
            GroupMessage::create([
                'group_id' => $groupId,
                'message_id' => $newMessageId,
                'service_id' => $session,
                'send' => false,
            ]);
        }

        return redirect()->route('whatsapp-options')->with('success', 'Mensagem criada com sucesso!');
    }

}
