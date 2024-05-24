<style>
    .group-list-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #e5e5e5;
    cursor: pointer;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ccc;
    background-size: cover;
    background-position: center;
    margin-right: 10px;
}

.group-info {
    flex-grow: 1;
    overflow: hidden;
    white-space: nowrap;
}

.group-info p {
    margin: 0;
    font-weight: bold;
    font-size: 16px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* You can add more styling as needed */

</style>
@php
    use App\Http\Helper\EncryptionHelper;
    $encryptionHelper = new EncryptionHelper();
@endphp
<div class="group-list-item">
    @if($group)
        <div data-id="{{ $group->id }}" data-action="0" class="avatar av-m">
            {{-- style="background-image: url('{{ Chatify::getUserWithAvatar($user)->avatar }}');" --}}
        </div>
        <div class="group-info">
            <p>{{ strlen($encryptionHelper->decryptAES($group->name)) > 5 ? substr($encryptionHelper->decryptAES($group->name), 0, 6).'..' : $encryptionHelper->decryptAES($group->name) }}</p>
            <!-- Additional group info can go here -->
        </div>
    @endif
</div>
