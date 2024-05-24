@extends('layouts.app')

@php
    $user = Auth::user();
@endphp

<style>
    .group {
        /* background: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 20px;
        padding: 10px; */

        /* background: white;
        box-shadow: 0 7px 14px 0 rgba(49, 49, 93, 0.10), 0 3px 6px 0 rgba(0, 0, 0, 0.08);
        border-radius: 4px;
        margin-bottom: 20px; */
    }

    .group label {
        color: #31325F;
        font-weight: 300;
        margin-bottom: 10px;
        width: 100%;
    }

    .group label>span {
        width: 120px;
        text-align: right;
        margin-right: 20px;
        font-weight: 500;
    }

    .group .field {
        /* flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #31325F;
        outline: none;
        font-weight: 300; */
        display: block;
        width: 100%;
        padding: 0px 2rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #293240;
        background-color: #ffffff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: 6px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }


    .group .field.stripe-input {
        background: transparent;
        /* border: 0; */
    }

    .group input[type="text"] {
        width: 100%;
    }

    .group button {
        background: #666EE8;
        color: white;
        border: none;
        border-radius: 4px;
        margin-top: 20px;
        font-size: 15px;
        font-weight: 400;
        width: 100%;
        height: 40px;
        outline: none;
        cursor: pointer;
    }

    button:hover {
        background: #555ABF;
    }

    .outcome {
        float: left;
        width: 100%;
        padding-top: 8px;
        min-height: 24px;
        text-align: center;
    }

    .success,
    .error {
        display: none;
        font-size: 13px;
    }

    .success.visible,
    .error.visible {
        display: inline;
    }

    .error {
        color: #E4584C;
    }

    .success {
        color: #666EE8;
    }

    .success .token {
        font-weight: 500;
        font-size: 13px;
    }

    /* CSS for the card brand */
    /* CSS for the card brand */
    .brand {
        display: flex;
        align-items: center;
        color: #31325F;
        font-size: 18px;
        /* Adjust the font size as needed */
    }

    .brand i {
        margin-right: 10px;
        font-size: 24px;
    }

    #card-brand-container {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        font-weight: 500;
        font-size: 16px;
        /* Adjust the font size as needed */
        color: #31325F;
        background: #fff;
    }

    .newrow {
        display: flex;
        justify-content: start;
        align-items: center;
        position: relative;

    }

    .newrow #card-brand-container {
        max-width: max-content;
        width: 100%;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        border: none;
        background: transparent;

    }

    #card-number-element {
        flex-grow: 1;
        width: 100%;
    }

    .inputFieldClass {
        border-radius: 5px !important;
        transition: background 0.15s ease, border 0.15s ease, box-shadow 0.15s ease, color 0.15s ease !important;
        border: 1px solid #e6e6e6 !important;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.03), 0px 3px 6px rgba(0, 0, 0, 0.02) !important;
    }
</style>


