<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemoRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        // users ガードで認証されていることを確認する
        return $this->user('users') !== null;
    }

    /**
     * リクエストに対するバリデーションルールを定義するメソッド。
     * @return string[]
     */
    public function rules(): array
    {
        return [
            // 釣行日・時間・天気・気温・風向
            'fishing_date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',
            'weather'      => 'nullable|string|in:晴れ,曇り,雨,その他',
            'air_temp'     => 'nullable|integer|min:0|max:60',
            'wind_dir'     => 'nullable|string|in:北,北東,東,南東,南,南西,西,北西',
            // 釣り場
            'spot_areas' => 'array',
            'spot_areas.*.spot_id' => 'required|integer|exists:spots,id',
            'spot_areas.*.river_flow' => 'nullable|string|in:流れあり,流れなし',
            'spot_areas.*.turbidity' => 'nullable|string|in:クリア,濁り',
            'spot_areas.*.water_level' => 'nullable|numeric|min:0|max:999.9',
            'spot_areas.*.water_temp' => 'nullable|integer|min:0|max:99',
            // エサ
            'baits' => 'array',
            'baits.*' => 'nullable|integer|distinct|exists:baits,id',
            // 釣果入力
            'fishing_results' => 'array',
            'fishing_results.*.fish_name_id' => 'nullable|integer|exists:fish_names,id',
            'fishing_results.*.count' => 'nullable|required_with:fishing_results.*.fish_name_id|integer|min:0',
            'fishing_results.*.length' => 'nullable|required_with:fishing_results.*.fish_name_id|integer|min:0',
            // 備考
            'content'      => 'string|max:1000',
        ];
    }

    /**
     * バリデーションエラーメッセージを定義するメソッド。
     * @return string[]
     */
    public function messages(): array
    {
        return [
            // 釣行日・時間・天気・気温・風向
            'fishing_date.required' => '釣行日を指定してください。',
            'fishing_date.date' => '釣行日の形式が不正です。',
            'fishing_date.before_or_equal' => '釣行日は今日以前の日付を指定してください。',
            'start_time.required' => '開始時間を指定してください。',
            'start_time.date_format' => '開始時間の形式は HH:MM で指定してください。',
            'end_time.required' => '終了時間を指定してください。',
            'end_time.date_format' => '終了時間の形式は HH:MM で指定してください。',
            'end_time.after_or_equal' => '終了時間は開始時間以降を指定してください。',
            'spot_areas.array' => '釣り場データの形式が不正です。',
            'spot_areas.*.spot_id.integer' => '釣り場は整数で指定してください。',
            'spot_areas.*.spot_id.exists' => '選択された釣り場は存在しません。',
            'weather.in' => '天気の値が不正です。',
            'weather.string' => '天気は文字列で指定してください。',
            'air_temp.integer' => '気温は整数で指定してください。',
            'air_temp.min' => '気温は 0 以上で指定してください。',
            'air_temp.max' => '気温は 60 以下で指定してください。',
            'max_wind.integer' => '最大風速は整数で指定してください。',
            'wind_dir.in' => '風向の値が不正です。',
            // 釣り場
            'spot_areas.*.spot_id.required' => '釣り場を選択してください。また、マスターズ管理から釣り場を登録をしてから選択してください。',
            'spot_areas.*.river_flow.in' => '川の流れの値が不正です。',
            'spot_areas.*.turbidity.in' => '濁りの値が不正です。',
            'spot_areas.*.water_level.numeric' => '水位は数値で指定してください。',
            'spot_areas.*.water_level.min' => '水位は 0 以上で指定してください。',
            'spot_areas.*.water_level.max' => '水位は 999.9 以下で指定してください。',
            'spot_areas.*.water_temp.integer' => '水温は整数で指定してください。',
            'spot_areas.*.water_temp.min' => '水温は 0 以上で指定してください。',
            'spot_areas.*.water_temp.max' => '水温は 99 以下で指定してください。',
            // エサ
            'baits.array' => 'エサの形式が不正です。',
            'baits.*.integer' => 'エサの選択値が不正です。',
            'baits.*.distinct' => '同じエサが複数選択されています。',
            'baits.*.exists' => '選択されたエサは存在しません。',
            // 釣果入力
            'fishing_results.array' => '釣果データの形式が不正です。',
            'fishing_results.*.fish_name_id.integer' => '魚名の値が不正です。',
            'fishing_results.*.fish_name_id.exists' => '選択された魚名は存在しません。',
            'fishing_results.*.count.required_with' => '匹数も入力してください。',
            'fishing_results.*.count.integer' => '匹数は整数で指定してください。',
            'fishing_results.*.count.min' => '匹数は 0 以上で指定してください。',
            'fishing_results.*.length.required_with' => '長さも入力してください。',
            'fishing_results.*.length.integer' => '長さは整数で指定してください。',
            'fishing_results.*.length.min' => '長さは 0 以上で指定してください。',
            // 備考
            'content.string' => 'メモの備考が空です。また、文字列で指定してください。',
            'content.max' => '文字数は、1000文字以内にしてください。',
        ];
    }
}
