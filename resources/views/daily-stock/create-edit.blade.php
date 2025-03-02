

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
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="card">
            <div class="card-body">
                <form method="POST" class="ajax-form"
                    action="{{ $is_edit ? route('daily-stock.update', $data->id) : route('daily-stock.store') }}">
                    @csrf
                    @if ($is_edit)
                        @method('PATCH')
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3 required">
                            <label for="ingredient" class="form-label">Ingredient</label>
                            <select name="ingredient" class="form-control js-example-basic-single" id="ingredient">
                                <option value="">Select...</option>
                                @foreach ($data as $item)
                                    <option value="{{ $item->id }}" data-quantity="{{ $item->quantity }}">
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 required">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control" required />
                        </div>
                    </div>

                    <table class="table table-bordered mt-3" id="ingredientTable">
                        <thead>
                            <tr>
                                <th>Ingredient</th>
                                <th>Available Stock Quantity</th>
                                <th>Kitchen Consumption Quantity</th>
                                <th>Products Made</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="row mb-3">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-light me-2"
                                onclick="window.location='{{ route('daily-stock.index') }}'">Cancel</button>
                            <button class="btn btn-primary">{{ $is_edit ? 'Update' : 'Create' }}</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('ingredient').addEventListener('change', function() {
            var ingredientSelect = document.getElementById('ingredient');
            var selectedOption = ingredientSelect.options[ingredientSelect.selectedIndex];
            var ingredientName = selectedOption.text;
            var ingredientId = selectedOption.value;
            var stockQuantity = selectedOption.getAttribute('data-quantity');

            if (ingredientId) {
                var tableBody = document.querySelector('#ingredientTable tbody');
                var existingRow = tableBody.querySelector(`tr[data-id='${ingredientId}']`);

                if (!existingRow) {
                    var newRow = document.createElement('tr');
                    newRow.setAttribute('data-id', ingredientId);
                    newRow.innerHTML = `
                        <td><input type="hidden" name="ingredients[]" value="${ingredientId}">${ingredientName}</td>
                        <td><input type="text" name="stock_quantity[]" class="form-control" value="${stockQuantity}" readonly></td>
                        <td><input type="text" name="kitchen_quantity[]" class="form-control" required></td>
                        <td><input type="text" name="products_made[]" class="form-control" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm removeRow">Remove</button></td>
                    `;
                    tableBody.appendChild(newRow);
                }
            }
        });

        document.querySelector('#ingredientTable').addEventListener('click', function(e) {
            if (e.target.classList.contains('removeRow')) {
                var row = e.target.closest('tr');
                row.remove();
            }
        });

        document.getElementById('date').value = new Date().toISOString().split('T')[0];
    </script>
@endsection
