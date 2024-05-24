import './bootstrap';

import Alpine from 'alpinejs';

window.Pikaday = require("pikaday");

import * as FilePond from 'filepond';

import 'filepond/dist/filepond.min.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';

import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';


FilePond.registerPlugin(FilePondPluginImagePreview);


import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';


FilePond.registerPlugin(FilePondPluginImageValidateSize);


import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';


FilePond.registerPlugin(FilePondPluginFileValidateType);


window.Alpine = Alpine;

Alpine.start();