@push('custom-script')
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        var type = window.location.hash.substr(1);
        $('.list-group-item').removeClass('active');
        $('.list-group-item').removeClass('text-primary');
        if (type != '') {
            $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
        } else {
            $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
        }

        $(document).on('click', '.list-group-item', function() {
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            setTimeout(() => {
                $(this).addClass('active').removeClass('text-primary');
            }, 10);
        });

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

        });

        $(document).on('click', '.apply-coupon', function(e) {
            e.preventDefault();
            var where = $(this).attr('data-from');

            applyCoupon($('#' + where + '_coupon').val(), where);
        })

        function applyCoupon(coupon_code, where) {

            if (coupon_code != '') {
                $.ajax({
                    url: '{{ route('apply.coupon') }}',
                    datType: 'json',
                    data: {
                        plan_id: '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}',
                        coupon: coupon_code,
                        frequency: $('input[name="' + where + '_payment_frequency"]:checked').val()
                    },
                    success: function(data) {


                        if (data.is_success) {
                            $('.' + where + '-coupon-tr').show().find('.' + where + '-coupon-price').text(data
                                .discount_price);
                            $('.' + where + '-final-price').text(data.final_price);

                            show_toastr('success', data.message, 'success');
                        } else {
                            $('.' + where + '-coupon-tr').hide().find('.' + where + '-coupon-price').text('');
                            $('.' + where + '-final-price').text(data.final_price);
                            show_toastr('error', data.message, 'error');
                        }
                    }
                })
            } else {
                show_toastr('error', '{{ __('Invalid Coupon Code.') }}');
                $('.' + where + '-coupon-tr').hide().find('.' + where + '-coupon-price').text('');
            }
        }
    </script>

    <script type="text/javascript">
        @if (isset($admin_payment_setting['is_stripe_enabled']) &&
                $admin_payment_setting['is_stripe_enabled'] == 'on' &&
                !empty($admin_payment_setting['stripe_key']) &&
                !empty($admin_payment_setting['stripe_secret']))


            var stripe = Stripe('{{ $admin_payment_setting['stripe_key'] }}');
            var elements = stripe.elements();

            var style = {
                base: {
                    iconColor: '#666EE8',
                    color: '#31325F',
                    lineHeight: '40px',
                    fontWeight: 300,
                    fontFamily: 'Helvetica Neue',
                    fontSize: '15px',

                    '::placeholder': {
                        color: '#CFD7E0',
                    },
                },
            };

            var cardNumberElement = elements.create('cardNumber', {
                style: style
            });
            cardNumberElement.mount('#card-number-element');

            var cardExpiryElement = elements.create('cardExpiry', {
                style: style
            });
            cardExpiryElement.mount('#card-expiry-element');

            var cardCvcElement = elements.create('cardCvc', {
                style: style
            });
            cardCvcElement.mount('#card-cvc-element');



            // function setOutcome(result) {
            //     var successElement = document.querySelector('.success');
            //     var errorElement = document.querySelector('.error');
            //     successElement.classList.remove('visible');
            //     errorElement.classList.remove('visible');

            //     if (result.token) {
            //         successElement.querySelector('.token').textContent = result.token.id;
            //         successElement.classList.add('visible');

            //         var form = document.querySelector('form');
            //         form.querySelector('input[name="token"]').setAttribute('value', result.token.id);
            //         form.submit();
            //     } else if (result.error) {
            //         errorElement.textContent = result.error.message;
            //         errorElement.classList.add('visible');
            //     }
            // }

            // var cardBrandToPfClass = {
            //     'visa': 'fa-cc-visa',
            //     'mastercard': 'fa-cc-mastercard',
            //     'amex': 'fa-cc-amex',
            //     'discover': 'fa-cc-discover',
            //     'diners': 'fa-cc-diners-club',
            //     'jcb': 'fa-cc-jcb',
            //     'unknown': 'fa-credit-card',
            // };

            // function setBrandIcon(brand) {
            //     var brandIconElement = document.getElementById('brand-icon');

            //     var pfClass = 'fas fa-credit-card';
            //     if (brand in cardBrandToPfClass) {
            //         pfClass = cardBrandToPfClass[brand];
            //     }

            //     for (var i = brandIconElement.classList.length - 1; i >= 0; i--) {
            //         brandIconElement.classList.remove(brandIconElement.classList[i]);
            //     }
            //     brandIconElement.classList.add('fab');
            //     brandIconElement.classList.add(pfClass);
            // }



            var cardBrandToImage = {
                'visa': 'https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4bdb47b03ac81d9945bfe.svg',
                'mastercard': 'https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130711885b5e41b28c9848f.svg',
                'amex': 'https://js.stripe.com/v3/fingerprinted/img/amex-a49b82f46c5cd6a96a6e418a6ca1717c.svg',
                'discover': 'https://js.stripe.com/v3/fingerprinted/img/discover-ac52cd46f89fa40a29a0bfb954e33173.svg',
                'diners': 'https://js.stripe.com/v3/fingerprinted/img/diners-fbcbd3360f8e3f629cdaa80e93abdb8b.svg',
                'jcb': 'https://js.stripe.com/v3/fingerprinted/img/jcb-271fd06e6e7a2c52692ffa91a95fb64f.svg',
                'unknown': 'https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4bdb47b03ac81d9945bfe.svg',
            };

            function setBrandImage(brand) {
                var cardBrandIconsContainer = document.querySelector('.p-CardBrandIcons');

                // Remove existing images
                cardBrandIconsContainer.innerHTML = '';

                if (brand in cardBrandToImage) {
                    var brandImageSrc = cardBrandToImage[brand];
                    var brandImage = document.createElement('img');
                    brandImage.className = 'p-CardBrandIcon';
                    brandImage.alt = brand;
                    brandImage.src = brandImageSrc;

                    cardBrandIconsContainer.appendChild(brandImage);
                }
            }

            cardNumberElement.on('change', function(event) {
                if (event.brand) {
                    setBrandImage(event.brand);
                }

            });





            var form = document.getElementById('payment-form');

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                stripe.createToken(cardNumberElement).then(function(result) {

                    if (result.error) {
                        $("#card-errors").html(result.error.message);
                        toastrs('Error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.

                        stripeTokenHandler(result.token);
                        form.trigger('submit');
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                // Submit the form
                form.submit();
            }




            // extra


            // var stripe = Stripe('{{ $admin_payment_setting['stripe_key'] }}');
            // var elements = stripe.elements();

            // // Custom styling can be passed to options when creating an Element.
            // var style = {
            //     base: {
            //         iconColor: '#666EE8',
            //         color: '#31325F',
            //         lineHeight: '40px',
            //         fontWeight: 300,
            //         fontFamily: 'Helvetica Neue',
            //         fontSize: '15px',

            //         '::placeholder': {
            //         color: '#CFD7E0',
            //         },
            //     },
            //     };

            // var payment = elements.create('card');

            //     payment.mount('#card-element');
            // var form = document.getElementById('payment-form');

            // form.addEventListener('submit',(event) => {
            //         event.preventDefault();
            //     stripe.createToken(payment).then(function (result) {
            //         console.log(result);
            //         if (result.error) {
            //             $("#card-errors").html(result.error.message);
            //             toastrs('Error', result.error.message, 'error');
            //         } else {
            //             // Send the token to your server.

            //             stripeTokenHandler(result.token);
            //             form.trigger('submit');
            //         }
            //     });
            // });

            // function stripeTokenHandler(token) {
            //     // Insert the token ID into the form so it gets submitted to the server
            //     var form = document.getElementById('payment-form');
            //     var hiddenInput = document.createElement('input');
            //     hiddenInput.setAttribute('type', 'hidden');
            //     hiddenInput.setAttribute('name', 'stripeToken');
            //     hiddenInput.setAttribute('value', token.id);
            //     form.appendChild(hiddenInput);
            //     // Submit the form
            //     form.submit();
            // }
        @endif
    </script>

    @if (
        !empty($admin_payment_setting['is_paystack_enabled']) &&
            isset($admin_payment_setting['is_paystack_enabled']) &&
            $admin_payment_setting['is_paystack_enabled'] == 'on')
        <script src="https://js.paystack.co/v1/inline.js"></script>
        <script>
            $(document).on("click", "#pay_with_paystack", function() {
                $('#paystack-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;
                        var paystack_callback = "{{ url('/plan/paystack') }}";
                        var order_id = '{{ time() }}';
                        var handler = PaystackPop.setup({
                            key: '{{ $admin_payment_setting['paystack_public_key'] }}',
                            email: res.email,
                            amount: res.total_price * 100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {
                                console.log(response.reference, order_id);
                                window.location.href = paystack_callback + '/' + response
                                    .reference + '/' + '{{ encrypt($plan->id) }}' +
                                    '?coupon_id=' + coupon_id
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('error', data.message);
                    }

                }).trigger('submit');
            });
        </script>
    @endif

    @if (
        !empty($admin_payment_setting['is_flutterwave_enabled']) &&
            isset($admin_payment_setting['is_flutterwave_enabled']) &&
            $admin_payment_setting['is_flutterwave_enabled'] == 'on')
        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>

        <script>
            //    is_flutterwave_enabled Payment
            $(document).on("click", "#pay_with_flaterwave", function() {

                $('#flaterwave-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if ("{{ isset($admin_payment_setting['flutterwave_public_key']) }}") {
                            API_publicKey = "{{ $admin_payment_setting['flutterwave_public_key'] }}";
                        }
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/plan/flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '{{ Auth::user()->email }}',
                            amount: res.total_price,
                            currency: res.currency,
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                'fluttpay_online-' +
                                {{ date('Y-m-d') }},
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function() {},
                            callback: function(response) {
                                var txref = response.tx.txRef;
                                if (
                                    response.tx.chargeResponseCode == "00" ||
                                    response.tx.chargeResponseCode == "0"
                                ) {
                                    window.location.href = flutter_callback + '/' + txref + '/' +
                                        '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}?coupon_id=' +
                                        coupon_id + '&payment_frequency=' + res.payment_frequency;
                                } else {
                                    // redirect to a failure page.
                                }
                                x.close(); // use this to close the modal immediately after payment.
                            }
                        });
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).trigger('submit');
            });
        </script>
    @endif

    @if (
        !empty($admin_payment_setting['is_razorpay_enabled']) &&
            isset($admin_payment_setting['is_razorpay_enabled']) &&
            $admin_payment_setting['is_razorpay_enabled'] == 'on')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            // Razorpay Payment

            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {

                        var razorPay_callback = '{{ url('/plan/razorpay') }}';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if ("{{ isset($admin_payment_setting['razorpay_public_key']) }}") {
                            API_publicKey = "{{ $admin_payment_setting['razorpay_public_key'] }}";
                        }
                        var options = {
                            "key": API_publicKey, // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Plan',
                            "currency": res.currency,
                            "description": "",
                            "handler": function(response) {
                                window.location.href = razorPay_callback + '/' + response
                                    .razorpay_payment_id + '/' +
                                    '{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}?coupon_id=' +
                                    coupon_id + '&payment_frequency=' + res.payment_frequency;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).trigger('submit');
            });
        </script>
    @endif

    @if (isset($admin_payment_setting['is_payfast_enabled']) &&
            $admin_payment_setting['is_payfast_enabled'] == 'on' &&
            !empty($admin_payment_setting['payfast_merchant_id']) &&
            !empty($admin_payment_setting['payfast_merchant_key']))
        <script>
            $(document).ready(function() {
                get_payfast_status(amount = 0, coupon = null);
            })

            function get_payfast_status(amount, coupon) {

                var plan_id = $('#plan_id').val();

                $.ajax({
                    url: '{{ route('payfast.payment') }}',
                    method: 'POST',
                    data: {
                        'plan_id': plan_id,
                        'coupon_amount': amount,
                        'coupon_code': coupon
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.success == true) {
                            $('#get-payfast-inputs').append(data.inputs);

                        } else {
                            show_toastr('error', data.inputs, 'error')
                        }
                    }
                });
            }
        </script>
    @endif

    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
    </script>
