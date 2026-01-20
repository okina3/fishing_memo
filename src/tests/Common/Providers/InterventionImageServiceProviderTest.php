<?php

namespace Tests\Common\Providers;

use App\Providers\InterventionImageServiceProvider;
use Illuminate\Support\Facades\App as AppFacade;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image as ImageFacade;
use Tests\Common\TestCase;

class InterventionImageServiceProviderTest extends TestCase
{
    // ImageManager のシングルトン登録と GD ドライバの利用を検証
    public function testRegistersImageManagerSingleton(): void
    {
        // Providerの準備
        $provider = new InterventionImageServiceProvider(AppFacade::getFacadeRoot());

        // コンテナへバインドする処理を実行
        $provider->register();

        // コンテナから ImageManager を2回取得
        $first = app(ImageManager::class);
        $second = app(ImageManager::class);

        // 取得したオブジェクトが期待する型（ImageManager）であること
        $this->assertInstanceOf(ImageManager::class, $first);

        // 同一インスタンスであることを検証（シングルトン登録の確認）
        $this->assertSame($first, $second);

        // ドライバが GdDriver であることを検証
        $this->assertInstanceOf(GdDriver::class, $first->driver());
    }

    // ファサードエイリアス 'InterventionImage' が登録されることを検証
    public function testRegistersFacadeAlias(): void
    {
        // Providerの準備
        $provider = new InterventionImageServiceProvider(AppFacade::getFacadeRoot());

        // 先にクラスをロード
        class_exists(ImageFacade::class);

        // エイリアスの登録
        $provider->boot();

        // エイリアスが追加されている、または既に存在しているかの検証
        $this->assertTrue(class_exists('InterventionImage', false));

        // エイリアスは元の Facade クラスを指していることの検証
        $this->assertTrue(is_a('InterventionImage', ImageFacade::class, true));

        // コンテナへバインドする処理を実行
        $provider->register();

        // エイリアス経由で ImageManager を取得し、ドライバが GdDriver であることを検証
        $this->assertInstanceOf(GdDriver::class, \InterventionImage::driver());
    }
}
