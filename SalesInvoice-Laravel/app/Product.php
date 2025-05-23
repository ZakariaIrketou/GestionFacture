<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'serial_number',
        'model',
        'category_id',
        'sales_price',
        'unit_id',
        'tax_id',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function tax()
    {
        return $this->belongsTo('App\Tax');
    }

    // Relationship with ProductSupplier (previously additionalProduct)
    public function productSuppliers()
    {
        return $this->hasMany('App\ProductSupplier');
    }

    // Access suppliers through the pivot table
    public function suppliers()
    {
        return $this->belongsToMany('App\Supplier', 'product_suppliers')
            ->using('App\ProductSupplier')
            ->withPivot('price');
    }

    public function sale()
    {
        return $this->hasMany('App\Sale');
    }

    public function invoice()
    {
        return $this->belongsToMany('App\Invoice');
    }
}
