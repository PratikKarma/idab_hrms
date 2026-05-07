<?php

namespace App\Services;

use Chatify\ChatifyMessenger;
use Illuminate\Support\Facades\Auth;
use App\Models\ChMessage as Message;
use Exception;
use Illuminate\Support\Collection;

class ChatifyMessengerExtended extends ChatifyMessenger
{
    /**
     * Fetch message by id and return the message card
     * view as a response.
     *
     * @param int $id
     * @return array
     */
    public static function fetchMessage($id, $index = null)
    {
        $attachment = null;
        $attachment_type = null;
        $attachment_title = null;

        $msg = Message::where('id', $id)->first();
        if (!$msg) {
            return [];
        }

        if (isset($msg->attachment)) {
            $attachmentOBJ = json_decode($msg->attachment);
            $attachment = $attachmentOBJ->new_name;
            $attachment_title = htmlentities(trim($attachmentOBJ->old_name), ENT_QUOTES, 'UTF-8');

            $ext = pathinfo($attachment, PATHINFO_EXTENSION);
            $attachment_type = in_array($ext, $this->getAllowedImages()) ? 'image' : 'file';
        }

        return [
            'index' => $index,
            'id' => $msg->id,
            'from_id' => $msg->from_id,
            'to_id' => $msg->to_id,
            'message' => $msg->body,
            'attachment' => [$attachment, $attachment_title, $attachment_type],
            'time' => $msg->created_at->diffForHumans(),
            'fullTime' => $msg->created_at,
            'viewType' => ($msg->from_id == Auth::user()->id) ? 'sender' : 'default',
            'seen' => $msg->seen,
        ];
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
            $lastMessage = $this->getLastMessageQuery($user->id);
            // Get Unseen messages counter
            $unseenCounter = $this->countUnseenMessages($user->id);
            if ($lastMessage) {
                $lastMessage->created_at = $lastMessage->created_at->toIso8601String();
                $lastMessage->timeAgo = $lastMessage->created_at->diffForHumans();
            }
            return view('Chatify::layouts.listItem', [
                'get' => 'users',
                'user' => $this->getUserWithAvatar($user),
                'lastMessage' => $lastMessage,
                'unseenCounter' => $unseenCounter,
            ])->render();
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function getUserWithAvatar($user)
    {
        if ($user->avatar == 'avatar.png' && config('chatify.gravatar.enabled')) {
            $imageSize = config('chatify.gravatar.image_size');
            $imageset = config('chatify.gravatar.imageset');
            $user->avatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '?s=' . $imageSize . '&d=' . $imageset;
        } else {
            $user->avatar = self::getUserAvatarUrl($user->avatar);
        }
        return $user;
    }

    public function getUserAvatarUrl($user_avatar_name)
    {
        return self::storage()->url(config('chatify.user_avatar.folder') . '/' . !empty($user_avatar_name) && $user_avatar_name ? $user_avatar_name : 'uploads/avatar/avatar.png');
    }

}
