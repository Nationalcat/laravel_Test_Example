<?php


namespace Tests\Unit\Services;

use App\Services\HomeService;
use App\Services\LoggerService;
use App\User;
use GuzzleHttp\Client;
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
     * @var \Mockery\MockInterface
     */
    private $mockClient;

    /**
     * 在測試類別中，setUp 像是 __construct，會在每次執行該 class 的測試案例時就執行一次。
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUp() : void
    {
        parent::setUp();
        // 如果需要 mock，必須放在受測類別的上面，因為等到類別被框架自動注入完，就蓋不過去了
        $this->mockClient        = $this->initMockery(Client::class);
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
        factory(User::class)->create(['name' => '邊緣人']);
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

    /**
     * 第三方服務並不是我們的測試目標，所以不用真的去打 API，如果需要該 API 的回傳值，只要 mock 時，在 andReturn() 放入假資料
     *
     * @test
     */
    public function 測試第三方服務()
    {
        // arrange
        $this->mockClient
            ->shouldReceive('get->getBody->getContents')
            ->andReturn(json_encode(['today' => '今天真好']));
        // act
        $act = $this->target->getWeather();
        // assert
        $this->assertEquals('今天真好', $act);
    }

    /**
     * mock 受測類別與檢測迴圈的傳入參數
     * 一般來說，不推薦 mock 受測類別，這會導致測試會被切的很碎，但有時候還是需要 mock 它們
     *
     * 執行下列指令，可只測試相同群組的測試案例
     * ./vendor/bin/phpunit --group=batchWritingLogs
     *
     * @test
     * @group batchWritingLogs
     *
     * @throws \ReflectionException
     */
    public function batchWritingLogs_is_work()
    {
        // arrange
        // 這邊要用 initPartialMockery 來 mock target，這樣就能同時 mock 與執行受測類別
        $this->target = $this->initPartialMockery(HomeService::class);
        /*
         * 接著，這裡需要做 1 或 3，或著其他更好的方法
         * 1. 不過 initPartialMockery 有個缺點是類別的屬性都會在 constructor 執行完後被清掉 (預設值不影響)，
         *    這時你需要使用 setObjectAttribute 重新注入
         * 2. phpunit 原生的 mock，對 mock 受測類別有更優雅的方式，但初期先學習 mockery 套件(因為這個很夯)
         * 3. 或著改在受測方法上用 app() 輔助方法來做 DI;
         */

        // 該方法預設會跑兩次迴圈，getCountry 執行結果順序如下變數。這邊你還需要 mock $this->target->getCountry()
        $countryByUserIdIsOne = '台灣';
        $countryByUserIdIsTWO = '美國';
        // 別忘了 $this->loggerService->save() 也需要 mock

        // act
        $events = [
            ['userId' => 1, 'content' => '新年快樂'],
            ['userId' => 2, 'content' => '早安'],
        ];
        $this->target->batchWritingLogs($events);
        // assert (因為回傳提示是 void 所以不判斷該方法的回傳值)
    }
}
