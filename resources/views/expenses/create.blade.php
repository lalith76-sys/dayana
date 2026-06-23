@extends('layouts.app')

@section('title', 'Add Expense')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Record New Expense</h3>
    </div>
    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Expense Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                               name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        @error('expense_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Expense Type <span class="text-danger">*</span></label>
                        <select class="form-control select2 @error('expense_type_id') is-invalid @enderror" 
                                name="expense_type_id" required>
                            <option value="">Select Type</option>
                            @foreach($expenseTypes as $id => $name)
                                <option value="{{ $id }}" {{ old('expense_type_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('expense_type_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">Rs.</span></div>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                   name="amount" value="{{ old('amount', 0) }}" required>
                        </div>
                        @error('amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Attachment</label>
                        <input type="file" class="form-control" name="attachment">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          name="description" rows="3" required>{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save Expense</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>$('.select2').select2({theme: 'bootstrap4'});</script>
@endpush