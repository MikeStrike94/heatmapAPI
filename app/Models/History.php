<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Type;
use Exception;
use Illuminate\Support\Facades\DB;

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
        'url_params'
    ];

    private static $data;

    /** 
     * Load Request data
     */
    public static function loadData($data)
    {
        if (!isset($data)) {
            return false;
        }
        self::$data = $data;
    }


    /**
     * Save user history data
     * @param array $data
     */
    public static function storeData()
    {
        if (!self::validateData()) {
            return false;
        }

        $preparedData = self::prepareData();

        try {
            self::create($preparedData);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Validate request data
     * @return bool
     */
    private static function validateData()
    {
        $data = self::$data;
        if (!isset($data)) {
            return false;
        }

        $fieldsToValidate = array('url', 'type', 'customer');
        foreach ($fieldsToValidate as $field) {
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
    private static function prepareData()
    {
        $data = self::$data;

        $fullUrl = $data['url'];
        $urlParts = explode('?', $fullUrl);

        $preparedData['full_url'] = $fullUrl;
        $preparedData['url'] = (isset($urlParts[0])) ? $urlParts[0] : null;
        $preparedData['url_params'] = (isset($urlParts[1])) ? $urlParts[1] : null;

        $preparedData['type_id'] = Type::where('name', $data['type'])->first()->id;
        $preparedData['customer_id'] = $data['customer'];

        return $preparedData;
    }

    /**
     * Count link hits
     * @param string $from 
     * @param string $to
     * @param string $link
     * @return int  
     */
    public static function countLinkHits($from = '', $to = '', $link = '')
    {
        $query = DB::table('histories');

        // Date interval condition
        if (strlen($from) > 0 || strlen($to) > 0) {
            $query->whereBetween('histories.created_at', [$from, $to]);
        }
        $result = $query->where('full_url', $link)
            ->get()
            ->count();

        return $result;
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     */
    public static function countTypeHits($from = '', $to = '')
    {
        $query = DB::table('histories');
        $query->selectRaw(
            'histories.type_id as type_id,
            types.name as type_name,
            count(histories.type_id) as count'
        );
        $query->leftJoin('types', 'types.id', '=', 'histories.type_id');

        // Date interval condition
        if (strlen($from) > 0 || strlen($to) > 0) {
            $query->whereBetween('histories.created_at', [$from, $to]);
        }

        $result = $query->whereBetween('histories.created_at', [$from, $to])
            ->groupBy('histories.type_id', 'types.name')
            ->get()
            ->toArray();

        return $result;
    }

    /**
     * List customer Journey
     * @param string $customer_id
     * @param string $from
     * @param string $to
     * @return array
     */
    public static function listCustomerJourney($customer_id, $from = '', $to = '')
    {

        $query = DB::table('histories');

        $query->selectRaw('url, created_at')
            ->where('customer_id', $customer_id);

        // Date interval condition
        if (strlen($from) > 0 || strlen($to) > 0) {
            $query->whereBetween('histories.created_at', [$from, $to]);
        }

        $result = $query->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        return $result;
    }

    /**
     * List customers with similar journey
     * A totally not good sollution, I know I should done only by SQL
     */
    public static function listCustomersWithSimilarJourney()
    {
        $query = DB::table('histories');
        $query->selectRaw('customer_id, GROUP_CONCAT(full_url) as full_url_list');
        $query->groupBy('customer_id');
        $query->orderBy('created_at', 'asc');
        $journeys = $query->get()->toArray();

        $urls = array();
        $foundJourneysUrls = array();
        foreach ($journeys as $journey) {
            if (in_array($journey->full_url_list, $urls)) {
                $foundJourneysUrls[] = $journey->full_url_list;
            }
            array_push($urls, $journey->full_url_list);
        }

        $foundJourneys = array();
        foreach ($foundJourneysUrls as $foundJourneysUrl) {
            foreach ($journeys as $journey) {
                if ($journey->full_url_list == $foundJourneysUrl) {
                    $foundJourneys[] = $journey;
                }
            }
        }

        return $foundJourneys;
    }
}
