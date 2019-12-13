<?php


namespace Tests\Unit\Services;

use App\Services\HomeService;
use App\Services\LoggerService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeServiceTest extends TestCase
{
    /**
     * 這個 trait 只適合用在 sqlite 的 memory DB，這個專案我已經設定好了，在 phpunit.xml 裡
     * 但要記住，一定要確定沒有 cache 設定檔或執行 php artisan config:clear，因為設定檔一旦被快取著，就會執行 .env 的值
     */
    use RefreshDatabase;
    /**
     * @var HomeService
     */
    private $target;
    /**
     * @var \Mockery\MockInterface
     */
    private $mockLoggerService;

    /**
     * 在測試類別中，setUp 像是 __construct，會在每次執行該 class 的測試案例時就執行一次。
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUp() : void
    {
        parent::setUp();
        // 如果需要 mock，必須放在受測類別的上面，因為等到類別被框架自動注入完，就蓋不過去了
        $this->mockLoggerService = $this->initMockery(LoggerService::class);
        // 定義 target 為該檔案主要測試的目標
        $this->target = $this->app->make(HomeService::class);
        // 也有一種情況會把 mock 放在下面，就是使用 makePartial()，但這個之後再說。
    }

    /**
     * tearDown 會在該 class 的測試案例結束時執行，通常用來清掉當前測試案例的設定，避免污染下一個測試。
     * 但 laravel 已經幫你處理好了，所以我通常也不會覆寫這個方法。
     *
     * @throws \Throwable
     */
    protected function tearDown() : void
    {
        parent::tearDown();
    }

    /**
     * 在這邊輸入 @test 或在測試方法開頭輸入 test，都能被 phpunit 偵測到
     *
     * @test
     */
    public function 測試名稱可以是中文() : void
    {
        $this->assertTrue(true);
    }

    /**
     * 因為 mock loggerService 的 save 方法，所以不會真的執行裡面的內容，不然每次測試都等五秒還得了 XD
     *
     * @test
     */
    public function 使用_mock_方式測試_getUserName_方法()
    {
        // arrange
        factory(User::class)->create(['name'=>'邊緣人']);
        // mock 『find』 方法
        $this->mockLoggerService->shouldReceive('save')
            // 傳入 1 這個值
            ->with(1)
            // 預期會執行一次
            ->once();
        // act
        $act = $this->target->getUserName(1);
        // assert
        $this->assertEquals($act, '邊緣人');
    }
}
