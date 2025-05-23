<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\ProductSupplier;
use App\Supplier;
use App\Tax;
use App\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $additional = ProductSupplier::with(['product', 'supplier'])->get();
        return view('product.index', compact('additional'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        $taxes = Tax::all();
        $units = Unit::all();

        return view('product.create', compact('categories', 'taxes', 'units', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3|unique:products|regex:/^[a-zA-Z ]+$/',
            'serial_number' => 'required',
            'model' => 'required|min:3',
            'category_id' => 'required',
            'sales_price' => 'required',
            'unit_id' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tax_id' => 'required',
        ]);

        $product = new Product();
        $product->name = $validated['name'];
        $product->serial_number = $validated['serial_number'];
        $product->model = $validated['model'];
        $product->category_id = $validated['category_id'];
        $product->sales_price = $validated['sales_price'];
        $product->unit_id = $validated['unit_id'];
        $product->tax_id = $validated['tax_id'];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/product/'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        foreach ($request->supplier_id as $key => $supplier_id) {
            $supplier = new ProductSupplier();
            $supplier->product_id = $product->id;
            $supplier->supplier_id = $supplier_id;
            $supplier->price = $request->supplier_price[$key];
            $supplier->save();
        }

        return redirect()->route('product.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::with(['suppliers'])->findOrFail($id);
        $additional = ProductSupplier::where('product_id', $id)->first();
        $suppliers = Supplier::all();
        $categories = Category::all();
        $taxes = Tax::all();
        $units = Unit::all();

        return view('product.edit', compact('product', 'additional', 'suppliers', 'categories', 'taxes', 'units'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|min:3|unique:products,name,' . $id . '|regex:/^[a-zA-Z ]+$/',
            'serial_number' => 'required',
            'model' => 'required|min:3',
            'category_id' => 'required',
            'sales_price' => 'required',
            'unit_id' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tax_id' => 'required',
            'supplier_id.*' => 'required|exists:suppliers,id',
            'supplier_price.*' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('images/product/' . $product->image))) {
                unlink(public_path('images/product/' . $product->image));
            }

            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('images/product/'), $imageName);
            $product->image = $imageName;
            $product->save();
        }

        ProductSupplier::where('product_id', $id)->delete();

        foreach ($request->supplier_id as $key => $supplier_id) {
            ProductSupplier::create([
                'product_id' => $id,
                'supplier_id' => $supplier_id,
                'price' => $request->supplier_price[$key]
            ]);
        }

        return redirect()->route('product.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && file_exists(public_path('images/product/' . $product->image))) {
            unlink(public_path('images/product/' . $product->image));
        }

        ProductSupplier::where('product_id', $id)->delete();
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully');
    }
}
