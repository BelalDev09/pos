@csrf

@isset($product)
    @method('PUT')
@endisset

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                            value="{{ old('sku', $product->sku ?? '') }}">
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                            value="{{ old('barcode', $product->barcode ?? '') }}">
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Classification</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">Select brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tax Rate</label>
                    <select name="tax_rate_id" class="form-select">
                        <option value="">No tax</option>
                        @foreach ($taxRates as $taxRate)
                            <option value="{{ $taxRate->id }}"
                                @selected(old('tax_rate_id', $product->tax_rate_id ?? '') == $taxRate->id)>
                                {{ $taxRate->name }} ({{ $taxRate->rate }}%)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="product_type" class="form-select" required>
                        @foreach (['standard' => 'Standard', 'service' => 'Service', 'composite' => 'Composite'] as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('product_type', $product->product_type ?? 'standard') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pricing</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cost <span class="text-danger">*</span></label>
                        <input type="number" step="0.0001" min="0" name="cost_price"
                            class="form-control @error('cost_price') is-invalid @enderror"
                            value="{{ old('cost_price', $product->cost_price ?? 0) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Selling <span class="text-danger">*</span></label>
                        <input type="number" step="0.0001" min="0" name="selling_price"
                            class="form-control @error('selling_price') is-invalid @enderror"
                            value="{{ old('selling_price', $product->selling_price ?? 0) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Wholesale</label>
                        <input type="number" step="0.0001" min="0" name="wholesale_price" class="form-control"
                            value="{{ old('wholesale_price', $product->wholesale_price ?? '') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Min Price</label>
                        <input type="number" step="0.0001" min="0" name="min_selling_price" class="form-control"
                            value="{{ old('min_selling_price', $product->min_selling_price ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">POS & Inventory</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Unit <span class="text-danger">*</span></label>
                    <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit ?? 'pcs') }}"
                        required>
                </div>

                @foreach ([
                    'track_stock' => 'Track Stock',
                    'allow_negative_stock' => 'Allow Negative Stock',
                    'has_variants' => 'Has Variants',
                    'is_active' => 'Active',
                    'is_pos_visible' => 'Visible in POS',
                    'track_expiry' => 'Track Expiry',
                    'track_batch' => 'Track Batch',
                ] as $field => $label)
                    <input type="hidden" name="{{ $field }}" value="0">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1"
                            id="{{ $field }}" @checked(old($field, $product->{$field} ?? in_array($field, ['track_stock', 'is_active', 'is_pos_visible'], true)))>
                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update Product' : 'Create Product' }}</button>
</div>
