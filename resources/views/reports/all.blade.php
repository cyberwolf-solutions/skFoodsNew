@extends('layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-sm-0">{{ $title }}</h3>

                    <ol class="breadcrumb m-0 mt-2">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>

                        @foreach ($breadcrumbs as $breadcrumb)
                            <li class="breadcrumb-item {{ $breadcrumb['active'] ? 'active' : '' }}">
                                @if (!$breadcrumb['active'])
                                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                                @else
                                    {{ $breadcrumb['label'] }}
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
                <div class="page-title-right">
                    <button id="printButton" class="btn btn-primary">
                        <i class="bi bi-printer-fill"></i>
                    </button>

                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="col-12">
            <div class="row">
                <div class="col-md-10">
                    <form method="GET" action="{{ route('final') }}" id="filter-form">
                        <div class="row p-3">
                            <div class="col-md-3 mb-3">
                                <label for="from_date" class="form-label">From</label>
                                <input type="date" id="from_date" name="from_date" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="to_date" class="form-label">To</label>
                                <input type="date" id="to_date" name="to_date" class="form-control" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-end mb-3">
                                <button type="submit" id="filter" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-1 ">
                    <div class="row p-3">
                        <label for="room_type" class="form-label">*</label>
                        <a href="{{ route('final') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div id="printableContent">
            <div class="justify-content-center mt-3">
                <div class="row">
                    <div class="card card-flush col-md-12">
                        <h1 class="repo_title mt-2 fs-4 text-capitalize">Income</h1>

                        <div class="repo_details_lines">

                            <hr>
                            <div class="repo_amount">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="fs-5 fw-light text-capitalize fw-bold">Sales</h1>
                                    </div>
                                    <div class="col-md-6">
                                        <h3 class="fs-6 fw-light">LKR. {{ number_format($orderTotal, 2) }}</h3>
                                        {{-- <h3 class="fs-6 fw-light">{{ number_format($sumsByCurrency1['LKR'], 2) }}</h3> --}}
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="card card-flush col-md-12">
                        <h1 class="repo_title mt-2 fs-4 text-capitalize">Expenses</h1>
                        <hr>
                        <div class="repo_details_lines">


                            <div class="repo_amount">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h1 class="fs-5 fw-light text-capitalize fw-bold">Ingredients</h1>
                                    </div>
                                    <div class="col-md-6">
                                        <h3 class="fs-6 fw-light">LKR. {{ number_format($ingredientsTotal, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        document.getElementById('printButton').addEventListener('click', function() {
            // alert("ok");
            var printContents = document.getElementById('printableContent').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const checkinDateInput = document.getElementById('from_date');
            const checkoutDateInput = document.getElementById('to_date');

            checkinDateInput.addEventListener('change', function() {
                const checkinDate = new Date(checkinDateInput.value);
                if (!isNaN(checkinDate.getTime())) {

                    const checkoutDate = new Date(checkinDate);
                    checkoutDate.setDate(checkinDate.getDate() + 1);


                    const year = checkoutDate.getFullYear();
                    const month = String(checkoutDate.getMonth() + 1).padStart(2, '0');
                    const day = String(checkoutDate.getDate()).padStart(2, '0');
                    const formattedDate = `${year}-${month}-${day}`;


                    checkoutDateInput.value = formattedDate;
                }
            });
        });
    </script>
@endsection
