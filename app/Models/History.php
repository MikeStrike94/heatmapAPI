<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Type;

class History extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_id',
        'customer_id',
        'full_url',
        'url',
        'url_index'
    ];

    private static $data;
    private const MY_BASE_URL = 'www.myStore.ro';

    /** 
     * Load Request data
     */
    public static function loadData($data) {
        if (!isset($data)) {
            return false;
        }
        self::$data = $data;
    }


    /**
     * Save user history data
     * @param array $data
     */
    public static function storeData() {
        if (!self::validateData()) {
            return false;
        }
           
        $preparedData = self::prepareData();
        self::create($preparedData);
    }

    /**
     * Validate request data
     * @return bool
     */
    private static function validateData() {
        $data = self::$data;
        if (!isset($data)) {
            return false;
        }

        $fieldsToValidate = array('url', 'type', 'customer');
        foreach($fieldsToValidate as $field) {
            if (!isset($data[$field]) || strlen($data[$field]) < 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Prepare data for inserting in db
     * @return array $data
     */
    private static function prepareData() {
        $data = self::$data;

        $fullUrl = $data['url'];
        $baseUrl = 'https://' . self::MY_BASE_URL . '/';
        $baseUrlParts = explode($baseUrl, $fullUrl);
        $urlParts = explode('/', $baseUrlParts[1]);

        $preparedData['full_url'] = $fullUrl;
        $preparedData['url'] = (isset($urlParts[0])) ? $urlParts[0] : null;
        $preparedData['url_index'] = (isset($urlParts[1])) ? $urlParts[1] : null;

        $preparedData['type_id'] = Type::where('name', $data['type'])->first()->id;
        $preparedData['customer_id'] = $data['customer'];

        return $preparedData;
    }
}
