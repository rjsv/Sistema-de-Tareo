<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Class Holiday
 * @package App\Models
 */
class LeadFiles extends Model
{
    // Don't forget to fill this array
    protected $fillable = [];

    protected $guarded = ['id'];
    protected $table =  'lead_files';

    public function lead(){
        return $this->belongsTo(Lead::class);
    }
}