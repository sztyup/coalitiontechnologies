<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    /**
     * @param Request $request
     *
     * @param FilesystemManager $fsManager
     *
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function newProduct(Request $request, FilesystemManager $fsManager)
    {
        /** @var FilesystemAdapter $fs */
        $fs = $fsManager->disk();
        if (!$fs->has('products.json')) {
            $json = [];
            $fs->put('products.json', json_encode($json));
        } else {
            $json = json_decode($fs->get('products.json'), true);
        }

        $product = [
            'name' => $request->request->get('name'),
            'qty' => $request->request->get('qty'),
            'price' => $request->request->get('price'),
            'time' => Carbon::now()
        ];

        $json[Str::uuid()->toString()] = $product;

        $fs->put('products.json', json_encode($json));

        return JsonResponse::create();
    }

    /**
     * @param FilesystemManager $fsManager
     *
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function listProducts(FilesystemManager $fsManager)
    {
        /** @var FilesystemAdapter $fs */
        $fs = $fsManager->disk();
        if (!$fs->has('products.json')) {
            $json = [];
            $fs->put('products.json', json_encode($json));
        } else {
            $json = json_decode($fs->get('products.json'), true);
        }

        return JsonResponse::create($json);
    }

    /**
     * @param Request $request
     * @param string $product
     * @param FilesystemManager $fsManager
     *
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function editProduct(Request $request, string $product, FilesystemManager $fsManager)
    {
        /** @var FilesystemAdapter $fs */
        $fs = $fsManager->disk();
        if (!$fs->has('products.json')) {
            $json = [];
            $fs->put('products.json', json_encode($json));
        } else {
            $json = json_decode($fs->get('products.json'));
        }

        if (Arr::has($json, $product)) {
            $json[$product]['name'] = $request->request->get('name');
            $json[$product]['qty'] = $request->request->get('qty');
            $json[$product]['price'] = $request->request->get('price');
        }

        $fs->put('products.json', json_encode($json));

        return JsonResponse::create();
    }
}