@endpush


@section('page-title')
    {{ __('Plan Payment') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('plans.index') }}">{{ __('Plan') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Plan Payment') }}</li>
@endsection

@section('content')

    <div class="col-sm-12">
        <div class="row g-0">
            <div class="col-xl-3 border-end border-bottom">
                <div class="card shadow-none bg-transparent sticky-top">

                    <div class="list-group list-group-flush rounded-0" id="useradd-sidenav">
                        @if (isset($admin_payment_setting['is_manually_enabled']) && $admin_payment_setting['is_manually_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#manually-payment" role="tab" aria-controls="manually"
                                aria-selected="true">{{ __('Manually') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_bank_enabled']) && $admin_payment_setting['is_bank_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#bank-payment" role="tab" aria-controls="manually"
                                aria-selected="true">{{ __('Bank Transfer') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#stripe-payment" role="tab" aria-controls="stripe"
                                aria-selected="true">{{ __('Stripe') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#paypal-payment" role="tab" aria-controls="paypal"
                                aria-selected="false">{{ __('Paypal') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#paystack-payment" role="tab" aria-controls="paystack"
                                aria-selected="false">{{ __('Paystack') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#flutterwave-payment" role="tab" aria-controls="flutterwave"
                                aria-selected="false">{{ __('Flutterwave') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#razorpay-payment" role="tab" aria-controls="razorpay"
                                aria-selected="false">{{ __('Razorpay') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#paytm-payment" role="tab" aria-controls="paytm"
                                aria-selected="false">{{ __('Paytm') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#mercadopago-payment" role="tab" aria-controls="mercadopago"
                                aria-selected="false">{{ __('Mercado Pago') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#mollie-payment" role="tab" aria-controls="mollie"
                                aria-selected="false">{{ __('Mollie') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#skrill-payment" role="tab" aria-controls="skrill"
                                aria-selected="false">{{ __('Skrill') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#coingate-payment" role="tab" aria-controls="coingate"
                                aria-selected="false">{{ __('Coingate') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on')
                            <a class="list-group-item list-group-item-action border-0" data-toggle="tab"
                                href="#paymentwall-payment" role="tab" aria-controls="paymentwall"
                                aria-selected="true">{{ __('Paymentwall') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_toyyibpay_enabled']) && $admin_payment_setting['is_toyyibpay_enabled'] == 'on')
                            <a href="#toyyibpay_payment" class="list-group-item list-group-item-action border-0"
                                data-toggle="tab" role="tab" aria-controls="toyyibpay"
                                aria-selected="true">{{ __('Toyyibpay') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_payfast_enabled']) && $admin_payment_setting['is_payfast_enabled'] == 'on')
                            <a href="#payfast_payment" class="list-group-item list-group-item-action border-0"
                                data-toggle="tab" role="tab" aria-controls="payfast" aria-selected="true">
                                {{ __('Payfast') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_iyzipay_enabled']) && $admin_payment_setting['is_iyzipay_enabled'] == 'on')
                            <a href="#useradd-16" class="list-group-item list-group-item-action border-0">
                                {{ __('IyziPay') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_sspay_enabled']) && $admin_payment_setting['is_sspay_enabled'] == 'on')
                            <a href="#useradd-17" class="list-group-item list-group-item-action border-0">
                                {{ __('SSPay') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        @endif
                        @if (isset($admin_payment_setting['is_paytab_enabled']) && $admin_payment_setting['is_paytab_enabled'] == 'on')
                            <a href="#useradd-18"
                                class="list-group-item list-group-item-action border-0">{{ __('PayTab') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        @endif
                        @if (isset($admin_payment_setting['is_benefit_enabled']) && $admin_payment_setting['is_benefit_enabled'] == 'on')
                            <a href="#useradd-19"
                                class="list-group-item list-group-item-action border-0">{{ __('Benefit') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        @endif
                        @if (isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on')
                            <a href="#useradd-20"
                                class="list-group-item list-group-item-action border-0">{{ __('Cashfree') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        @endif
                        @if (isset($admin_payment_setting['is_aamarpay_enabled']) && $admin_payment_setting['is_aamarpay_enabled'] == 'on')
                            <a href="#useradd-21"
                                class="list-group-item list-group-item-action border-0">{{ __('Aamarpay') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        @endif
                    </div>

                    <div class="card shadow-none rounded-0 border price-card price-1 wow animate__fadeInUp"
                        data-wow-delay="0.2s"
                        style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                        <div class="card-body">
                            <span class="price-badge bg-primary">{{ $plan->name }}</span>

                            <span class="mb-4 f-w-600 p-price">{{ number_format($plan->price) }}<small class="text-sm">/
                                    {{ $plan->duration }}</small></span>

                            <p class="mb-0">
                                {{ $plan->description }}
                            </p>

                            <ul class="list-unstyled my-5">
                                <li>
                                    <span class="theme-avtar">
                                        <i class="text-primary ti ti-circle-plus"></i></span>
                                    {{ $plan->max_users < 0 ? __('Unlimited') : $plan->max_users }}
                                    {{ __('Users') }}
                                </li>
                                {{-- <li>
                                        <span class="theme-avtar">
                                            <i class="text-primary ti ti-circle-plus"></i></span>
                                        {{ $plan->max_employee < 0 ? __('Unlimited') : $plan->max_employee }}
                                        {{ __('Employee') }}
                                    </li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                @if (isset($admin_payment_setting['is_manually_enabled']) && $admin_payment_setting['is_manually_enabled'] == 'on')
                    <div id="useradd-14" class="card shadow-none rounded-0 border-bottom">
                        <div class="card-header">
                            <h5 class=" h6 mb-0">{{ __('Manually') }}</h5>
                        </div>
                        <div class="card-body">
                            <label>{{ __('Requesting manual payment for the planned amount for the subscriptions paln.') }}</label>
                        </div>
                        <div class="card-footer text-end">


                            {{-- @if (empty($planReqs))
                                <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                    class="btn btn-primary btn-icon m-1" data-title="{{ __('Send Request') }}" data-bs-toggle="tooltip">
                                    <span class="btn-inner--icon">{{__('Send Request')}}</span>
                                </a>
                            @else
                                <a href="{{ route('response.request', [$planReqs->id,0]) }}"
                                    class="btn btn-danger btn-icon m-1" data-title="{{ __('Cancel Request') }}" data-bs-toggle="tooltip">
                                    <span class="btn-inner--icon">{{__('Cancel Request')}}</span>
                                </a>
                            @endif --}}
                        </div>
                    </div>
                @endif
                @if (isset($admin_payment_setting['is_bank_enabled']) && $admin_payment_setting['is_bank_enabled'] == 'on')
                    <div id="bank-payment" class="card shadow-none rounded-0 border-bottom">
                        <form class="w3-container w3-display-middle w3-card-4" method="POST"
                            action="{{ route('plan.pay.with.bank') }}" enctype='multipart/form-data'>
                            @csrf
                            <div class="card-header">
                                <h5 class=" h6 mb-0">{{ __('Bank Transfer') }}</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label"><b>{{ __('Bank Details:') }}</b></label>
                                            <div class="form-group">

                                                @if (isset($admin_payment_setting['bank_details']) && !empty($admin_payment_setting['bank_details']))
                                                    {!! $admin_payment_setting['bank_details'] !!}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label"> {{ __('Payment Receipt') }}</label>
                                            <div class="form-group">
                                                <input type="file" name="payment_receipt" class="form-control mb-3"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <form>
                                        <div class="row mt-3">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="bank_coupon"
                                                        class="form-label">{{ __('Coupon') }}</label>
                                                    <input type="text" id="bank_coupon" name="coupon"
                                                        class="form-control coupon"
                                                        placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mt-4">
                                                <div class="form-group apply-bank-btn-coupon">
                                                    <a href="#"
                                                        class="btn btn-primary align-items-center apply-coupon"
                                                        data-from="bank">{{ __('Apply') }}</a>
                                                </div>
                                            </div>
                                            <div class="col-6 text-right">
                                                <b>{{ __('Plan Price') }}</b> : ${{ $plan->price }}<b
                                                    class="bank-coupon-price"></b>
                                            </div>
                                            <div class="col-6 text-right ">
                                                <b>{{ __('Net Amount') }}</b> : $<span class="bank-final-price">
                                                    {{ $plan->price }}
                                                </span></b>
                                                <small>(After coupon apply)</small>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-sm-12">
                                                    <div class="float-end">
                                                        <input type="hidden" name="plan_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                        <button class="btn btn-primary d-flex align-items-center"
                                                            type="submit">
                                                            <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                            (<span
                                                                class="bank-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="error" style="display: none;">
                                                    <div class='alert-danger alert'>
                                                        {{ __('Please correct the errors and try again.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif
                @if (isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on')
                    <div id="stripe-payment" class="card  shadow-none rounded-0 border-bottom">
                        <div class="card-header">
                            <h5>{{ __('Pay Using Stripe') }}</h5>
                            <small class="text-muted">{{ __('Details about your plan Stripe payment') }}</small>
                        </div>
                        <div class="card-body">
                            <form role="form" action="{{ route('stripe.post') }}" method="post"
                                class="require-validation" id="payment-form">
                                @csrf


                                <div class=" rounded stripe-payment-div">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">

                                            <div class="form-group">
                                                <label class="form-label" for="card-name-on"
                                                    class="form-label">{{ __('Name on card') }}</label>
                                                <input type="text" name="name" id="card-name-on"
                                                    class="form-control required inputFieldClass"
                                                    placeholder="{{ \Auth::user()->name }}">
                                            </div>

                                        </div>
                                        <div class="group row col-md-8 p-0 gap-3 gap-md-0">
                                            <div class="col-md-12 pb-2 form-group">
                                                <label class="form-label">
                                                    Card number
                                                </label>
                                                <div class="newrow">
                                                    <div id="card-number-element"
                                                        class="field stripe-input inputFieldClass"></div>

                                                    <div id="card-brand-container" class="p-CardBrandIcons">
                                                        <img style="width:40px;" class="p-CardBrandIcon" alt="visa"
                                                            src="https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4bdb47b03ac81d9945bfe.svg">
                                                    </div>

                                                    {{-- <span class="brand" id="card-brand-container">
                                                        <i id="brand-icon" class="fas fa-credit-card "></i>
                                                    </span> --}}
                                                </div>
                                                {{-- <div class="p-CardIcons Input">
                                                            <div style="position: relative;"><div class="p-CardBrandIcons p-CardBrandIcons--loaded p-CardBrandIcons--multiple" aria-describedby="cardBrandIconsDesc"><div class="p-CardBrandIcons-item"><img class="p-CardBrandIcon" alt="visa" src="https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4bdb47b03ac81d9945bfe.svg"></div>
                                                                <div class="p-CardBrandIcons-item"><img class="p-CardBrandIcon" alt="mastercard" src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130711885b5e41b28c9848f.svg"></div>
                                                                <div class="p-CardBrandIcons-item"><img class="p-CardBrandIcon" alt="amex" src="https://js.stripe.com/v3/fingerprinted/img/amex-a49b82f46c5cd6a96a6e418a6ca1717c.svg"></div>
                                                                <div class="p-CardBrandIcons-item p-CardBrandIcons-more"><img class="p-CardBrandIcon" alt="discover" src="https://js.stripe.com/v3/fingerprinted/img/discover-ac52cd46f89fa40a29a0bfb954e33173.svg"><img class="p-CardBrandIcon" alt="diners" src="https://js.stripe.com/v3/fingerprinted/img/diners-fbcbd3360f8e3f629cdaa80e93abdb8b.svg"><img class="p-CardBrandIcon p-CardBrandIcon--visible" alt="jcb" src="https://js.stripe.com/v3/fingerprinted/img/jcb-271fd06e6e7a2c52692ffa91a95fb64f.svg"><img class="p-CardBrandIcon" alt="unionpay" src="https://js.stripe.com/v3/fingerprinted/img/unionpay-8a10aefc7295216c338ba4e1224627a1.svg"><img class="p-CardBrandIcon" alt="elo" src="https://js.stripe.com/v3/fingerprinted/img/elo-efe873e884e6c9eb817f23a120caaa3e.svg"></div></div>
                                                                <div class="p-CardKnownIcon"><div class="u-visually-hidden" id="knownCardBrandDesc"></div></div>
                                                                <div class="p-CardErrorIcon">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="var(--colorIconCardError)" role="presentation">
                                                                        <path opacity=".4" fill-rule="evenodd" clip-rule="evenodd" d="M15.337 4A5.5 5.5 0 1023 11.663V18a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h12.337zm6.707.293c.239.202.46.424.662.663a2.01 2.01 0 00-.662-.663zM3 9a1 1 0 011-1h5a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V9z"></path>
                                                                        <path opacity=".6" fill-rule="evenodd" clip-rule="evenodd" d="M4 15a1 1 0 100 2h1.5a1 1 0 100-2H4zm4.8 0a1 1 0 100 2h1.5a1 1 0 100-2H8.8zm3.8 1a1 1 0 011-1h1.5a1 1 0 110 2h-1.5a1 1 0 01-1-1zm5.9-1a1 1 0 100 2H20a1 1 0 100-2h-1.5z"></path>
                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.5 14a5.5 5.5 0 110-11 5.5 5.5 0 010 11zm0-1.719a1.031 1.031 0 100-2.062 1.031 1.031 0 000 2.062zm0-7.906a.687.687 0 00-.688.688V8.5a.687.687 0 101.375 0V5.062a.687.687 0 00-.687-.687z"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </div> --}}

                                            </div>


                                            <div class="col-md-4 form-group">
                                                <label class="form-label">
                                                    Expiry date
                                                </label>
                                                <div id="card-expiry-element" class="field stripe-input inputFieldClass">
                                                </div>

                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="form-label">
                                                    CVC
                                                </label>
                                                <div id="card-cvc-element" class="field stripe-input inputFieldClass">
                                                </div>

                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="form-label">
                                                    Postal code
                                                </label>
                                                <input id="postal-code" name="postal_code"
                                                    class=" field_zip form-control inputFieldClass" placeholder="90210" />

                                            </div>
                                           

                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="form-label" for="card-name-on"
                                                    class="form-label">{{ __('Country') }}</label>
                                                <select class="form-control inputFieldClass" id="country"
                                                    name="country" required>
                                                    <option value="">{{ __('Select Country') }}</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ $country->name == 'United States' ? 'selected' : '' }}>
                                                            {{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-8">
                                            <label for="card-element">Credit or debit card</label>

                                                <div id="card-element" style="    display: block;
                                                width: 100%;
                                                padding: 0.575rem 1rem;
                                                font-size: 0.875rem;
                                                font-weight: 400;
                                                line-height: 1.5;
                                                color: #293240;
                                                background-color: #ffffff;
                                                background-clip: padding-box;
                                                border: 1px solid #ced4da;
                                                -moz-appearance: none;
                                                appearance: none;
                                                border-radius: 6px;
                                                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;"></div>
                                                <div id="card-errors" role="alert"></div>
                                            </div> --}}
                                        <div class="col-md-8 mt-2">




                                            <div class="form-group" style="flex-grow: 1;">
                                                <label for="stripe_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <input type="text" id="stripe_coupon" name="coupon"
                                                        class="form-control coupon inputFieldClass" data-from="stripe"
                                                        placeholder="{{ __('Enter Coupon Code') }}">
                                                    <div class=" coupon-apply-btn">
                                                        <div class="form-group apply-stripe-btn-coupon mb-0">
                                                            <a href="#"
                                                                class="btn btn-primary align-items-center apply-coupon"
                                                                data-from="stripe">{{ __('Apply') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>





                                        </div>
                                        <div class="col-md-8">
                                            <div class="text-danger" id="card-errors" role="alert"></div>
                                        </div>

                                        <div class="col-12 text-right stripe-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="stripe-coupon-price"></b>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="stripe-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>

                                                    <div id="payment-message" class="hidden"></div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on')
                    <div class="card  shadow-none rounded-0 border-bottom" id="paypal-payment">
                        <form role="form" action="{{ route('plan.pay.with.paypal') }}" method="post"
                            id="paypal-payment-form" class="w3-container w3-display-middle w3-card-4">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Pay Using Paypal') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Paypal payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="paypal_coupon" class="form-label">{{ __('Coupon') }}</label>
                                                <input type="text" id="paypal_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group apply-paypal-btn-coupon mt-4">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="paypal">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right paypal-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="paypal-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="paypal-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on')
                    <div id="paystack-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.paystack') }}" method="post"
                            id="paystack-payment-form" class="w3-container w3-display-middle w3-card-4">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Paystack') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Paystack payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="paystack_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="paystack_coupon" name="coupon"
                                                    class="form-control coupon" data-from="paystack"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="paystack">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right paystack-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="paystack-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="button" id="pay_with_paystack">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="paystack-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')
                    <div id="flutterwave-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.flaterwave') }}" method="post"
                            class="w3-container w3-display-middle w3-card-4" id="flaterwave-payment-form">
                            @csrf <div class="card-header">
                                <h5>{{ __('Flutterwave') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Flutterwave payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="flaterwave_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="flaterwave_coupon" name="coupon"
                                                    class="form-control coupon" data-from="flaterwave"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="flaterwave">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right flaterwave-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="flaterwave-coupon-price"></b>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="button" id="pay_with_flaterwave">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="flaterwave-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')
                    <div id="razorpay-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.razorpay') }}" method="post"
                            class="w3-container w3-display-middle w3-card-4" id="razorpay-payment-form">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Razorpay') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Razorpay payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label for="razorpay_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="razorpay_coupon" name="coupon"
                                                    class="form-control coupon" data-from="razorpay"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary  align-items-center apply-coupon"
                                                    data-from="razorpay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right razorpay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="razorpay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="button" id="pay_with_razorpay">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="razorpay-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on')
                    <div id="paytm-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.paytm') }}" method="post"
                            class="w3-container w3-display-middle w3-card-4" id="paytm-payment-form">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Paytm') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Paytm payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label for="paytm_coupon"
                                                    class="form-label text-dark">{{ __('Mobile Number') }}</label>
                                                <input type="text" id="mobile" name="mobile"
                                                    class="form-control mobile" data-from="mobile"
                                                    placeholder="{{ __('Enter Mobile Number') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label for="paytm_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="paytm_coupon" name="coupon"
                                                    class="form-control coupon" data-from="paytm"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="paytm">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right paytm-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="paytm-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit" id="pay_with_paytm">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="paytm-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on')
                    <div id="mercadopago-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.mercado') }}" method="post"
                            class="w3-container w3-display-middle w3-card-4" id="mercado-payment-form">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Mercado Pago') }}</h5>
                                <small
                                    class="text-muted">{{ __('Details about your plan Mercado Pago payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label for="mercado_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="mercado_coupon" name="coupon"
                                                    class="form-control coupon" data-from="mercado"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="mercado">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right mercado-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="mercado-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit" id="pay_with_paytm">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="mercado-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on')
                    <div id="mollie-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.mollie') }}" method="post"
                            class="w3-container w3-display-middle w3-card-4" id="mollie-payment-form">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Mollie') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Mollie payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label for="mollie_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="mollie_coupon" name="coupon"
                                                    class="form-control coupon" data-from="mollie"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="mollie">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right mollie-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="mollie-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit" id="pay_with_mollie">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="mollie-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on')
                    <div id="skrill-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.skrill') }}" method="post"
                            class="w3-container w3-display-middle w3-card-4" id="skrill-payment-form">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Skrill') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Skrill payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label for="skrill_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="skrill_coupon" name="coupon"
                                                    class="form-control coupon" data-from="skrill"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="skrill">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right skrill-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="skrill-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit" id="pay_with_skrill">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="skrill-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $skrill_data = [
                                            'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                            'user_id' => 'user_id',
                                            'amount' => 'amount',
                                            'currency' => 'currency',
                                        ];
                                        session()->put('skrill_data', $skrill_data);
                                    @endphp
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on')
                    <div id="coingate-payment" class="card  shadow-none rounded-0 border-bottom ">
                        <form role="form" action="{{ route('plan.pay.with.coingate') }}" method="post"
                            class="w3-container w3-display-middle w3-card-4" id="coingate-payment-form">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Coingate') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Coingate payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-10">
                                            <div class="form-group">
                                                <label for="coingate_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="coingate_coupon" name="coupon"
                                                    class="form-control coupon" data-from="coingate"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="coingate">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right coingate-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="coingate-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit" id="pay_with_coingate">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="coingate-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on')
                    <div id="paymentwall-payment" class="card shadow-none rounded-0 border-bottom">
                        <form role="form" action="{{ route('paymentwall') }}" method="post"
                            id="paymentwall-payment-form" class="w3-container w3-display-middle w3-card-4">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('PaymentWall') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan PaymentWall payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="paymentwall_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="paymentwall_coupon" name="coupon"
                                                    class="form-control coupon" data-from="paymentwall"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group pt-3 mt-3">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="paymentwall">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right paymentwall-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="paymentwall-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit" id="pay_with_paymentwall">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{ __('Pay Now') }}
                                                        (<span
                                                            class="paymentwall-final-price">{{ $admin_payment_setting['currency_symbol'] }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_toyyibpay_enabled']) && $admin_payment_setting['is_toyyibpay_enabled'] == 'on')
                    <div id="toyyibpay_payment" class="card shadow-none rounded-0 border-bottom">
                        <form role="form" action="{{ route('plan.pay.with.toyyibpay') }}" method="post"
                            id="toyyibpay-payment-form" class="w3-container w3-display-middle w3-card-4">
                            @csrf
                            <div class="card-header">
                                <h5>{{ __('Toyyibpay') }}</h5>
                                <small class="text-muted">{{ __('Details about your plan Toyyibpay payment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="toyyibpay_coupon"
                                                    class="form-label text-dark">{{ __('Coupon') }}</label>
                                                <input type="text" id="toyyibpay_coupon" name="coupon"
                                                    class="form-control coupon" data-from="toyyibpay"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3 apply-toyyibpay-btn-coupon">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="toyyibpay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right toyyibpay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="toyyibpay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit" id="pay_with_toyyibpay">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i>
                                                        {{ __('Pay Now') }} (<span
                                                            class="toyyibpay-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)</button>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_payfast_enabled']) && $admin_payment_setting['is_payfast_enabled'] == 'on')
                    <div id="payfast_payment" class="card shadow-none rounded-0 border-bottom">
                        <div class="card-header">
                            <h5>{{ __('Payfast') }}</h5>
                        </div>

                        @if (
                            $admin_payment_setting['is_payfast_enabled'] == 'on' &&
                                !empty($admin_payment_setting['payfast_merchant_id']) &&
                                !empty($admin_payment_setting['payfast_merchant_key']) &&
                                !empty($admin_payment_setting['payfast_signature']) &&
                                !empty($admin_payment_setting['payfast_mode']))
                            <div
                                {{ ($admin_payment_setting['is_payfast_enabled'] == 'on' &&
                                    !empty($admin_payment_setting['payfast_merchant_id']) &&
                                    !empty($admin_payment_setting['payfast_merchant_key'])) == 'on'
                                    ? 'active'
                                    : '' }}>
                                @php
                                    $pfHost = $admin_payment_setting['payfast_mode'] == 'sandbox' ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
                                @endphp
                                <form role="form" action={{ 'https://' . $pfHost . '/eng/process' }} method="post"
                                    class="require-validation" id="payfast-form">
                                    <div class="card-body  ">

                                        <div class="row mt-3">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="payfast_coupon"
                                                        class="form-label text-dark">{{ __('Coupon') }}</label>
                                                    <input type="text" id="payfast_coupon" name="coupon"
                                                        class="form-control coupon" data-from="payfast"
                                                        placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-2 coupon-apply-btn">
                                                <div class="form-group pt-3 mt-3 apply-payfast-btn-coupon">
                                                    <a href="#"
                                                        class="btn btn-primary align-items-center apply-coupon"
                                                        data-from="payfast">{{ __('Apply') }}</a>
                                                </div>
                                            </div>
                                            <div class="col-12 text-right payfast-coupon-tr" style="display: none">
                                                <b>{{ __('Coupon Discount') }}</b> : <b class="payfast-coupon-price"></b>
                                            </div>

                                            <div id="get-payfast-inputs"></div>
                                            <div class="row mt-2">
                                                <div class="col-sm-12">
                                                    <div class="float-end">
                                                        <input type="hidden" name="plan_id" id="plan_id"
                                                            value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                        <button class="btn btn-primary d-flex align-items-center"
                                                            type="submit" id="pay_with_payfast">
                                                            <i class="mdi mdi-cash-multiple mr-1"></i>
                                                            {{ __('Pay Now') }} (<span
                                                                class="payfast-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_iyzipay_enabled']) && $admin_payment_setting['is_iyzipay_enabled'] == 'on')
                    <div id="useradd-16" class="card shadow-none rounded-0 border-bottom">
                        <form class="w3-container w3-display-middle w3-card-4" method="POST" id="iyzipay-payment-form"
                            action="{{ route('iyzipay.payment.init') }}">
                            @csrf <div class="card-header">
                                <h5>{{ __('IyziPay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="iyzipay_coupon"
                                                    class="form-label">{{ __('Coupon') }}</label>
                                                <input type="text" id="iyzipay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3  apply-iyzipay-btn-coupon">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="iyzipay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right iyzipay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="iyzipay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i>
                                                        {{ __('Pay Now') }}
                                                        (<span
                                                            class="iyzipay-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_sspay_enabled']) && $admin_payment_setting['is_sspay_enabled'] == 'on')
                    <div id="useradd-17" class="card shadow-none rounded-0 border-bottom">
                        <form class="w3-container w3-display-middle w3-card-4" method="POST" id="sspay-payment-form"
                            action="{{ route('plan.sspaypayment') }}">
                            @csrf <div class="card-header">
                                <h5>{{ __('SSPay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="sspay_coupon" class="form-label">{{ __('Coupon') }}</label>
                                                <input type="text" id="sspay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3 apply-sspay-btn-coupon">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="sspay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right sspay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="iyzipay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i>
                                                        {{ __('Pay Now') }}
                                                        (<span
                                                            class="sspay-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_paytab_enabled']) && $admin_payment_setting['is_paytab_enabled'] == 'on')
                    <div id="useradd-18" class="card shadow-none rounded-0 border-bottom">
                        <form class="w3-container w3-display-middle w3-card-4" method="POST" id="paytab-payment-form"
                            action="{{ route('plan.pay.with.paytab') }}">
                            @csrf <div class="card-header">
                                <h5>{{ __('PayTab') }}</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="sspay_coupon" class="form-label">{{ __('Coupon') }}</label>
                                                <input type="text" id="sspay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3 apply-sspay-btn-coupon">
                                                <a href="#" class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="sspay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right sspay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="iyzipay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i>
                                                        {{ __('Pay Now') }}
                                                        (<span
                                                            class="sspay-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_benefit_enabled']) && $admin_payment_setting['is_benefit_enabled'] == 'on')
                    <div id="useradd-19" class="card shadow-none rounded-0 border-bottom">
                        <form class="w3-container w3-display-middle w3-card-4" method="POST" id="benefit-payment-form"
                            action="{{ route('benefit.initiate') }}">
                            @csrf <div class="card-header">
                                <h5>{{ __('Benefit') }}</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="sspay_coupon"
                                                    class="form-label">{{ __('Coupon') }}</label>
                                                <input type="text" id="sspay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3 apply-sspay-btn-coupon">
                                                <a href="#"
                                                    class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="sspay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right sspay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="iyzipay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i>
                                                        {{ __('Pay Now') }}
                                                        (<span
                                                            class="sspay-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on')
                    <div id="useradd-20" class="card shadow-none rounded-0 border-bottom">
                        <form class="w3-container w3-display-middle w3-card-4" method="POST"
                            id="cashfree-payment-form" action="{{ route('plan.pay.with.cashfree') }}">
                            @csrf <div class="card-header">
                                <h5>{{ __('Cashfree') }}</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="sspay_coupon"
                                                    class="form-label">{{ __('Coupon') }}</label>
                                                <input type="text" id="sspay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3 apply-sspay-btn-coupon">
                                                <a href="#"
                                                    class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="sspay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right sspay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="iyzipay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i>
                                                        {{ __('Pay Now') }}
                                                        (<span
                                                            class="sspay-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif

                @if (isset($admin_payment_setting['is_aamarpay_enabled']) && $admin_payment_setting['is_aamarpay_enabled'] == 'on')
                    <div id="useradd-21" class="card shadow-none rounded-0 border-bottom">
                        <form class="w3-container w3-display-middle w3-card-4" method="POST"
                            id="aamarpay-payment-form" action="{{ route('plan.pay.with.aamarpay') }}">
                            @csrf <div class="card-header">
                                <h5>{{ __('Aamarpay') }}</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row mt-3">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="sspay_coupon"
                                                    class="form-label">{{ __('Coupon') }}</label>
                                                <input type="text" id="sspay_coupon" name="coupon"
                                                    class="form-control coupon"
                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn">
                                            <div class="form-group pt-3 mt-3 apply-sspay-btn-coupon">
                                                <a href="#"
                                                    class="btn btn-primary align-items-center apply-coupon"
                                                    data-from="sspay">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right sspay-coupon-tr" style="display: none">
                                            <b>{{ __('Coupon Discount') }}</b> : <b class="iyzipay-coupon-price"></b>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <div class="float-end">
                                                    <input type="hidden" name="plan_id"
                                                        value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                                    <button class="btn btn-primary d-flex align-items-center"
                                                        type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i>
                                                        {{ __('Pay Now') }}
                                                        (<span
                                                            class="sspay-final-price">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price }}</span>)
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="error" style="display: none;">
                                                <div class='alert-danger alert'>
                                                    {{ __('Please correct the errors and try again.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
