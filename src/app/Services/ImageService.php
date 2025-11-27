<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService
{
    /**
     * 別のユーザーの画像を見られなくする為のメソッド。
     * @param $request
     * @return void
     */
    public static function checkUserImage($request): void
    {
        // パラメーターを取得
        $id_image = $request->route()->parameter('image');
        // パラメーターが無ければチェック不要
        if (!is_null($id_image)) {
            // 自分自身の画像なのかチェック
            $image = Image::select('user_id')->findOrFail($id_image);
            if ($image->user_id !== Auth::id()) {
                abort(404);
            }
        }
    }

    /**
     * 画像を保存するメソッド。
     * @param string $filename
     * @return Image
     */
    public static function createImage(string $filename): Image
    {
        return Image::create([
            'user_id' => Auth::id(),
            'filename' => $filename,
        ]);
    }

    /**
     * 選択したメモに紐づいた画像を取得するメソッド。
     * @param Collection $select_memo_images
     * @return array
     */
    public static function getMemoImages(Collection $select_memo_images): array
    {
        return $select_memo_images->all();
    }

    /**
     * 選択したメモに紐づいた画像のidを取得するメソッド。
     * @param Collection $select_memo_images
     * @return array
     */
    public static function getMemoImagesId(Collection $select_memo_images): array
    {
        return $select_memo_images->pluck('id')->toArray();
    }

    /**
     * 画像をリサイズして、Laravelのフォルダ内に保存するメソッド。
     * @param UploadedFile $image_file
     * @param ImageManager $manager
     * @return string
     */
    public static function afterResizingImage(UploadedFile $image_file, ImageManager $manager): string
    {
        // ランダムなファイル名の生成
        $rnd_file_name = uniqid(rand() . '_');
        // ランダムなファイル名と拡張子を結合
        $only_one_file_name = $rnd_file_name . '.' . 'jpeg';
        // 画像の読み込み
        $image = $manager->read($image_file->getPathname());
        // 実際のリサイズ
        $resized_image = $image->resize(720, 480);
        // 画像をJPEG形式でエンコード
        $encoded_image = $resized_image->toJpeg();

        // 保存釣り場とファイル名を指定して、Laravel内に保存
        Storage::disk('public')->put($only_one_file_name, $encoded_image);

        return $only_one_file_name;
    }

    /**
     * Storageフォルダ内の画像ファイルを削除するメソッド。
     * @param string $image_filename
     * @return void
     */
    public static function deleteStorage(string $image_filename): void
    {
        // Storageフォルダ内の画像ファイルを削除
        if (Storage::disk('public')->exists($image_filename)) {
            Storage::disk('public')->delete($image_filename);
        }
    }
}
