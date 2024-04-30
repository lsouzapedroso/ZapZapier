<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Models\GroupMessage;
use App\Models\WppconnectGroups;
use App\Models\WppconnectService;
use App\Models\WppconnectToken;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class MessagesController extends Controller
{
    protected $url;
    protected $key;
    protected $session;

    /**
     * __construct function
     */
    public function __construct()
    {
        $this->url = config('wppconnect.defaults.base_uri');
        $this->key = config('wppconnect.defaults.secret_key');
        $this->session = "front";
    }

    public function index()
    {
        $user = auth()->user();
        $accessLevel = $user->access_level;
        return view('communication.whatsapp.message',compact('accessLevel'));
    }

    public function store(Request $request)
    {
        // Valide os dados do formulário, se necessário

        $data = [
            'name' => $request->input('name'),
            'day_time' => $request->input('day_time'),
            'message' => $request->input('message'),
        ];

        if ($request->has('show_media_checkbox')) {
            $file = $request->file('media_input');

            if ($file->isValid()) {
                $extension = $file->extension();
                $fileName = md5($file->getClientOriginalName() . strtotime("now")) . "." . $extension;

                $destinationPath = public_path('midia/message'); // Caminho completo para o diretório de destino
                $file->move($destinationPath, $fileName);

                $data['media'] = 'public/midia/message/' . $fileName; // Concatenando o diretório ao nome do arquivo
            }
        }

        // Crie a nova mensagem
        $newMessage = Message::create($data);
        $newMessageId = $newMessage->id;


        return $this->selectGroups($newMessageId);
    }

    public function selectGroups($newMessageId)
    {
        $user = auth()->user();
        $accessLevel = $user->access_level;
        $groups = WppconnectGroups::with('classe')->get();

        return view('communication.whatsapp.select-groups',compact('accessLevel'))->with([
            'groups' => $groups,
            'newMessageId' => $newMessageId,
        ]);
    }

    public function saveMassage(Request $request)
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

    public function edit($id)
    {
        $user = auth()->user();
        $accessLevel = $user->access_level;
        $Message = Message::find($id);
        return view('communication.whatsapp.edit-message', compact('accessLevel'))->with('Message', $Message);

    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'day_time' => 'required|string',
            'message' => 'required|string',
            'media' => 'nullable|string',
        ]);

        $message = Message::find($id);

        if (!$message) {
            return redirect()->route('whatsapp-options')->with('error', 'Mensagem não encontrada.');
        }

        // Atualize os campos com os novos valores
        $message->update($validatedData);

        return redirect()->route('whatsapp-options')->with('success', 'Mensagem atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $message = Message::find($id);
        $messageGroup = GroupMessage::where('message_id', $id)->get();

        if (!$message) {
            return redirect()->route('whatsapp-options')->with('error', 'Mensagem não encontrada.');
        }
        $messageGroup->each->delete();
        $message->delete();

        return redirect()->route('whatsapp-options')->with('success', 'Mensagem deletada com sucesso.');
    }
}
