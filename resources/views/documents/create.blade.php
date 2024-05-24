{{ Form::open(['route' => 'documents.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('Select Case', __('Cases'), ['class' => 'form-label']) !!}
                {!! Form::select('case_id', ['' => __('Select Case')] + $cases->toArray(), null, [
                    'class' => 'form-control',
                    'id' => 'choices-multiple',
                    'data-role' => 'tagsinput',
                ]) !!}
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div id="folderContainer" class="col-md-12">
                    <a class="btn mt-4 mb-3 btn-primary createFolderButton justify-content-end btn-sm text-white"
                        id="createFolderButton">Create Folder</a>
                </div>
            </div>
            <div class="row mt-4" id="folderTemplate">

            </div>
        </div>


    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Submit') }}" class="btn btn-primary">
</div>

{{ Form::close() }}



<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<!-- Confirmation Modal -->
<div id="deleteConfirmationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this file?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        let editFormData = {};
        let selectedCaseId = null;
        let folderCounts = null;
        let itemCounts = null;


        function updateFolderTemplate() {

            const folderContainer = document.getElementById("folderContainer");
            const folderTemplate = $('#folderTemplate')[0];

            const templateContainer = document.getElementById('appendDIv');
            const newdiv = document.getElementById("newdiv");


            const createFolderButton = document.getElementById("createFolderButton");


            if (selectedCaseId === null) {
                createFolderButton.classList.add('disabled');
            } else {
                createFolderButton.classList.remove('disabled');
            }


            folderCounts = 0;
            itemCounts = 0;

            for (const folderIndex in editFormData) {
                const folderData = editFormData[folderIndex];
                const makeFolder = createRowFolder(folderCounts, folderData);
                folderTemplate.appendChild(makeFolder);
                folderCounts++;
            }

    

            FilePond.parse(folderTemplate);
        }


        function createRowFolder(folderCounts, folderData = null) {
            const folderClone = document.createElement('div');
            folderClone.classList.add('col-md-12');


            let docDataHtml = '';
            if (folderData && folderData.docData) {
                let folderTableCreated = false; // Flag to track if a table has been created for the folder

                folderData.docData.forEach((doc, docIndex) => {
                    if (Array.isArray(doc.files) && doc.files.length > 0) {
                        if (!folderTableCreated) {
                            docDataHtml += `
                <div class="table-responsive">
                    <table class="table mt-3">
                        <tr>
                            <th scope="col">File Name</th>
                            <th scope="col">File Description</th>
                            <th scope="col">Actions</th>
                        </tr>`;
                            folderTableCreated = true;
                        }

                        doc.files.forEach((file, fileIndex) => {
                            docDataHtml += `
                <tr class='doc_table_tr' data-folder-index="${folderCounts}" data-doc-index="${docIndex}" data-file-index="${fileIndex}">
                    <td>${doc.doc_name}</td>
                    <td>${doc.doc_des}</td>
                    <td>
                        <div class="d-flex justify-content-start align-items-center">
                            <a href="../../storage/uploads/case_docs/tmp/{{ Auth::user()->id.'-case-docs/'  }}${file}" download class="ti ti-download btn  btn-sm btn"></a>
                            <a href="#" class="ti ti-trash btn  btn-sm btn  delete-btn"></a>
                        </div>
                    </td>
                </tr>`;
                        });
                    } else {
                        console.error('doc.files is not an array or empty:', doc.files);
                    }
                });

                if (folderTableCreated) {
                    docDataHtml += `</table></div>`;
                }
            }


            folderClone.innerHTML = `<div class="card card_closest shadow-none rounded-0 border my-3">
                                        <div class="card-header">
                                            <div class="row flex-grow-1">
                                                <div class="col-md d-flex align-items-center col-6">
                                                    <h5 class="card-header-title text-primary">Folder Name and
                                                        Description</h5>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                        <a 
                                                        class="ti ti-trash btn btn-danger btn_danger_color text-white btn-sm remove-folder-button"
                                                        ></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body appendDIv">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="">Folder Name</label>
                                                    <input type="text" class="form-control"
                                                        name="folders[${folderCounts}][folder_name]"
                                                        value="${folderData ? folderData.folder_name : ''}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="">Folder Description</label>
                                                    <input type="text" class="form-control"
                                                        name="folders[${folderCounts}][folder_description]"
                                                        value="${folderData ? folderData.folder_description : ''}">
                                                </div>
                                            </div>
                                                <br>
                                                <hr class="py-2">

                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <a style="float: right;" class="btn btn-primary btn-sm text-white add-row-button"><i class="fas fa-plus"></i> Add
                                                            Files</a>
                                                    </div>
                                                </div>
                                                <div class="unique_closest_class mt-3">
                                                    
                                                   
                                                    ${docDataHtml}
                                                </div>

                                        </div>
                                    </div>`;

            return folderClone;
        }



        document.addEventListener("click", event => {
            if (event.target.classList.contains("add-row-button")) {

                const closestAppendDiv = event.target.closest(".appendDIv");
                const closestUniqueDiv = closestAppendDiv.querySelector(".unique_closest_class");

                const folderCountsInput = closestAppendDiv.querySelector(
                    '[name^="folders["][name$="[folder_name]"]');
                const nameAttribute = folderCountsInput.getAttribute('name');
                const startIndex = nameAttribute.indexOf("[") + 1;
                const endIndex = nameAttribute.indexOf("]", startIndex);

                // Remove "const" here to refer to the outer variable
                folderCounts = parseInt(nameAttribute.substring(startIndex, endIndex));
           

                const rowTemplateee = createRowTemplate(folderCounts, itemCounts);
                itemCounts++;

                closestUniqueDiv.appendChild(rowTemplateee);
            } else if (event.target.classList.contains("createFolderButton")) {


                const closestAppendDiv = document.querySelector(".appendDIv");

                if (closestAppendDiv === null) {
                    folderCounts = 0;
                } else {
                    const lastFolderInput = closestAppendDiv.querySelector(
                        '[name^="folders["][name$="[folder_name]"]:last-child'
                    );
                    const nameAttribute = lastFolderInput.getAttribute('name');
                    const startIndex = nameAttribute.indexOf("[") + 1;

                    const endIndex = nameAttribute.indexOf("]", startIndex);

                    folderCounts = parseInt(nameAttribute.substring(startIndex, endIndex)) + 1;
                }

            
                const makeFolder = createRowFolder(folderCounts);
                folderTemplate.appendChild(makeFolder);
                folderCounts++;

                createFolderButton.classList.remove('disabled');

            } else if (event.target.classList.contains("remove-row-button")) {
                const row = event.target.closest(".row-template");
                row.remove();
            } else if (event.target.classList.contains("remove-folder-button")) {
                const folder = event.target.closest(".card");
                folder.remove();
            } else if (event.target.classList.contains('delete-btn')) {
                event.preventDefault();

                const folderIndex = event.target.closest(
                        '.doc_table_tr')
                    .getAttribute('data-folder-index');
                const docIndex = event.target.closest(
                        '.doc_table_tr')
                    .getAttribute(
                        'data-doc-index');
                const fileIndex = event.target.closest(
                        '.doc_table_tr')
                    .getAttribute('data-file-index');

                const row = event.target.closest('.doc_table_tr');
                // row.remove();
                // Send AJAX request
                const formData = new FormData();
                formData.append('folderIndex', folderIndex);
                formData.append('docIndex', docIndex);
                formData.append('fileIndex', fileIndex);
                formData.append('case_id', selectedCaseId);


                // Show the confirmation modal
                $('#deleteConfirmationModal').modal('show');

                // Add a click event listener to the "Delete" button in the confirmation modal
                document.getElementById('confirmDelete').addEventListener('click', function() {
                    // Close the confirmation modal
                    $('#deleteConfirmationModal').modal('hide');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $(
                                    'meta[name="csrf-token"]')
                                .attr(
                                    'content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('cases.delete.doc') }}",
                        dataType: 'json',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            const row = event.target
                                .closest(
                                    '.doc_table_tr');
                            row.remove();
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error(error);
                        }
                    });
                });
            }
        });



        function createRowTemplate(folderIndex, itemIndex, doc = null) {

            const rowClone = document.createElement('div');
            rowClone.classList.add('row', 'mt-4', 'mb-4', 'row-template', 'template-container');
            rowClone.style.display = 'flex';

            const docName = doc ? doc.doc_name : '';
            const docDescription = doc ? doc.doc_des : '';
            const files = doc ? doc.files : '';



            rowClone.innerHTML = `
            <div class="col-md-6">
                <input type="text" class="form-control doc_name" placeholder="Name"
                    name="folders[${folderIndex}][folder_doc][${itemIndex}][doc_name]"
                    value="${docName}">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control"
                    placeholder="Description"
                    name="folders[${folderIndex}][folder_doc][${itemIndex}][doc_description]"
                    value="${docDescription}">
            </div>
            <div class="col-md-1 text-end">
                <a class="ti ti-trash btn btn-danger btn_danger_color text-end text-white btn-sm remove-row-button"></a>
            </div>
            <div class="col-md-11 py-2">
                <div class="col-md-12 ">
                
                    <input  name="folders[${folderIndex}][folder_doc][${itemIndex}][files][]" class="filepond-input form-control filepond_${folderIndex}${itemIndex}" type="file"   id="filepond_${folderIndex}${itemIndex}">
                </div>
            </div>
         `;

            const inputElement = rowClone.querySelector(`.filepond_${folderIndex}${itemIndex}`);

            initializeFilePond(inputElement, folderIndex, itemIndex);

            return rowClone;
        }

        function initializeFilePond(inputElement, folderIndex, docIndex) {



            const pond = FilePond.create(inputElement, {
                allowMultiple: true,
                allowImageValidateSize: false,
                dropOnElement: true,
                dropOnPage: false,
                imageValidateSizeMinHeight: 370,
                imageValidateSizeMinWidth: 550,
                allowImagePreview: true,
                imagePreviewHeight: 200,
                acceptedFileTypes: [],
                server: {
                    process: (fieldName, file, metadata, load, error, progress, abort, transfer,
                        options) => {
                        // Handle file upload using Ajax
                        const formData = new FormData();
                        const headers = new Headers();
                        headers.append('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        formData.append(fieldName, file);
                        fetch('/cases/case_docs', {
                                method: 'POST',
                                body: formData,
                                headers: headers,
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    load(result.fileUrl);
                                    const fileItem = pond.getFiles().find(item => item
                                        .file ===
                                        file);
                                    if (fileItem) {
                                        fileItem.file.name = result
                                            .fileName; // Update the name of the uploaded file

                                    }
                                } else {
                                    error(result.error);
                                }
                            })
                            .catch(() => {
                                error('Upload failed');
                            });
                    }
                },


            });


        }


        // Event listener for case selection dropdown
        $('#choices-multiple').on('change', function() {
            selectedCaseId = $(this).val();

            editFormData = {};
            folderCounts = 0;
            itemCounts = 0;

            $.ajax({
                url: '/documents/get-case-docs/' + selectedCaseId,
                type: 'GET',
                success: function(response) {
                    editFormData = JSON.parse(response.case_docs);
                    const dynamicFieldsContainer = $('#folderTemplate');

                    dynamicFieldsContainer.empty();
                    updateFolderTemplate();

                }
            });
        });

        updateFolderTemplate();
    });
</script>
