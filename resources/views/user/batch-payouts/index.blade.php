
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from www.radixtouch.in/templates/admin/otika/source/light/subscribe.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 12 Mar 2021 14:30:52 GMT -->
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Emerald Farms Batch Payout</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <meta id="csrf" name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
<div class="loader"></div>
<div id="app">
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12">
                    <div class="text-center mb-4">
                        <img alt="image" src="{{asset('assets/img/logo-icon.png')}}" class="header-logo" style="max-width:70px;">
                    </div>
                    <div class="login-brand">
                        Emerald Farms Batch Payout
                    </div>
                    <div class="card card-primary">
                        <div class="card-body table-responsive">
                            <form method="GET">
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <label for="name_or_email">Name or Email</label>
                                        <input required type="text" class="form-control" name="name_or_email" id="name_or_email" placeholder="Name or Email">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label for="farm_cycle">Farm Cycle</label>
                                        <select required name="farm_cycle" id="farm_cycle" class="form-control">
                                            <option value="">Select Farm Cycle</option>
                                            @foreach($farms as $farm)
                                                <option value="{{ $farm }}">{{ $farm }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2 d-md-flex d-block align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-lg btn-round btn-primary">
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Queue</th>
                                    <th scope="col">Farm Cycle</th>
                                    <th scope="col">Payment Date</th>
                                    <th scope="col">Amount Due</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if (count($payouts) > 0)
                                        @foreach($payouts as $payout)
                                            <tr>
                                                <td>{{ $payout['name'] }}</td>
                                                <td>{{ $payout['email'] }}</td>
                                                <td>{{ $payout['queue'] }}</td>
                                                <td>{{ $payout['farm_cycle'] }}</td>
                                                <td>{{ date('M-Y', strtotime($payout['payment_date'])) }}</td>
                                                <td>{{ '₦ '.number_format((float)$payout['expected_returns']) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No records</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="simple-footer">
                        Copyright &copy; Emerald Farms {{ date('Y') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script src="{{ asset('assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/page/index.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
</body>


<!-- Mirrored from www.radixtouch.in/templates/admin/otika/source/light/subscribe.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 12 Mar 2021 14:30:52 GMT -->
</html>
