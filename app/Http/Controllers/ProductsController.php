<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    protected $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index()
    {
        try {

            DB::beginTransaction();

            $products = $this->product->with("categories.images")->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Berhasil di Tampilkan",
                "data" => $products
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $product = $this->product::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            foreach ($request->categories as $categoryData) {
                $category = $product->categories()->create([
                    'category_name' => $categoryData['name']
                ]);

                if (!empty($categoryData['image'])) {
                    foreach ($categoryData['image'] as $image) {
                        $path = $image->store('image', 'public');
                        $category->images()->create(['image' => $path]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Berhasil di Simpan"
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $product = $this->product->where("id", $id)->first();

            if (!$product) {
                return response()->json([
                    "status" => false,
                    "message" => "Produk tidak ditemukan"
                ]);
            }

            // Hapus semua kategori yang terkait dengan produk
            foreach ($product->categories as $category) {
                // Hapus gambar dari storage dan database
                foreach ($category->images as $image) {
                    $imagePath = storage_path("app/public/" . $image->image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath); // Hapus file dari storage
                    }
                    $image->delete(); // Hapus record dari database
                }
                $category->delete(); // Hapus kategori
            }

            $product->delete(); // Hapus produk

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data dan gambar berhasil dihapus"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
