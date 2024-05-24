<?php

namespace App\Http\Controllers\vendor\Chatify;

use App\Models\ChFavorite as Favorite;
use App\Models\ChMessage as Message;
use App\Models\User;
use App\Models\Cases;
use App\Models\UserCourse;
use App\Models\Utility;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Exception;
// Client
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;

use App\Http\Helper\EncryptionHelper;
use App\Models\Course;

class MessagesController extends Controller
{

    public $perPage = 150;
    /**
     * Authinticate the connection for pusher
     *
     * @param Request $request
     *
     * @return void
     */
    public function pusherAuth(Request $request)
    {

        // Auth data
        $authData = json_encode(
            [
                'user_id' => Auth::user()->id,
                'user_info' => [
                    'name' => Auth::user()->name,
                ],
            ]
        );
        // check if user authorized

        if (Auth::check()) {
            return Chatify::pusherAuth(
                $request->user(),
                Auth::user(),
                $request['channel_name'],
                $request['socket_id']
            );
        }

        // if not authorized
        return new Response('Unauthorized', 401);
    }

    /**
     * Returning the view of the app with the required data.
     *
     * @param int $id
     *
     * @return void
     */
    public function index($id = null)
    {

        if (Auth::user()->type != 'admin') {
            // get current route
            $routeName = FacadesRequest::route()->getName();
            $route = (in_array(
                $routeName,
                ['user', config('chatify.routes.prefix')]
            )) ? 'user' : $routeName;

            // prepare id
            return view(
                'Chatify::pages.app',
                [
                    'id' => ($id == null) ? 0 : $route . '_' . $id,
                    'route' => $route,
                    'messengerColor' => Auth::user()->messenger_color,
                    'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
                ]
            );
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Fetch data by id for (user/group)
     *
     * @param Request $request
     *
     * @return collection
     */
    public function idFetchData(Request $request)
    {
        if ($request['callFunction'] != '') {
            $callFunction = $request['callFunction'];
            return $this->$callFunction($request);
        }

        $userid = trim($request['id'], 'user_');
        // Favorite
        $favorite = Chatify::inFavorite($userid);

        // User data
        if ($request->type == 'groups') {
            $fetch = Cases::where('id', $userid)->first();
            $encryptionHelper = new EncryptionHelper();
            $fetch->name = $encryptionHelper->decryptAES($fetch->name);
            // dd($fetch);
            $fetch->avatar = NULL;
        } else {
            $fetch = User::where('id', $userid)->first();
        }

        // if ($fetch->avatar == null) {
        //     $fetch->avatar = '';
        // }

        if (!empty($fetch->avatar)) {
            $avatar = \App\Models\Utility::get_file('uploads/avatar' . '/' . $fetch->avatar);
        } else {
            $avatar = \App\Models\Utility::get_file('/' . config('chatify.user_avatar.folder') . '/avatar.png');
        }

        return Response::json([
            'favorite' => $favorite,
            'fetch' => $fetch ?? null,
            'user_avatar' => $avatar ?? null,
        ]);
    }

    /**
     * This method to make a links for the attachments
     * to be downloadable.
     *
     * @param string $fileName
     *
     * @return void
     */
    public function download($fileName)
    {
        $path = storage_path() . '/' . config('chatify.attachments.folder') . '/' . $fileName;
        if (file_exists($path)) {
            return Response::download($path, $fileName);
        } else {
            return abort(404, "Sorry, File does not exist in our server or may have been deleted!");
        }
    }
    private function sendMessage($message, $recipients)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $message =  $client->messages->create(
            $recipients,
            ['from' => $twilio_number, 'body' => $message]
        );
    }
    /**
     * Send a message to database
     *
     * @param Request $request
     *
     * @return JSON response
     */
    public function send(Request $request)
    {

        // dd($response);
        $userid = trim($request['id'], 'user_id');
        $caseid = $request->type == 'group' ? $userid : null;
        $userid = $request->type == 'group' ? null : $userid;
        // default variables
        $error = (object) [
            'status' => 0,
            'message' => null,
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed = $this->getAllowedImages();

            $file = $request->file('file');
            // if size less than 150MB
            if ($file->getSize() <= $this->getAllowedSize()) {
                if (in_array($file->getClientOriginalExtension(), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name

                    $dir = '/attachments/';
                    $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $path = \App\Models\Utility::upload_file($request, 'file', $attachment, $dir, []);

                    if ($path['flag'] == 1) {
                        $url = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }

                    //                    $file->storeAs("/" . config('chatify.attachments.folder'), $attachment);

                } else {
                    $error->status = 1;
                    $error->message = "File extension not allowed!";
                }
            } else {
                $error->status = 1;
                $error->message = "File size you are trying to upload is too large!";
            }
        }
        if (!$error->status) {
            // send to database
            $messageID = mt_rand(9, 999999999) + time();
            $message =  $this->newMessage(
                [
                    'id' => $messageID,
                    'type' => $request['type'],
                    'from_id' => Auth::user()->id,
                    'to_id' => $userid,
                    'case_id' => $caseid,
                    'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                    'attachment' => ($attachment) ? json_encode((object) [
                        'new_name' => $attachment,
                        'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                    ]) : null,
                ]
            );
            $messageData = Chatify::parseMessage($message);
            if (Auth::user()->id != $request['id'] && $request->type != 'group') {
                Chatify::push("private-chatify." . $request['id'], 'messaging', [
                    'from_id' => Auth::user()->id,
                    'to_id' => $request['id'],
                    'message' => Chatify::messageCard($messageData, true)
                ]);
            }
            if ($request->type == 'group') {

                $case = Cases::where('id', $caseid)->first();
                $your_team = $case ? implode(',', array_filter([$case->your_team, $case->your_advocates])) : '';
                $your_teamArray = explode(',', $your_team);
                $your_teamArray[] = Auth::user()->id;
                $your_teamArray[] = Auth::User()->getCompanyName(Auth::user()->id, 'id');
                $your_teamArray = array_unique($your_teamArray);
                // foreach and check is gt user->type and if type = client sen message
                foreach ($your_teamArray as $key => $value) {
                    if ($value != Auth::user()->id) {
                        $user = User::where('id', $value)->first();
                        if ($user->type == 'client' && !$user->active_status) {
                            //    $this->sendMessage($user->name. ' : A New Message on Ecasify Massenger', '+17027686358');
                        }
                    }
                }
                $messageData = $this->parseMessage($message);
                // if (Auth::user()->id != $request['id']) {
                    Chatify::push("private-chatify." . $caseid, 'groupChat', [
                        'from_id' => Auth::user()->id,
                        'from_id_name' => Auth::user()->name,
                        'to_id' => $request['id'],
                        'case_id' => $caseid,
                        'message' => Chatify::messageCard($messageData, true),
                        'company_name' => Auth::User()->getCompanyName(Auth::user()->id, 'name'),
                    ]);
                // }
            }
        }

        // send the response
        return Response::json(
            [
                'status' => '200',
                'error' => $error->status ? 1 : 0,
                'error_msg' => $error->message,
                'message' => Chatify::messageCard(@$messageData),
                'tempID' => $request['temporaryMsgId'],
            ]
        );
    }
    public function newMessage($data)
    {
        $message = new Message();
        $message->from_id = $data['from_id'];
        $message->to_id = $data['to_id'];
        $message->course_id = $data['course_id'];
        $message->body = $data['body'];
        $message->attachment = $data['attachment'];
        $message->save();
        return $message;
    }
    /**
     * fetch [user/group] messages from database
     *
     * @param Request $request
     *
     * @return JSON response
     */
    public function fetchGroupMessagesQuery($group_id)
    {
        $case = Course::where('id', $group_id)->first();

        return  Message::Where('course_id', $case->id);
    }
    public function fetch(Request $request)
    {
        if ($request->type == 'groups') {
            // fetchGroupMessagesQuery
            $query = $this->fetchGroupMessagesQuery($request['id'])->latest();
        } else {
            $query = Chatify::fetchMessagesQuery($request['id'])->latest();
        }
        // $query = Chatify::fetchMessagesQuery($request['id'])->latest();
        $messages = $query->paginate($request->per_page ?? $this->perPage);
        $totalMessages = $messages->total();
        $lastPage = $messages->lastPage();
        $response = [
            'total' => $totalMessages,
            'last_page' => $lastPage,
            'last_message_id' => collect($messages->items())->last()->id ?? null,
            'messages' => '',
        ];

        // if there is no messages yet.
        if ($totalMessages < 1) {
            $response['messages'] = '<p class="message-hint center-el"><span>Say \'hi\' and start messaging</span></p>';
            return Response::json($response);
        }
        if (count($messages->items()) < 1) {
            $response['messages'] = '';
            return Response::json($response);
        }
        $allMessages = null;
        foreach ($messages->reverse() as $message) {
            // dd( Chatify::parseMessage($message));
            $allMessages .= Chatify::messageCard(
                Chatify::parseMessage($message)
            );
        }
        $response['messages'] = $allMessages;
        return Response::json($response);
    }
    /**
     * Fetch & parse message and return the message card
     * view as a response.
     *
     * @param Message $prefetchedMessage
     * @param int $id
     * @return array
     */
    public function parseMessage($prefetchedMessage = null, $id = null)
    {
        $msg = null;
        $attachment = null;
        $attachment_type = null;
        $attachment_title = null;
        if (!!$prefetchedMessage) {
            $msg = $prefetchedMessage;
        } else {
            $msg = Message::where('id', $id)->first();
            if (!$msg) {
                return [];
            }
        }
        if (isset($msg->attachment)) {
            $attachmentOBJ = json_decode($msg->attachment);
            $attachment = $attachmentOBJ->new_name;
            $attachment_title = htmlentities(trim($attachmentOBJ->old_name), ENT_QUOTES, 'UTF-8');
            $ext = pathinfo($attachment, PATHINFO_EXTENSION);
            $attachment_type = in_array($ext, $this->getAllowedImages()) ? 'image' : 'file';
        }
        return [
            'id' => $msg->id,
            'from_id' => $msg->from_id,
            'to_id' => $msg->to_id,
            'course_id' => $msg->course_id,
            'message' => $msg->body,
            'attachment' => (object) [
                'file' => $attachment,
                'title' => $attachment_title,
                'type' => $attachment_type
            ],
            'timeAgo' => $msg->created_at->diffForHumans(),
            'created_at' => $msg->created_at->toIso8601String(),
            'isSender' => ($msg->from_id == Auth::user()->id),
            'seen' => $msg->seen,
        ];
    }

    /**
     * Make messages as seen
     *
     * @param Request $request
     *
     * @return void
     */
    public function seen(Request $request)
    {

        $userid = trim($request['id'], 'user_id');
        // make as seen
        $seenmessage = Message::Where('from_id', $userid)->where('to_id', Auth::user()->id)->where('seen', 0)->count();
        $messageCount = Message::where('to_id', Auth::user()->id)->where('seen', 0)->count();
        $seen = Chatify::makeSeen($userid);

        if ($seen) {
            $messageCount = $messageCount - $seenmessage;
        }
        // send the response
        return Response::json(
            [
                'status' => $seen,
                'messengerCount' => $messageCount,
            ],
            200
        );
    }

    /**
     * Get contacts list
     *
     * @param Request $request
     *
     * @return JSON response
     */
    public function getContacts(Request $request)
    {
        if ($request['type'] == 'groups') {
            return $this->getGroups($request);
            exit;
        }

        // dd($request);

        // get all users that received/sent message from/to [Auth user]
        $users = Message::join(
            'users',
            function ($join) {
                $join->on('ch_messages.from_id', '=', 'users.id')
                    ->orOn('ch_messages.to_id', '=', 'users.id');
            }
        )->where(function ($q) {
            $q->where('ch_messages.from_id', Auth::user()->id)
                ->orWhere('ch_messages.to_id', Auth::user()->id);
        })->where('users.id', '!=', Auth::user()->id)
            ->orderBy('ch_messages.created_at', 'desc')
            ->get()->unique('id');
        if ($users->count() > 0) {
            // fetch contacts
            $contacts = null;
            foreach ($users as $user) {
                if ($user->id != Auth::user()->id) {
                    // Get user data
                    $userCollection = User::where('id', $user->id)->first();
                    $contacts .= Chatify::getContactItem($user);
                }
            }
        }

        $objUser = Auth::user();
        $members = NULL;
            if ($objUser->type == 'company' ||$objUser->type == 'co admin'  ) {
            
            $members = User::where('created_by', '=', $objUser->creatorId())->get();
        } else {
            $members = User::where('created_by', '=', $objUser->creatorId())->where('id', '!=', $objUser->id)->orWhere('id', '=', $objUser->creatorId())->get();
        }

        $getRecords = null;

        foreach ($members as $record) {

            $getRecords .= view(
                'vendor.Chatify.layouts.listItem',
                [
                    'get' => 'all_members',
                    'type' => 'user',
                    'user' => $record,
                ]
            )->render();
        }

        // send the response
        return Response::json(
            [
                'contacts' => $users->count() > 0 ? $contacts : '<br><p class="message-hint"><span>' . __('Your contact list is empty') . '</span></p>',
                'allUsers' => $members->count() > 0 ? $getRecords : '<br><p class="message-hint"><span>' . __('Your member list is empty') . '</span></p>',
            ],
            200
        );
    }

    /**
     * Update user's list item data
     *
     * @param Request $request
     *
     * @return JSON response
     */
    public function updateContactItem(Request $request)
    {
        // Get user data
        $userid = trim($request['user_id'], 'user_id');
        $userCollection = User::where('id', $userid)->first();

        $contactItem = Chatify::getContactItem($userCollection);
        $messageCount = Message::where('to_id', Auth::user()->id)->where('seen', 0)->count();
        // send the response
        // dd($contactItem);
        return Response::json(
            [
                'contactItem' => $contactItem,
                'messengerCount' => $messageCount,
            ],
            200
        );
    }

    /**
     * Put a user in the favorites list
     *
     * @param Request $request
     *
     * @return void
     */
    public function favorite(Request $request)
    {
        // check action [star/unstar]
        if (Chatify::inFavorite($request['user_id'])) {
            // UnStar
            Chatify::makeInFavorite($request['user_id'], 0);
            $status = 0;
        } else {
            // Star
            Chatify::makeInFavorite($request['user_id'], 1);
            $status = 1;
        }

        // send the response
        return Response::json(
            [
                'status' => @$status,
            ],
            200
        );
    }

    /**
     * Get favorites list
     *
     * @param Request $request
     *
     * @return void
     */
    public function getFavorites(Request $request)
    {
        $favoritesList = null;
        $favorites = Favorite::where('user_id', Auth::user()->id);
        foreach ($favorites->get() as $favorite) {
            // get user data
            $user = User::where('id', $favorite->favorite_id)->first();
            $favoritesList .= view(
                'Chatify::layouts.favorite',
                [
                    'user' => $user,
                ]
            );
        }

        // send the response
        return Response::json(
            [
                'count' => $favorites->count(),
                'favorites' => $favorites->count() > 0 ? $favoritesList : '<p class="message-hint"><span>' . __("Your favorite list is empty") . '</span></p>',
            ],
            200
        );
    }

    /**
     * Get groups list
     *
     * @param Request $request
     *
     * @return void
     */
    public function getGroups(Request $request)
    {
        $groupsList = null;
        $authUserId = Auth::user()->id;
        if(Auth::user()->type == 'co admin'){
            $authUserId = Auth::user()->created_by;
        }
        if(Auth::user()->type == 'co admin'){
            $authUserId = Auth::user()->created_by;
        }
        $cases = Cases::where(function ($query) use ($authUserId) {
            $query->whereRaw("FIND_IN_SET(?, your_team)", [$authUserId])
                ->orWhereRaw("FIND_IN_SET(?, your_advocates)", [$authUserId]);
        })
            ->orWhere('created_by', $authUserId)
            ->select('id', 'name')
            ->get();
        $layout = Auth::User()->type == 'client' ? 'listItem' : 'groups';
        foreach ($cases as $group) {
            $groupsList .= view(
                'Chatify::layouts.' . $layout,
                [
                    'get' => 'group',
                    'group' => $group,
                ]
            )->render();
        }

        // send the response
        return Response::json(
            [
                'count' => count($cases),
                'groups' => count($cases) > 0 ? $groupsList : '<p class="message-hint"><span>' . __("You don't have any group yet") . '</span></p>',
            ],
            200
        );
    }

    /**
     * Search in messenger
     *
     * @param Request $request
     *
     * @return void
     */
    public function search(Request $request)
    {
        $getRecords = null;
        $input = trim(filter_var($request['input']));
        // dd($input);
        if (\Auth::user()->type != "client") {
            $records = User::where('id', '!=', Auth::user()->id)
                ->where('created_by', \Auth::user()->creatorId())
                ->where('type', '!=', 'client')
                ->where('name', 'LIKE', "%{$input}%")
                ->orWhere('id', \Auth::user()->creatorId())
                ->paginate($request->per_page ?? $this->perPage);
        } else {
            $records = array();
        }

        foreach ($records->items() as $record) {
            $getRecords .= view('Chatify::layouts.listItem', [
                'get' => 'search_item',
                'user' => Chatify::getUserWithAvatar($record),
            ])->render();
        }
        if ($records->total() < 1) {
            $getRecords = '<p class="message-hint center-el"><span>Nothing to show.</span></p>';
        }
        // send the response
        return Response::json([
            'records' => $getRecords,
            'total' => $records->total(),
            'last_page' => $records->lastPage()
        ], 200);
    }

    public function getSharedPhotos($user_id, $type = 'chat')
    {

        $images = array(); // Default
        // Get messages
        if ($type == 'groups') {
            // dd($user_id,$type);

            $msgs = $this->fetchGroupMessagesQuery($user_id)->orderBy('created_at', 'DESC');
        } else {
            $msgs = Chatify::fetchMessagesQuery($user_id)->orderBy('created_at', 'DESC');
        }

        if ($msgs->count() > 0) {
            foreach ($msgs->get() as $msg) {
                // If message has attachment
                if ($msg->attachment) {
                    $attachment = json_decode($msg->attachment);
                    // determine the type of the attachment
                    in_array(pathinfo($attachment->new_name, PATHINFO_EXTENSION), $this->getAllowedImages())
                        ? array_push($images, $attachment->new_name) : '';
                }
            }
        }
        return $images;
    }

    /**
     * Get shared photos
     *
     * @param Request $request
     *
     * @return void
     */
    public function sharedPhotos(Request $request)
    {
        $shared = $this->getSharedPhotos($request['user_id'], $request['type']);
        $sharedPhotos = null;

        // shared with its template
        for ($i = 0; $i < count($shared); $i++) {
            $sharedPhotos .= view(
                'Chatify::layouts.listItem',
                [
                    'get' => 'sharedPhoto',
                    'image' => \App\Models\Utility::get_file('attachments/' . $shared[$i]),

                ]
            )->render();
        }

        // send the response
        return Response::json(
            [
                'shared' => count($shared) > 0 ? $sharedPhotos : '<p class="message-hint"><span>Nothing shared yet</span></p>',
            ],
            200
        );
    }

    /**
     * Delete conversation
     *
     * @param Request $request
     *
     * @return void
     */
    public function deleteConversation(Request $request)
    {
        // delete
        $delete = Chatify::deleteConversation($request['id']);

        // send the response
        return Response::json(
            [
                'deleted' => $delete ? 1 : 0,
            ],
            200
        );
    }

    public function updateSettings(Request $request)
    {
        $msg = null;
        $error = $success = 0;

        // dark mode
        if ($request['dark_mode']) {
            $request['dark_mode'] == "dark"
                ? User::where('id', Auth::user()->id)->update(['dark_mode' => 1]) // Make Dark
                : User::where('id', Auth::user()->id)->update(['dark_mode' => 0]); // Make Light
        }

        // If messenger color selected
        if ($request['messengerColor']) {

            $messenger_color = trim(filter_var($request['messengerColor']));
            // $messenger_color = Chatify::getMessengerColors()[$messenger_color[1]];
            // dd($messenger_color);
            User::where('id', Auth::user()->id)->update(['messenger_color' => $messenger_color]);
        }
        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = $this->getAllowedImages();

            $file = $request->file('avatar');
            // if size less than 150MB
            if ($file->getSize() < $this->getAllowedSize()) {
                if (in_array($file->getClientOriginalExtension(), $allowed_images)) {
                    // delete the older one
                    if (Auth::user()->avatar != config('chatify.user_avatar.default')) {
                        $path = storage_path(config('chatify.user_avatar.folder') . '/' . Auth::user()->avatar);
                        if (file_exists($path)) {
                            @unlink($path);
                        }
                    }
                    // upload
                    $avatar = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $update = User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);
                    // $file->storeAs("public/" . config('chatify.user_avatar.folder'), $avatar);
                    // $file->storeAs(config('chatify.user_avatar.folder'), $avatar);
                    $file->storeAs('avatar', $avatar);
                    $success = $update ? 1 : 0;
                } else {
                    $msg = "File extension not allowed!";
                    $error = 1;
                }
            } else {
                $msg = "File extension not allowed!";
                $error = 1;
            }
        }

        // send the response
        return Response::json(
            [
                'status' => $success ? 1 : 0,
                'error' => $error ? 1 : 0,
                'message' => $error ? $msg : 0,
            ],
            200
        );
    }

