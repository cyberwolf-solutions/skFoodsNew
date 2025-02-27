@extends('layouts.master')
@section('title')
    {{ $title }}
@endsection
@section('content')
    <!-- start page title -->
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
                    {{-- Add Buttons Here --}}
                    @can('create purchases')
                        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-icon" data-bs-toggle="tooltip"
                            title="Create">
                            <i class="ri-add-line"></i>
                        </a>
                    @endcan
                    {{-- <a href="{{route('purchase.Reports')}}" >
                        <button  class="btn btn-border btn-danger">Reports</button>
                    </a> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle" id="example">
                        <thead class="table-light">
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td>{{ $settings->purchase($item->id) }}</td>
                                    <td>{{ $item->supplier->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->date)->format($settings->date_format) }}</td>
                                    <td>{{ $settings->currency }} {{ number_format($item->total, 2) }}</td>
                                    <td>{{ $item->payment_status }}</td>
                                    <td>
                                        @can('view purchases')
                                            <a href="{{ route('purchases.show', [$item->id]) }}"
                                                class="btn btn-light btn-sm small btn-icon text-white">
                                                <i class="bi bi-eye" data-bs-toggle="tooltip" title="View"></i>
                                            </a>
                                        @endcan
                                        @can('edit purchases')
                                            <a href="{{ route('purchases.edit', [$item->id]) }}"
                                                class="btn btn-secondary btn-sm small btn-icon">
                                                <i class="bi bi-pencil-square" data-bs-toggle="tooltip" title="Edit"></i>
                                            </a>
                                        @endcan
                                        @can('add-payments purchases')
                                            <a href="javascript:void(0)"
                                                data-url="{{ route('purchases.payment', ['id' => encrypt($item->id)]) }}"
                                                data-title="Add Purchase Payment" data-location="centered"
                                                data-ajax-popup="true" data-bs-toggle="tooltip" title="Add Payment"
                                                class="btn btn-sm btn-info text-dark"><i class="bi bi-currency-dollar"></i>
                                            </a>
                                        @endcan
                                        @can('view-payments purchases')
                                            <a href="javascript:void(0)" data-url="{{ route('purchases.payments.view') }}"
                                                data-title="Purchase Payments" data-size="lg" data-location="centered"
                                                data-ajax-popup="true" data-bs-toggle="tooltip" title="View Payments"
                                                class="btn btn-sm btn-soft-warning"><i class="mdi mdi-cash-multiple"></i>
                                            </a>
                                        @endcan
                                        @if($item->status == 0.00)
                                        @can('delete purchases')
                                            <a href="javascript:void(0)" 
                                                data-url="{{ route('purchases.payments.approve', ['id' => $item->id]) }}"
                                                data-title="Purchase Payments" 
                                                data-size="lg" 
                                                data-location="centered"
                                                data-bs-toggle="tooltip" 
                                                title="Confirm"
                                                class="btn btn-sm btn-soft-success approve-btn">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                        @endcan
                                    @endif
                                    

                                        @can('delete purchases')
                                            <a href="javascript:void(0)"
                                                data-url="{{ route('purchases.destroy', [$item->id]) }}"
                                                class="btn btn-danger btn-sm small btn-icon delete_confirm">
                                                <i class="bi bi-trash" data-bs-toggle="tooltip" title="Delete"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-dark" id="approvalModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i> Confirm Approval
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="fs-5 text-muted mb-3">
                        Are you sure you want to <span class="fw-bold text-success">approve</span> this purchase?
                    </p>
                    <i class="bi bi-question-circle text-primary" style="font-size: 3rem;"></i>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success px-4 py-2" id="confirmApproval">
                        <i class="bi bi-check-lg"></i> Yes, Approve
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- JavaScript to Handle Modal Confirmation -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let approveBtn = document.querySelector(".approve-btn");
            let confirmBtn = document.getElementById("confirmApproval");
            let approvalUrl = "";

            approveBtn.addEventListener("click", function() {
                approvalUrl = this.getAttribute("data-url");
                let modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                modal.show();
            });

            confirmBtn.addEventListener("click", function() {
                if (approvalUrl) {
                    window.location.href = approvalUrl; // Redirect to the approval route
                }
            });
        });
    </script>
@endsection
