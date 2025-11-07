<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\Item,
    Models\Attribute,
    Models\AttributeOption,
    Http\Controllers\Controller,
    Http\Requests\AttributeOptionRequest,
    Helpers\ImageHelper
};
use App\Models\Currency;
use App\Models\Gallery;
use Illuminate\Support\Facades\DB;

class AttributeOptionController extends Controller
{
    /**
     * Constructor Method.
     *
     * Setting Authentication
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Item $item)
    {

        return view('back.item.attribute_option.index', [
            'item'  => $item,
            'curr' => Currency::where('is_default', 1)->first(),
            'datas' => $item->join('attributes', 'attributes.item_id', '=', 'items.id')
                ->join('attribute_options', 'attribute_options.attribute_id', '=', 'attributes.id')
                ->select('attribute_options.id', 'attribute_options.attribute_id', 'attribute_options.name', 'attribute_options.keyword', 'attribute_options.stock', 'attribute_options.price', 'attribute_options.image', 'attribute_options.color_code', 'attribute_options.gallery_image_id', DB::raw('attributes.name as attribute'))
                ->where('items.id', '=', $item->id)
                ->latest('attribute_options.id')
                ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Item $item)
    {
        return view('back.item.attribute_option.create', [
            'item'  => $item,
            'curr' => Currency::where('is_default', 1)->first(),
            'attributes' => Attribute::whereItemId($item->id)->get(),
            'galleries' => Gallery::where('item_id', $item->id)->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeOptionRequest $request, Item $item)
    {

        $input = $request->all();
        $curr = Currency::where('is_default', 1)->first();
        $input['price'] = $request->price / $curr->value;

        // Handle image upload
        if ($file = $request->file('image')) {
            $input['image'] = ImageHelper::handleUploadedImage($file, 'images');
        }

        // Handle color code (can be empty)
        $input['color_code'] = $request->color_code ? $request->color_code : null;

        // Handle gallery_image_id (can be empty)
        $input['gallery_image_id'] = $request->gallery_image_id ? $request->gallery_image_id : null;

        AttributeOption::create($input);

        return redirect()->route('back.option.index', $item->id)->withSuccess(__('New Attribute Option Added Successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item, AttributeOption $option)
    {
        return view('back.item.attribute_option.edit', [
            'item'  => $item,
            'option' => $option,
            'curr' => Currency::where('is_default', 1)->first(),
            'attributes' => Attribute::whereItemId($item->id)->get(),
            'galleries' => Gallery::where('item_id', $item->id)->get()
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeOptionRequest $request, Item $item, AttributeOption $option)
    {

        $input = $request->all();
        $curr = Currency::where('is_default', 1)->first();
        $input['price'] = $request->price / $curr->value;

        // Handle image upload
        if ($file = $request->file('image')) {
            $input['image'] = ImageHelper::handleUpdatedUploadedImage($file, 'images', $option, 'images', 'image');
        }

        // Handle color code (can be empty)
        $input['color_code'] = $request->color_code ? $request->color_code : null;

        // Handle gallery_image_id (can be empty)
        $input['gallery_image_id'] = $request->gallery_image_id ? $request->gallery_image_id : null;

        $option->update($input);

        return redirect()->route('back.option.index', $item->id)->withSuccess(__('Attribute Option Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item, AttributeOption $option)
    {
        // Delete image if exists
        if ($option->image) {
            ImageHelper::handleDeletedImage($option, 'image', 'images');
        }
        $option->delete();
        return redirect()->route('back.option.index', $item->id)->withSuccess(__('Attribute Option Deleted Successfully.'));
    }
}
