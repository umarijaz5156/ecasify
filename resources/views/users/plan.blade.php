<div class="table-border-style ">
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th>{{__('Name / Price')}}</th>
                <th>{{__('Users')}}</th>
                <th>{{__('Advocates')}}</th>
                <th>{{__('Action')}}</th>
            </tr>
            @foreach($plans as $plan)
                <tr>

                    <td>{{$plan->name}} <br> <small>{{$admin_payment_setting['site_currency_symbol'].$plan->price}} {{' / '.
                            $plan->duration}}</small></td>
                    <td>{{$plan->max_users}}</td>
                    <td>
                        @if($user->plan == $plan->id)
                        <div class="btn btn-primary btn-sm rounded-pill my-auto w-100">{{__('Active')}}</div>
                        @else
                        <a href="{{route('plan.active',[$user->id,$plan->id])}}"
                            class="btn btn-primary btn-sm rounded-pill my-auto w-100"
                            title="{{__('Click to Upgrade Plan')}}"><i class="fas fa-cart-plus"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
