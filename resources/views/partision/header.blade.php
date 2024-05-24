

@php
    $users = \Auth::user();
    $logo = App\Models\Utility::get_file('uploads/profile/');
    $currantLang = $users->currentLanguage();
    $languages = App\Models\Utility::languages();
    $mode_setting = \App\Models\Utility::mode_layout();

    $LangName = \App\Models\Languages::where('code', $currantLang)->first();
    if (empty($LangName)) {
        $LangName  = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
    use Carbon\Carbon;

    $notifications = App\Models\Notification::where('user_id', Auth::user()->id)
    ->orderBy('created_at', 'desc')
    ->get();

    
@endphp

    <header class="dash-header {{(isset($mode_setting['cust_theme_bg']) && $mode_setting['cust_theme_bg'] == 'on')?'transprent-bg':''}}">
    <div class="header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">

                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>

                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0 " data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="theme-avtar">
                            <img alt="#" style="width:30px;"
                                src="{{ !empty(\Auth::user()->avatar) ? $logo.  \Auth::user()->avatar : $logo . '/avatar.png' }}"
                                class="header-avtar">
                        </span>
                        <span class="hide-mob ms-2">
                            @if (!Auth::guest())
                                {{ __('Hi, ') }}{{ Auth::user()->name }}!
                            @else
                                {{ __('Guest') }}
                            @endif
                        </span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>

                    <div class="dropdown-menu dash-h-dropdown">
                        <a href="{{route('users.edit', Auth::user()->id)}}" class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{ __('Profile') }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="form_logout">
                            @csrf
                            <a href="#"  class="dropdown-item" id="logout-form">
                                <i class="ti ti-power"></i>
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </li>

            </ul>
        </div>

      
        <div class="ms-auto">
            <ul class="list-unstyled">

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-bell nocolor"></i>
                        <span id="notification-count" class="drp-text hide-mob"> {{ count($notifications) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    @if (count($notifications) != 0)
                    <div style="width: 270px;height: 400px;overflow-y: scroll;" class="custom-scroll dropdown-menu p-2 dash-h-dropdown dropdown-menu-end " aria-labelledby="dropdownLanguage">
                       
                        <div id="existing-notifications" >
                            @foreach($notifications as $notification)
                                 <?php
                                $timestamp = Carbon::parse($notification->created_at);
                                $formattedTimestamp = $timestamp->diffForHumans();
                                ?>
                                
                                @if ($notification->type == 'case')
                                <ul style="padding-left: 0px" class="pl-0">
                                    <li class="notification">
                                      <a href="{{ route('cases.show', $notification->target_id) }}" class="top-text-block">
                                        {{ $notification->message }}                                        
                                        <div class="top-text-light">{{ $formattedTimestamp }}</div>
                                      </a> 
                                    </li>
                                </ul>
                                @elseif($notification->type == 'task')
                                <ul style="padding-left: 0px" class="pl-0">
                                    <li class="notification">
                                      <a href="#" class="top-text-block">
                                        {{ $notification->message }}                                        
                                        <div class="top-text-light">{{ $formattedTimestamp }}</div>
                                      </a> 
                                    </li>
                                </ul>

                                @else
                                <ul style="padding-left: 0px" class="pl-0">
                                    <li class="notification">
                                      <a href="#" class="top-text-block">
                                        {{ $notification->message }}                                        
                                        <div class="top-text-light">{{ $formattedTimestamp }}</div>
                                      </a> 
                                    </li>
                                </ul>
                                @endif

                              
                            @endforeach
                        </div>
                        
                    </div>
                    @else
                    <div class="dropdown-menu p-2 dash-h-dropdown dropdown-menu-end " aria-labelledby="dropdownLanguage">
                        <div id="text-center">
                          
                          <ul style="padding-left: 0px" class="pl-0">
                            <li>
                              <div class="top-text-block">
                                No Notifications                                  
                              </div> 
                            </li>
                        </ul>
                        </div>
                        
                    </div>
                    @endif
                   
                </li>
             

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{ Str::upper($LangName->fullName) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end " aria-labelledby="dropdownLanguage">
                        @foreach (App\Models\Utility::languages() as $code => $lang)
                            <a href="{{ route('change.language', $code) }}"
                                class="dropdown-item {{ $currantLang == $code ? 'text-danger' : '' }}">
                                {{ Str::upper($lang) }}
                            </a>
                        @endforeach
                        @can('create language')
                            <div class="dropdown-divider m-0"></div>
                            <a href="#" data-url="{{ route('create.language') }}" data-size="md" data-ajax-popup="true" data-title="{{__('Create New Language')}}"
                            class="dropdown-item  text-primary text-primary" >{{ __('Create Language') }}</a>
                            <div class="dropdown-divider m-0"></div>
                            <a href="{{ route('manage.language', $currantLang) }}"
                                class="dropdown-item text-primary">{{ __('Manage Language') }}</a>
                        @endcan
                    </div>
                </li>
            </ul>
        </div>
        
    </div>
</header>

@push('custom-script')
    <script>
   

        $('#logout-form').on('click',function(){
            event.preventDefault();
            $('#form_logout').trigger('submit');
        });
    </script>
@endpush
