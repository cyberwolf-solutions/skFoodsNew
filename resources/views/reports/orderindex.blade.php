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
                    <button id="csvButton" class="btn btn-success">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-4">
            <select class="form-select" aria-label="Default select example">
                <option selected>Open this select menu</option>
                <option value="Dining" selected>Dining</option>
                <option value="TakeAway">TakeAway</option>
                <option value="RoomDelivery">Room Delivery</option>
              </select>
        </div>
    </div>
    --}}

    <div class="row">
        <div class="col-4">
            <select class="form-select" id="typeSelect" aria-label="Default select example">
                <option selected disabled>Select Type</option>
                <option value="Dining">Dining</option>
                <option value="TakeAway">TakeAway</option>
            </select>
         
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-dark" onclick="window.location.href='{{ route('order.ReportsIndex') }}'">
                <i class="bi bi-arrow-repeat"></i>

            </button>
        </div>
        
    </div>
    <div class="row mt-3">

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle" id="example">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Table</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>#{{ $settings->invoice($item->id) }}</td>
                                    @if ($item->customer_id == 0)
                                        <td>Walking Customer</td>
                                    @else
                                        <td>{{ $item->customer->name }}</td>
                                    @endif
                                    <td>{{ \Carbon\Carbon::parse($item->order_date)->format($settings->date_format) }}</td>
                                    <td>{{ $settings->currency }}
                                        {{ number_format($item->payment ? $item->payment->total : 0, 2) }}
                                    </td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ $item->table_id != 0 ? $item->table->availability : 'No Table' }}</td>
                                    <td>
                                        @can('view orders')
                                            <a href="javascript:void(0)" data-url="{{ route('orders.show', [$item->id]) }}"
                                                data-title="View Order" data-size="xl" data-location="centered"
                                                data-ajax-popup="true" data-bs-toggle="tooltip" title="View Order"
                                                class="btn btn-sm btn-light"><i class="mdi mdi-eye"></i>
                                            </a>
                                        @endcan
                                        <a href="{{ route('order.print', [$item->id]) }}" target="__blank"
                                            class="btn btn-sm btn-soft-warning ms-1" data-bs-toggle="tooltip"
                                            title="Print">
                                            <i class="mdi mdi-printer"></i>
                                        </a>
                                        @if ($item->table_id != 0)
                                            <a href="javascript:void(0)" data-url="{{ route('order.complete') }}"
                                                data-data='{"id":{{ $item->id }}}'
                                                class="btn btn-sm btn-soft-success ms-1 send-post-ajax"
                                                data-bs-toggle="tooltip" title="Complete">
                                                <i class="mdi mdi-check"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            {{-- @foreach ($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->customer_id }}</td>
                                    <td>{{ $item->room_id }}</td>
                                    <td>{{ $item->order_date }}</td>
                                    <td>{{ $item->table_id }}</td>
                                    <td>{{ $item->orderable_type }}</td>
                                    <td>{{ $item->orderable_id }}</td>
                                  
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#typeSelect').change(function() {
            var selectedType = $(this).val();
            window.location.href = "/search-by-type?type=" + selectedType;
        });

        // $(document).ready(function() {

        //     $('#typeSelect').change(function() {
        //         var selectedType = $(this).val();
        //         $.ajax({
        //             url: "{{ route('search.by.type') }}",
        //             method: 'GET',
        //             data: {
        //                 type: selectedType
        //             },
        //             success: function(response) {
        //                 console.log(response); // Process the response and display the data
        //                 // You could display the results in a table here.
        //             },
        //             error: function(xhr) {
        //                 alert("An error occurred: " + xhr.statusText);
        //                 console.error("An error occurred: " + xhr.statusText);
        //             }
        //         });
        //     });
        // });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("printButton").addEventListener("click", function() {
                var logoUrl = "{{ asset('build/images/logo.png') }}";
                var printWindow = window.open("", "_blank");
                printWindow.document.open();
                printWindow.document.write("<html><head><title>Print</title>");

                // Add a base tag for resolving relative URLs
                printWindow.document.write('<base href="{{ url('/') }}">');

                printWindow.document.write("<style>");
                printWindow.document.write(`
            /* Force colors on print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                background-color: #f8f9fa;
                font-family: 'Arial', sans-serif;
                color: #333;
                padding: 20px;
            }
            .container-fluid {
                padding: 20px;
            }
            .logo {
                max-width: 200px;
                margin-bottom: 10px;
            }
            .resort-name {
                font-size: 24px;
                color: #111316;
                font-weight: bold;
                text-align: center;
                margin: 0 auto;
            }
            hr {
                border: 1px solid #ddd;
                margin: 20px 0;
            }
            h4 {
                text-align: center;
                font-size: 22px;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                border-radius: 8px;
                overflow: hidden;
                background-color: #ffffff;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            }
            thead {
                background-color: #116530;
                color: white;
                text-align: left;
                font-weight: bold;
            }
            th, td {
                padding: 12px;
                border: 1px solid #ddd;
            }
            tbody tr:nth-child(odd) {
                background-color: #f8f9fa;
            }
            tbody tr:hover {
                background-color: #e9ecef;
            }
            .status-active {
                color: #28a745;
                font-weight: bold;
            }
            .status-inactive {
                color: #dc3545;
                font-weight: bold;
            }
        `);
                printWindow.document.write("</style></head><body>");
                printWindow.document.write('<div class="container-fluid">');

                // Centered Logo
                printWindow.document.write(
                    '<div style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">' +
                    '<img src="' + logoUrl +
                    '" class="logo img-fluid" style="height:100px;width:auto;">' +
                    "</div>"
                );

                printWindow.document.write("<hr>");
                printWindow.document.write("<h4>Sales Report</h4>");
                printWindow.document.write(document.getElementById("example").outerHTML);
                printWindow.document.write("</div>");
                printWindow.document.write("</body></html>");
                printWindow.document.close();

                // Wait for the content to load before printing
                printWindow.onload = function() {
                    printWindow.print();
                };
            });

            document.getElementById("csvButton").addEventListener("click", function() {
                var csvContent = "data:text/csv;charset=utf-8,";
                var rows = document.querySelectorAll("#example tbody tr");

                rows.forEach(function(row) {
                    var rowData = [];
                    row.querySelectorAll("td").forEach(function(cell) {
                        rowData.push(cell.innerText);
                    });
                    csvContent += rowData.join(",") + "\r\n";
                });

                var encodedUri = encodeURI(csvContent);
                var link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "sales.csv");
                document.body.appendChild(link);
                link.click();
            });
        });
    </script>
@endsection
