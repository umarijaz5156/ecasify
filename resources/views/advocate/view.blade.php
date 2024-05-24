 <div class="modal-body">
 <div class="table-responsive">
     <table class="table dataTable data-table">

         <thead>
             <tr>
                 <th width="20%">{{ __('Court') }}</th>
                 <th>{{ __('Case') }}</th>
                 <th>{{ __('Title') }}</th>
                 <th>{{ __('Attorney') }}</th>
                 <th>{{ __('Priority') }}</th>

             </tr>
         </thead>
         <tbody>
             @foreach ($cases as $case)
                 <tr>
                     <td>
                         {{ App\Models\CauseList::getCourtById($case['court']) }} -
                         {{ App\Models\CauseList::getHighCourtById($case['highcourt']) == '-' ? $case['casenumber'] : App\Models\CauseList::getHighCourtById($case['highcourt']) }}
                         - {{ App\Models\CauseList::getBenchById($case['bench']) }}
                     </td>
                     <td>
                         {{ !empty($case['casenumber']) ? $case['casenumber'] : ' ' }} {{-- case type --}}
                         {{ !empty($case['case_number']) ? $case['case_number'] : $case['diarybumber'] }} /
                         {{-- case number or diary number --}}
                         {{ $case['year'] }}

                     </td>

                     <td>{{ $case['title'] }}</td>
                     <td>{{ App\Models\Advocate::getAdvocates($case['your_advocates']) }}</td>
                     <td>{{ $case['priority'] }}</td>

                 </tr>
             @endforeach
         </tbody>
     </table>
 </div>
 </div>
