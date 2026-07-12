<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentStatusHistory extends Model
{

protected $table = 'document_status_history';
    public $timestamps = false;
    protected $fillable = ['document_request_id', 'status', 'changed_at'];

    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }
}