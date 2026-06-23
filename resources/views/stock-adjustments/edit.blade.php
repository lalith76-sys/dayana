@extends('layouts.app')

@section('title', 'Edit Stock Adjustment')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Adjustment: {{ $stockAdjustment->adjustment_number }}</h3>
    </div>
    <form action="{{ route('stock-adjustments.update', $stockAdjustment) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="item_id">Item <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required style="width:100%;">
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id', $stockAdjustment->item_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->item_code }} - {{ $item->item_name }} (Stock: {{ $item->stock_quantity }})
                        </option>
                    @endforeach
                </select>
                @error('item_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="type">Adjustment Type <span class="text-danger">*</span></label>
                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="addition" {{ old('type', $stockAdjustment->type) == 'addition' ? 'selected' : '' }}>Addition (Stock In)</option>
                    <option value="deduction" {{ old('type', $stockAdjustment->type) == 'deduction' ? 'selected' : '' }}>Reduction (Stock Out)</option>
                    <option value="defective" {{ old('type', $stockAdjustment->type) == 'defective' ? 'selected' : '' }}>Mark as Defective</option>
                </select>
                @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $stockAdjustment->quantity) }}" min="1" required>
                @error('quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="cost_price">Cost Price</label>
                <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price', $stockAdjustment->cost_price) }}" min="0">
                @error('cost_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $stockAdjustment->date->format('Y-m-d')) }}">
                @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="reason">Reason <span class="text-danger">*</span></label>
                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason', $stockAdjustment->reason) }}</textarea>
                @error('reason') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Adjustment</button>
            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
