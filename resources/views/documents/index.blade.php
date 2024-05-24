@extends('layouts.app')

@section('page-title', __('Documents'))
@php
    
    $docfile = \App\Models\Utility::get_file('uploads/case_docs/');
    $originalPath = storage_path('app/public/uploads/case_docs/');
    $docfileTmp = \App\Models\Utility::get_file('uploads/case_docs/tmp/'.Auth::user()->id.'-case-docs/');
    
    
@endphp
@section('action-button')
    @can('create document')
        <div class="text-sm-end d-flex all-button-box justify-content-sm-end">
            <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="xl" data-title="Add Documents"
                data-url="{{ route('documents.create') }}" data-toggle="tooltip" title="{{ __('Create New Documents') }}"
                data-bs-original-title="{{ __('Create New Documents') }}" data-bs-placement="top" data-bs-toggle="tooltip">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endcan

@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Documents') }}</li>
@endsection

@section('content')
<style>
    .typeDocumentSelect{
        width: 200px;
        height: 40px;
        border-radius: 5px;
        padding: 5px;
    }
    </style>
    {{-- {{ dd($selectedType) }} --}}
{{-- crate a div input ->type-> select option for Filter by Type of Document --}}
<form action="{{ route('documents.index')}}" method="GET" id="filterForm" class="p-3 d-flex justify-content-end">
    <div class="form-group mb-0">
        <label for="filter_type">Filter by Type of Document</label>
        <select name="type" id="filter_type" class="form-control typeDocumentSelect">
            <option value="0"  >ALL</option>
            @php
                $types = array('pdf','png','jpg','jpeg','gif','docx','doc','xls','xlsx','zip','txt','pptx','ppt');
            @endphp
            @foreach ($types as $type)
                <option value="{{ $type }}" {{ $selectedType == $type ? 'selected' : '' }}>{{ strtoupper($type) }}</option>
            @endforeach
        </select>
    </div>