    /**
     * Set user's active status
     *
     * @param Request $request
     *
     * @return void
     */
    public function setActiveStatus(Request $request)
    {
        $update = $request['status'] > 0 ? User::where('id', $request['user_id'])->update(['active_status' => 1]) : User::where('id', $request['user_id'])->update(['active_status' => 0]);

        // send the response
        return Response::json(
            [
                'status' => $update,
            ],
            200
        );
    }


    public function deleteMessage(Request $request)
    {
        // delete
        $delete = Chatify::deleteMessage($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ], 200);
    }

    // getAllowedImages
    public function getAllowedImages()
    {
    $allowedExtensions = Utility::getValByName('local_storage_validation');
    return explode(',', $allowedExtensions);
    }
    //  getAllowedSize
    public function getAllowedSize()
    {
        return Utility::getValByName('local_storage_max_upload_size');
    }

    // groupContacts
    public function groupContacts($request)
    {

            $courseId = trim($request['course_id']);

            $course = Course::where('id', $courseId)->first();
            $your_team = UserCourse::where('course_id',$course->id)->get();
            $your_teamArray = explode(',', $your_team);
            $remove = Auth::User()->id;
            $your_teamArray = array_filter($your_teamArray, function ($item) use ($remove) {
                return $item != $remove;
            });
            
            
            $your_teamArray = array_unique($your_teamArray);




            if (count($your_teamArray) > 0) {
                // fetch contacts
                $contacts = null;
                foreach ($your_teamArray as $user_id) {
                    $user = User::where('id', $user_id)->first();
                    if ($user->id != Auth::user()->id && $user->type != 'client') {
                        // Get user data
                        $contacts .= $this->getContactItem($user);
                        // $contacts[] = $user;
                    }
                }
            }
            // dd($contacts);
            // send the response
            return Response::json(
                [
                    'contacts' => count($your_teamArray) > 0 ? $contacts : '<br><p class="message-hint"><span>' . __('Your contact list is empty') . '</span></p>',
                ],
                200
            );
        
    }
    /**
     * Get user list's item data [Contact Itme]
     * (e.g. User data, Last message, Unseen Counter...)
     *
     * @param int $messenger_id
     * @param Collection $user
     * @return string
     */
    public function getContactItem($user)
    {
        try {
            // get last message
            $lastMessage = Chatify::getLastMessageQuery($user->id);
            // Get Unseen messages counter
            $unseenCounter = Chatify::countUnseenMessages($user->id);
            // if ($lastMessage) {
            //     $lastMessage->created_at = $lastMessage->created_at->toIso8601String();
            //     $lastMessage->timeAgo = $lastMessage->created_at->diffForHumans();
            // }
            return view('Chatify::layouts.listItem', [
                'get' => 'users',
                'user' => Chatify::getUserWithAvatar($user) ?? 'eeee',
                'lastMessage' => ($lastMessage) ? $lastMessage : '---',
                'unseenCounter' => $unseenCounter,
            ])->render();
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
