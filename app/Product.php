<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = ['name', 'price', 'tax_id'];
    protected $appends = ['total_amount'];


    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function getTotalAmountAttribute(){

        if(!is_null($this->price) && !is_null($this->tax)){
            return $this->price + ($this->price * ($this->tax->rate_percent/100));
        }

        return "";
    }
}