</form>




    <div class="row p-0">
        <div class="col-xl-12">
            <div class=" shadow-none">
                <div class="card-header card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table dataTable data-table">
                            <thead>
                                <tr>
                                    <th style="width:14%">{{ __('Document Type') }}</th>
                                    <th>{{ __('Document Title') }}</th>
                                    <th>{{ __('Case Title') }}</th>
                                    <th>{{ __('Uploaded at') }}</th>
                                    <th>{{ __('Uploaded By') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allDocuments as $document)
                                @php
                                $temFile = decryptFile($originalPath.$document['file'],pathinfo($document['file'])['extension']);
                                @endphp
                                {{-- {{ dd($allDocuments) }} --}}
                                    <tr>
                                        <td>
                                            <p  onclick="openFilePreview('{{ $docfileTmp . $temFile }}')">
                                            @php
                                                $type = pathinfo($document['file'])['extension'];
                                            @endphp
                                            @switch($type)
                                            @case('pdf')
                                                <img style="cursor: pointer; width: 35px; height: auto;"  src='{{ asset('storage/uploads/pdf.png') }}' alt="PDF" data-toggle="tooltip" data-bs-original-title="PDF" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="PDF">
                                                @break
                                            @case('png')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/png.png') }}' alt="PNG" data-toggle="tooltip" data-bs-original-title="PNG" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="PNG">
                                                @break
                                            @case('jpg')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/jpg.png') }}' alt="JPG" data-toggle="tooltip" data-bs-original-title="JPG" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="JPG">
                                                @break
                                            @case('jpeg')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/jpg.png') }}' alt="JPEG" data-toggle="tooltip" data-bs-original-title="JPEG" data-bs-placement="top"
                                                    data-bs-toggle="tooltip" aria-label="JPEG">
                                                @break
                                            @case('gif')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/gif.png') }}' alt="GIF" data-toggle="tooltip" data-bs-original-title="GIF" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="GIF">
                                                @break
                                            @case('docx')
                                            @case('doc')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/doc.png') }}' alt="DOCX" data-toggle="tooltip" data-bs-original-title="DOCX" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="DOCX">
                                                @break
                                            @case('xls')
                                            @case('xlsx')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/xls.png') }}' alt="XLS" data-toggle="tooltip" data-bs-original-title="XLS" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="XLS">
                                                @break
                                            @case('zip')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/zip.png') }}' alt="ZIP" data-toggle="tooltip" data-bs-original-title="ZIP" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="ZIP">
                                                @break
                                            @case('txt')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/txt.png') }}' alt="TXT" data-toggle="tooltip" data-bs-original-title="TXT" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="TXT">
                                                @break
                                            @case('pptx')
                                            @case('ppt')
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/ppt.png') }}' alt="PPT" data-toggle="tooltip" data-bs-original-title="PPT" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="PPT">
                                                @break
                                            @default
                                                <img style="cursor: pointer; width: 35px; height: auto;" src='{{ asset('storage/uploads/unknown.png') }}' alt="Unknown" data-toggle="tooltip" data-bs-original-title="Unknown" data-bs-placement="top" data-bs-toggle="tooltip" aria-label="Unknown">
                                            @endswitch  
                                            </p>                                              
                                        </td>
                                        <td onclick="openFilePreview('{{ $docfileTmp . $temFile }}')">{{ $document['doc_name'] }}</td>
                                        <td><a href="{{ route('cases.show', $document['case_id']) }}">{{ $document['case_name'] }}</a></td>
                                        @php
                                            $timestamp = strtotime($document['human_readable_time']);
                                        $formattedDate = date('F d, Y', $timestamp);
                                        @endphp
                                        <td>{{ $formattedDate }}</td>
                                        {{-- get user name where $document.uploaded_by = user id  --}}
                                        <td>{{ \App\Models\User::find($document['uploaded_by'])->name ?? '-' }}</td>
                                        <td>
                                           
                                            <div class="d-flex justify-content-start align-items-center">
                                                <a href="#" class="btn action_btn p-2"
                                                    onclick="openFilePreview('{{ $docfileTmp . $temFile }}')">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                <a href="{{ $docfileTmp . $temFile }}" target="_blank" download
                                                    class="btn action_btn p-2">
                                                    <i class="bi bi-box-arrow-down"></i>
                                                </a>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                  
                            <a href="" target="_blank" download
                            class="btn action_btn_download p-2">
                            <i class="bi bi-box-arrow-down"></i>
                        </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body">


                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>


@endsection


@push('custom-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


    <script>
        function openFilePreview(url) {
            // Clear previous content in the modal
            $('#previewModal .modal-body').empty();

            // Check the file type based on its extension
            var fileExtension = url.split('.').pop().toLowerCase();

            console.log(fileExtension);

            if (fileExtension === 'pdf') {
                // For PDF files, use an iframe to display the PDF
                $('#previewModal .modal-body').html('<iframe src="' + url +
                    '" frameborder="0" style="width: 100%; height: 400px;"></iframe>');

                    const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {

                $('#previewModal .modal-body').html('<img src="' + url +
                    '" style="max-width: 100%; max-height: 400px;" alt="Preview">');

                    const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);

            } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
                fetch(url)
                    .then(function(response) {
                        return response.arrayBuffer();
                    })
                    .then(function(data) {
                        var workbook = XLSX.read(data, {
                            type: 'array'
                        });

                        var sheet = workbook.Sheets[workbook.SheetNames[0]];

                        var tableHTML = XLSX.utils.sheet_to_html(sheet);

                        // Set a class for the table for styling
                        var tableClass = 'custom-table';

                        // Wrap the table in a div with a class
                        var styledTableHTML = '<div style="overflow-x: auto;" class="' + tableClass + '">' + tableHTML +
                            '</div>';

                        $('#previewModal .modal-body').html(styledTableHTML);
                        const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                    });
            } else if (fileExtension === 'docx') {
                fetch(url)
                    .then(response => response.arrayBuffer())
                    .then(arrayBuffer => mammoth.convertToHtml({
                        arrayBuffer: arrayBuffer
                    }))
                    .then(result => {
                       
                        const htmlContent = result.value;
                        // Wrap the HTML content in a temporary div to manipulate it
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = htmlContent;

                        // Find all images in the HTML content and add the 'width: 100%' style
                        const images = tempDiv.querySelectorAll('img');
                        images.forEach(img => {
                            img.style.width = '100%';
                        });

                        // Set the modified HTML content in the modal's body
                        $('#previewModal .modal-body').html(tempDiv.innerHTML);
                        const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);
                        $('#previewModal').modal('show');
                    })
                    .catch(error => console.error(error));
            }

            else {
                // Handle other formats here or show an error message
                $('#previewModal .modal-body').html('<p class="text-center">This file type is not supported for preview.</p>');
                $('#previewModal').modal('show');
                const downloadLink = $('.action_btn_download'); 
                downloadLink.attr('href', url);
            }

            $('#previewModal').modal('show');
        }

   
            //  on change filter_type make form submit
            $('#filter_type').on('change', function() {
                $('#filterForm').submit();
            });
    </script>
@endpush
