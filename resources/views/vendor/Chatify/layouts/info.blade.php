{{-- user info and avatar --}}
<div class="avatar av-l chatify-d-flex"></div>
<p class="info-name">{{ config('chatify.name') }}</p>
<div class="messenger-infoView-btns">
    {{-- <a href="#" class="danger delete-conversation">Delete Conversation</a> --}}
</div>
{{-- shared photos --}}
<div class="messenger-infoView-shared">
    @if(Auth::User()->type != 'client')
        <p class="messenger-title group-contact-title" ><span>Case Participants</span></p>
        <div class="group-contact-list" ></div>    
    @endif
    <p class="messenger-title"><span>Shared Photos</span></p>
    <div class="shared-photos-list"></div>
</div>
