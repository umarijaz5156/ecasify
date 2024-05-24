<div class="messenger-sendCard">
    <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf
        <label><span class="fas fa-plus-circle"></span><input disabled='disabled' type="file" class="upload-attachment" name="file" accept=".{{implode(', .',config('chatify.attachments.allowed_images'))}}, .{{implode(', .',config('chatify.attachments.allowed_files'))}}" /></label>
        <button class="emoji-button"></span><span class="fas fa-smile"></button>
        <textarea readonly='readonly' name="message" class="m-send app-scroll" placeholder="Type a message.."></textarea>
        <button disabled='disabled' class="send-button"><span class="fas fa-paper-plane"></span></button>
    </form>
</div>
<script>
    // message form on submit.
 $(document).on("submit", "#message-form", (e) => {
   e.preventDefault();
   sendMessage();
 });

 // message input on keyup [Enter to send, Enter+Shift for new line]
 $(document).on("keyup", "#message-form .m-send", (e) => {
   // if enter key pressed.
   if (e.which == 13 || e.keyCode == 13) {
     // if shift + enter key pressed, do nothing (new line).
     // if only enter key pressed, send message.
     if (!e.shiftKey) {
       triggered = isTyping(false);
       sendMessage();
     }
   }
 });

// On [upload attachment] input change, show a preview of the image/file.
$("body").on("change", ".upload-attachment", (e) => {
  let file = e.target.files[0];
  if (!attachmentValidate(file)) return false;
  let reader = new FileReader();
  let sendCard = $(".messenger-sendCard");
  reader.readAsDataURL(file);
  reader.addEventListener("loadstart", (e) => {
    $("#message-form").before(loadingSVG());
  });
  reader.addEventListener("load", (e) => {
    $(".messenger-sendCard").find(".loadingSVG").remove();
    if (!file.type.match("image.*")) {
      // if the file not image
      sendCard.find(".attachment-preview").remove(); // older one
      sendCard.prepend(attachmentTemplate("file", file.name));
    } else {
      // if the file is an image
      sendCard.find(".attachment-preview").remove(); // older one
      sendCard.prepend(
        attachmentTemplate("image", file.name, e.target.result)
      );
    }
  });
});

function attachmentValidate(file) {
  const fileElement = $(".upload-attachment");
  const { name: fileName, size: fileSize } = file;
  const fileExtension = fileName.split(".").pop();
  if (
    !chatify.allAllowedExtensions.includes(
      fileExtension.toString().toLowerCase()
    )
  ) {
    alert("file type not allowed");
    fileElement.val("");
    return false;
  }
  // Validate file size.
  if (fileSize > chatify.maxUploadSize) {
    alert("File is too large!");
    return false;
  }
  return true;
}

  // Attachment preview cancel button.
  $("body").on("click", ".attachment-preview .cancel", () => {
    cancelAttachment();
  });
  /**
 *-------------------------------------------------------------
 * Cancel file attached in the message.
 *-------------------------------------------------------------
 */
function cancelAttachment() {
  $(".messenger-sendCard").find(".attachment-preview").remove();
  $(".upload-attachment").replaceWith(
    $(".upload-attachment").val("").clone(true)
  );
}

</script>