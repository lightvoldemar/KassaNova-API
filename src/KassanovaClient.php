<?php
namespace lightvoldemar\kassanovaBankApi\Kassanova;

use yii\httpclient\Client;

/**
 * Клиентский класс.
 */
class KassanovaClient
{
    /**
     * Имя провайдера.
     */
    const providerName = 'kassanova';

    /**
     * URL редиректа.
     */
    public $dataRedirectUrl = "ERROR";

    /**
     * URL успешной транзакции.
     */
    public $returnUrl;

    /**
     * URL не успешной транзакции.
     */
    public $failUrl;

    /**
     * Текущая валюта.
     */
    public $currency;

    /**
     * Текущий язык.
     */
    public $language;

    /**
     * API логин.
     */
    public $apiLogin;

    /**
     * API пароль.
     */
    public $apiPassword;

    /**
     * URL регистрации заказа.
     */
    public $registerUrl = 'https://3ds.kassanova.kz/payment/rest/register.do';

    /**
     * Доступные валюты (ISO 4217).
     *
     * @var array
     */
    protected $currencyEnum = array(
        840 => 'USD',
        398 => 'KZT',
    );

    /**
     * Доступные языки.
     *
     * @var array
     */
    protected $languageList = array(
        'en' => 'en',
        'ru' => 'ru',
    );

    /**
     * Конфигурация.
     *
     * @var array
     */
    protected $config = array();

    /**
     * Конструктор.
     */
    public function __construct()
    {

    }

    /**
     * Возвращает ID валюты.
     *
     * @param  string $key
     * @return null|integer
     */
    public function getCurrencyId($key = 'KZT')
    {
        $types = array_flip($this->currencyEnum);

        return isset($types[$key]) ? $types[$key] : null;
    }

    /**
     * Задает указанный тип валюты.
     *
     * @param  string $key
     */
    public function setCurrency($key = 'KZT')
    {
        $types = array_flip($this->currencyEnum);

        $this->currency = isset($types[$key]) ? $types[$key] : null;
    }

    /**
     * Возвращает ID языка.
     *
     * @param  string $key
     * @return null|integer
     */
    public function getLangId($key = 'ru')
    {
        $types = array_flip($this->langList);

        return isset($types[$key]) ? $types[$key] : null;
    }

    /**
     * Задает указанный тип языка.
     *
     * @param  string $key
     */
    public function setLang($key = 'ru')
    {
        $types = array_flip($this->langList);

        $this->currency = isset($types[$key]) ? $types[$key] : null;
    }

    /**
     * Функция оплаты.
     *
     * @param int $amount сумма плетажа
     * @param int $orderId идентификатор ордера
     * @return boolean
     */
    public function pay($amount,$orderId) {
        // ID ордера
        $order['order_id'] = $orderId;
        $order['return_url'] = $this->returnUrl;
        $order['fail_url'] = $this->failUrl;
        $arr = $this->registerOrder($amount,$orderId);

        if(isset($arr['errorCode'])) {
            return false;
        } else {
            // Запись OrderId в БД
            $this->orderRecord($order['order_num'],$arr['orderId']);
            // Возврат URL
            $this->dataRedirectUrl = $arr['formUrl'];
            return true;
        }
    }

    /**
     * Регистрация заказа.
     *
     * @param int $amount сумма плетажа
     * @param int $orderId идентификатор ордера
     * @return object
     */
    private function registerOrder($amount,$orderId) {
        $data['amount'] = $amount."00";
        $data['currency'] = $this->currency;
        $data['language'] = $this->language;
        $data['orderNumber'] = $orderId;
        $data['userName'] = $this->apiLogin;
        $data['password'] = $this->apiPassword;
        $data['returnUrl'] = $this->returnUrl;
        $data['failUrl'] = $this->failUrl;

        return $this->sendRequest($this->registerUrl,$data);
    }

    /**
     * Отправка запроса.
     *
     * @param string $url адрес отправки запроса
     * @param array $data массив данных запроса
     * @return object
     */
    private function sendRequest($url,$data) {

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl($url)
            ->setData($data)
            ->send();

        if($response->isOk) {
            return $response;
        }
    }
  
}
