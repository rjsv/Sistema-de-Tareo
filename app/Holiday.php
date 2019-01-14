<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Class Holiday
 * @package App\Models
 */
class Holiday extends Model
{
    // Don't forget to fill this array
    protected $fillable = [];

    protected $guarded = ['id'];
    protected $dates = ['date'];

    public static function getHolidayByDates($startDate, $endDate){

        return Holiday::Where('date', '>=', $startDate)->where('date', '<=', $endDate)->get();
    }

    public static function checkHolidayByDate($date){
        return Holiday::Where('date', $date)->first();
    }
}