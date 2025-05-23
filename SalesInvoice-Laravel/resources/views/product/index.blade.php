@extends('layouts.master')

@section('title', 'Product List')
@section('content')
@include('partials.header')
@include('partials.sidebar')

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-th-list"></i> Product Table</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb side">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item">Products</li>
            <li class="breadcrumb-item active"><a href="#">All Products</a></li>
        </ul>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('product.create') }}">
            <i class="fa fa-plus"></i> Add Product
        </a>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <table class="table table-hover table-bordered" id="sampleTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Model</th>
                                <th>Serial</th>
                                <th>Sales Price</th>
                                <th>Purchase Price</th>
                                <th>Supplier</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($additional as $add)
                            <tr>
                                <td>{{ isset($add->product->name) ? $add->product->name : 'N/A' }}</td>
                                <td>{{ isset($add->product->model) ? $add->product->model : 'N/A' }}</td>
                                <td>{{ isset($add->product->serial_number) ? $add->product->serial_number : 'N/A' }}</td>
                                <td>{{ isset($add->product->sales_price) ? number_format($add->product->sales_price, 2) : 'N/A' }}</td>
                                <td>{{ isset($add->price) ? number_format($add->price, 2) : 'N/A' }}</td>
                                <td>{{ isset($add->supplier->name) ? $add->supplier->name : 'N/A' }}</td>
                                <td>
                                    @if(isset($add->product->image) && $add->product->image && file_exists(public_path('images/product/'.$add->product->image)))
                                    <img width="40" src="{{ asset('images/product/'.$add->product->image) }}" alt="Product Image" class="img-thumbnail">
                                    @else
                                    <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($add->product) && $add->product->id)
                                    <a href="{{ route('product.edit', $add->product->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteProduct($add,product,id)" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $add->product->id }}" action="{{ route('product.destroy', $add->product->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @else
                                    <button class="btn btn-sm btn-primary" disabled title="Edit unavailable">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" disabled title="Delete unavailable">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/plugins/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/plugins/dataTables.bootstrap.min.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#sampleTable').DataTable();
    });

    function deleteProduct(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush