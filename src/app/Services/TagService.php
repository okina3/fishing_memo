<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TagService
{
    /**
     * タグを保存するメソッド。
     * @param string $new_tag
     * @return Tag
     */
    public static function createTag(string $new_tag): Tag
    {
        return Tag::firstOrCreate([
            'name' => $new_tag,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * メモ画面の新規タグの保存・更新するメソッド。
     * @param $request_new_tag
     * @param int $memo_id
     * @return void
     */
    public static function createNewTag($request_new_tag, int $memo_id): void
    {
        if (!empty($request_new_tag)) {
            // タグを保存または取得
            $tag = self::createTag($request_new_tag);
            // メモとタグの中間テーブルに値を保存
            Tag::findOrFail($tag->id)->memos()->attach($memo_id);
        }
    }

    /**
     * 選択したメモに紐づいた、タグのIDを、配列で取得するメソッド。
     * @param Collection $select_memo_tags
     * @return array
     */
    public static function getMemoTagsId(Collection $select_memo_tags): array
    {
        return $select_memo_tags->pluck('id')->toArray();
    }

    /**
     * 選択したメモに紐づいた、タグのNameを、配列で取得するメソッド。
     * @param Collection $select_memo_tags
     * @return array
     */
    public static function getMemoTagsName(Collection $select_memo_tags): array
    {
        return $select_memo_tags->pluck('name')->toArray();
    }

    /**
     * タグを一括削除するメソッド。
     * @param array|
     * @return void
     */
    public static function deleteTags(array $tags): void
    {
        $tagIds = array_map('intval', (array) $tags);
        if (!empty($tagIds)) {
            Tag::whereIn('id', $tagIds)->delete();
        }
    }
}
