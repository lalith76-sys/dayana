@extends('layouts.app')

@section('title', 'Create Item')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Item</h3>
    </div>
    <form action="{{ route('items.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id">Category <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('category_id') is-invalid @enderror" 
                                id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="item_name">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                               id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                        @error('item_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                               id="brand" name="brand" value="{{ old('brand') }}">
                        @error('brand')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="barcode">Barcode</label>
                        <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                               id="barcode" name="barcode" value="{{ old('barcode') }}">
                        @error('barcode')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cost_price">Cost Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs.</span>
                            </div>
                            <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                                   id="cost_price" name="cost_price" value="{{ old('cost_price', 0) }}" required>
                        </div>
                        @error('cost_price')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs.</span>
                            </div>
                            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" 
                                   id="selling_price" name="selling_price" value="{{ old('selling_price', 0) }}" required>
                        </div>
                        @error('selling_price')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required>
                        @error('stock_quantity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="reorder_level">Reorder Level <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" 
                               id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 10) }}" required>
                        @error('reorder_level')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save Item</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $('.select2').select2({theme: 'bootstrap4'});
</script>
@endpush