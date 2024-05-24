<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cases extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'cases';
    protected $fillable = [
        'court',
        'highcourt',
        'bench',
        'casetype',
        'casenumber',
        'diarybumber',
        'year',
        'case_number',
        'filing_date',
        'filing_date',
        'floor',
        'title',
        'description',
        'before_judges',
        'referred_by',
        'section',
        'priority',
        'under_acts',
        'under_sections',
        'FIR_police_station',
        'FIR_number',
        'FIR_year',
        'your_advocates',
        'your_team',
        'opponents',
        'opponent_advocates',
    ];

    public static function getCasesById($id){

        $cases = Cases::whereIn('id',explode(',',$id))->pluck('name')->toArray();
        return implode(',',$cases);
    }

    public static function caseType(){
        $types = [
            'Arbitration Petition' => 'Arbitration Petition',
            'Civil Appeal' => 'Civil Appeal',
            'Contempt Petition (Civil)' => 'Contempt Petition (Civil)',
            'Contempt Petition (Criminal)' => 'Contempt Petition (Criminal)',
            'Criminal Appeal' => 'Criminal Appeal',
            'Curative Petition(Civil)' => 'Curative Petition(Civil)',
            'Curative Petition(Criminal)' => 'Curative Petition(Criminal)',
            'DEATH REFERENCE CASE' => 'DEATH REFERENCE CASE',
            'DIARY NO.' => 'DIARY NO.',
            'DIARYNO AND DIARYYR' => 'DIARYNO AND DIARYYR',
            'Election Petition (Civil)' => 'Election Petition (Civil)',
            'FILE NUMBER' => 'FILE NUMBER',
            'MISCELLANEOUS APPLICATION' => 'MISCELLANEOUS APPLICATION',
            'Motion Case(Crl.)' => 'Motion Case(Crl.)',
            'Original Suit' => 'Original Suit',
            'REF. U/A 317(1)' => 'REF. U/A 317(1)',
            'REF. U/S 14 RTI' => 'REF. U/S 14 RTI',
            'REF. U/S 143' => 'REF. U/S 143',
            'REF. U/S 17 RTI' => 'REF. U/S 17 RTI',
            'Review Petition (Civil)' => 'Review Petition (Civil)',
            'Review Petition (Criminal)' => 'Review Petition (Criminal)',
            'SLP (Civil)' => 'SLP (Civil)',
            'SLP (Criminal)' => 'SLP (Criminal)',
            'SPECIAL LEAVE TO PETITION (CIVIL)' => 'SPECIAL LEAVE TO PETITION (CIVIL)',
            'SPECIAL LEAVE TO PETITION (CRIMINAL)' => 'SPECIAL LEAVE TO PETITION (CRIMINAL)',
            'Special Reference Case' => 'Special Reference Case',
            'Suo-Moto Contempt Pet.(Civil) D' => 'Suo-Moto Contempt Pet.(Civil) D',
            'Suo-Moto Contempt Pet.(Criminal) D' => 'Suo-Moto Contempt Pet.(Criminal) D',
            'Suo-Moto W.P(Civil) D' => 'Suo-Moto W.P(Civil) D',
            'Suo-Moto W.P(Criminal) D' => 'Suo-Moto W.P(Criminal) D',
            'Tax Reference Case' => 'Tax Reference Case',
            'Tranfer Case (Civil)' => 'Tranfer Case (Civil)',
            'Transfer Case (Criminal)' => 'Transfer Case (Criminal)',
            'Transfer Petition (Civil)' => 'Transfer Petition (Civil)',
            'Transfer Petition (Criminal)' => 'Transfer Petition (Criminal)',
            'Writ Petition (Civil)' => 'Writ Petition (Civil)',
            'Writ Petition(Criminal)' => 'Writ Petition(Criminal)',
            'WRIT TO PETITION (CIVIL)' => 'WRIT TO PETITION (CIVIL)',
            'WRIT TO PETITION (CRIMINAL)' => 'WRIT TO PETITION (CRIMINAL)',
        ];
        return $types;
    }

    public static function casePriority(){
        return [
            'Super Critical' => 'Super Critical',
            'Critical' => 'Critical',
            'Important' => 'Important',
            'Routine' => 'Routine',
            'Normal' => 'Normal',
        ];
    }

    public function taskData()
    {
        return $this->hasMany(TaskData::class);
    }

    public function fallSlip()
    {
        return $this->hasOne(CasesSlipFall::class, 'case_id', 'id');
    }
    
}
