<?php

namespace App\Repositories\Back;

use App\{
    Models\Category,
    Helpers\ImageHelper
};

class CategoryRepository
{

    /**
     * Store category.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return void
     */

    public function store($request)
    {
        $input = $request->all();
        $input['photo'] = ImageHelper::handleUploadedImage($request->file('photo'), 'images');
        Category::create($input);
    }

    /**
     * Update category.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return void
     */

    public function update($category, $request)
    {
        $input = $request->all();
        if ($file = $request->file('photo')) {
            $input['photo'] = ImageHelper::handleUpdatedUploadedImage($file, 'images', $category, 'images', 'photo');
        }
        $category->update($input);
    }

    /**
     * Delete category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($category)
    {
        // Check if category has products
        if ($category->items()->count() > 0) {
            return ['message' => __('This category has products. Please delete all products in this category first, then delete the category.'), 'status' => 0];
        }

        // Delete category image and category
        ImageHelper::handleDeletedImage($category, 'photo', 'images');
        $category->delete();
        return ['message' => __('Category Deleted Successfully.'), 'status' => 1];
    }
}
