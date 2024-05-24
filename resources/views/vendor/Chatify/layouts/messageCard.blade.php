<?php
$seenIcon = !!$seen ? 'check-double' : 'check';
// span message time and seen
$timeAndSeen =
    "<span data-time='$created_at' class='message-time'>
        " .
    ($isSender ? "<span class='fas fa-$seenIcon' seen'></span>" : '') .
    " <span class='time'>$timeAgo</span>
    </span>";

$user = \App\Models\User::where('id', $from_id)->first();

$username = Auth::User()->type != 'client' ? $user->name : $user->getCompanyName($user->id);
$from_username = $from_id == Auth::User()->id ? 'You' : $username;
$isTeam = Auth::User()->type != 'client' && $user->type != 'client' ? 'is_team' : 'false';
// span to show sender name and time
$displaySenderAndTimer = $attachment->type == 'image' ? '' : 'display:none';
$senderAndTime =
    "<span  class='message-time sendAndTime' style=''>
    <b><span class='sender-name'> " .
    $from_username .
    " </span> </b> || <span class='tme'>" .
    date('M j g:i A Y', strtotime($created_at)) .
    "</span>
    </span>";
?>

<div class="message-card @if ($isSender) mc-sender @endif" data-id="{{ $id }}">
    {{-- Delete Message Button --}}
    {{-- @if ($isSender)
        <div class="actions">
            <i class="fas fa-trash delete-btn" data-id="{{ $id }}"></i>
        </div>
    @endif --}}
    {{-- Card --}}
    <div class="message-card-content">
        @if (@$attachment->type != 'image' || $message)
            <div class="message {!! $isTeam !!} " style="cursor: pointer;">
                {!! $message == null && $attachment != null && @$attachment->type != 'file'
                    ? $attachment->title
                    : nl2br($message) !!}
                {!! $timeAndSeen !!}
                {{-- If attachment is a file --}}
                @if (@$attachment->type == 'file')
                    <a href="{{ route(config('chatify.attachments.download_route_name'), ['fileName' => $attachment->file]) }}"
                        class="file-download">
                        <span class="fas fa-file"></span> {{ $attachment->title }}</a>
                @endif
            </div>
        @endif
        @if (@$attachment->type == 'image')
            <div class="image-wrapper" style="text-align: {{ $isSender ? 'end' : 'start' }}">
                <div class="image-file chat-image"
                    style="background-image: url('{{ Chatify::getAttachmentUrl($attachment->file) }}')">
                    <div>{{ $attachment->title }}</div>
                </div>
                <div style="margin-bottom:5px">
                    {!! $timeAndSeen !!}
                </div>
            </div>
        @endif
        {!! $senderAndTime !!}
    </div>
</div>
